<?php

class PaymentController
{
    public static function pixCreate(): void
    {
        $input = json_decode(file_get_contents('php://input'), true);
        $code = trim($input['code'] ?? '');

        if ($code === '') {
            jsonResponse(['error' => 'Código da reserva é obrigatório'], 400);
        }

        $reservation = Reservation::findByCode($code);
        if (!$reservation) {
            jsonResponse(['error' => 'Reserva não encontrada'], 404);
        }

        if ($reservation['status'] === 'paid') {
            jsonResponse(['error' => 'Esta reserva já foi paga'], 400);
        }

        // Rate limit: if there's already a pending pix transaction, reuse it
        if (!empty($reservation['pix_transaction_id']) && $reservation['pix_status'] === 'PENDING') {
            $status = self::checkPixApi($reservation['pix_transaction_id']);
            if ($status && ($status['status'] ?? '') === 'PENDING') {
                $discount = (int)env('PIX_DISCOUNT', '30');
                $discounted = round((float)$reservation['rate_total'] * (1 - $discount / 100), 2);
                jsonResponse([
                    'success'        => true,
                    'transaction_id' => $reservation['pix_transaction_id'],
                    'qr_code'        => $status['qrCode'] ?? '',
                    'qr_code_base64' => $status['qrCodeBase64'] ?? '',
                    'amount'         => $discounted,
                    'original_amount'=> (float)$reservation['rate_total'],
                    'expires_at'     => $status['expiresAt'] ?? '',
                ]);
            }
        }

        $discount = (int)env('PIX_DISCOUNT', '30');
        $original = (float)$reservation['rate_total'];
        $discounted = round($original * (1 - $discount / 100), 2);

        $payload = [
            'amount'      => $discounted,
            'externalId'  => 'res_' . $reservation['code'],
            'description' => 'Reserva ' . $reservation['code'] . ' - ' . env('HOTEL_NAME'),
        ];

        $response = self::callPixApi('POST', $payload);

        if (!$response || !isset($response['id'])) {
            jsonResponse(['error' => 'Erro ao gerar pagamento Pix. Tente novamente.'], 502);
        }

        Reservation::updateByCode($code, [
            'pix_transaction_id' => $response['id'],
            'pix_status'         => $response['status'] ?? 'PENDING',
        ]);

        jsonResponse([
            'success'         => true,
            'transaction_id'  => $response['id'],
            'qr_code'         => $response['qrCode'] ?? '',
            'qr_code_base64'  => $response['qrCodeBase64'] ?? '',
            'amount'          => $discounted,
            'original_amount' => $original,
            'expires_at'      => $response['expiresAt'] ?? '',
        ], 201);
    }

    public static function pixStatus(): void
    {
        $code = trim($_GET['code'] ?? '');
        if ($code === '') {
            jsonResponse(['error' => 'Código obrigatório'], 400);
        }

        $reservation = Reservation::findByCode($code);
        if (!$reservation) {
            jsonResponse(['error' => 'Reserva não encontrada'], 404);
        }

        if ($reservation['status'] === 'paid') {
            jsonResponse(['status' => 'PAID', 'reservation_status' => 'paid']);
        }

        if (empty($reservation['pix_transaction_id'])) {
            jsonResponse(['status' => 'NO_TRANSACTION', 'reservation_status' => $reservation['status']]);
        }

        $statusData = self::checkPixApi($reservation['pix_transaction_id']);
        $pixStatus = $statusData['status'] ?? 'UNKNOWN';

        if ($pixStatus === 'PAID') {
            self::markAsPaid($reservation);
        } elseif (in_array($pixStatus, ['EXPIRED', 'REJECTED'], true)) {
            Reservation::updateByCode($code, ['pix_status' => $pixStatus]);
        }

        jsonResponse([
            'status'             => $pixStatus,
            'reservation_status' => $pixStatus === 'PAID' ? 'paid' : $reservation['status'],
        ]);
    }

    public static function pixWebhook(): void
    {
        $input = json_decode(file_get_contents('php://input'), true);

        if (!$input || !isset($input['data']['id'])) {
            jsonResponse(['error' => 'Invalid payload'], 400);
        }

        $event = $input['event'] ?? '';
        $txId = $input['data']['id'];
        $txStatus = $input['data']['status'] ?? '';

        if ($event === 'payment.confirmed' && $txStatus === 'PAID') {
            $reservation = Reservation::findByPixTransactionId($txId);
            if ($reservation && $reservation['status'] !== 'paid') {
                self::markAsPaid($reservation);
            }
        }

        jsonResponse(['ok' => true]);
    }

    // ── Private ──

    private static function markAsPaid(array $reservation): void
    {
        $discount = (int)env('PIX_DISCOUNT', '30');
        $paidAmount = round((float)$reservation['rate_total'] * (1 - $discount / 100), 2);

        Reservation::updateByCode($reservation['code'], [
            'status'         => 'paid',
            'pix_status'     => 'PAID',
            'payment_method' => 'pix',
            'paid_amount'    => $paidAmount,
            'paid_at'        => date('Y-m-d H:i:s'),
        ]);
    }

    private static function callPixApi(string $method, ?array $payload = null): ?array
    {
        $url = env('PIX_API_URL');
        if (!$url) return null;

        $ch = curl_init($url);
        $opts = [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
                'X-Public-Key: ' . env('PIX_PUBLIC_KEY'),
                'X-Api-Key: ' . env('PIX_API_KEY'),
            ],
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_SSL_VERIFYPEER => true,
        ];

        if ($method === 'POST' && $payload) {
            $opts[CURLOPT_POST] = true;
            $opts[CURLOPT_POSTFIELDS] = json_encode($payload);
        }

        curl_setopt_array($ch, $opts);
        $resp = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($code < 200 || $code >= 300) return null;
        return json_decode($resp, true) ?: null;
    }

    private static function checkPixApi(string $transactionId): ?array
    {
        $url = env('PIX_API_URL') . '?id=' . urlencode($transactionId);

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => [
                'X-Public-Key: ' . env('PIX_PUBLIC_KEY'),
                'X-Api-Key: ' . env('PIX_API_KEY'),
            ],
            CURLOPT_TIMEOUT        => 15,
            CURLOPT_SSL_VERIFYPEER => true,
        ]);

        $resp = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($code !== 200) return null;
        return json_decode($resp, true) ?: null;
    }
}
