<?php

class ReservationController
{
    public static function show(string $code): void
    {
        $reservation = Reservation::findByCode($code);

        if (!$reservation) {
            http_response_code(404);
            require APP_PATH . '/src/views/public/not_found.php';
            return;
        }

        $hotelName = env('HOTEL_NAME', 'Windsor Plaza');
        $hotelPhone = env('HOTEL_PHONE', '');
        $hotelEmail = env('HOTEL_EMAIL', '');
        $pixDiscount = (int)env('PIX_DISCOUNT', '30');
        $cardButtonActive = Setting::get('card_button_active', '0') === '1';
        $expiryHours = (int)env('PAYMENT_EXPIRY_HOURS', '48');

        $originalValue = (float)$reservation['rate_total'];
        $discountedValue = round($originalValue * (1 - $pixDiscount / 100), 2);
        $savings = round($originalValue - $discountedValue, 2);

        $checkinDate = new DateTime($reservation['checkin']);
        $checkoutDate = new DateTime($reservation['checkout']);
        $nights = $checkinDate->diff($checkoutDate)->days;

        $paidToday = Reservation::countPaidToday();
        $socialProofCount = max(12, $paidToday + 12);

        require APP_PATH . '/src/views/public/reservation.php';
    }
}
