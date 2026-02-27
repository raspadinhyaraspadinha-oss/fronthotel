# Windsor Plaza — Recuperação de Pagamentos

Sistema para recuperação de reservas com pagamento pendente via Pix.

## Estrutura do Projeto

```
FrontHotel/
├── public/                  ← Document root (= public_html na Hostinger)
│   ├── index.php            ← Front controller (router)
│   ├── .htaccess            ← Rewrite + segurança
│   └── assets/
│       ├── css/design-system.css
│       └── img/logo.png     ← Logo do hotel
│
├── app/                     ← Lógica (FORA do document root = seguro)
│   ├── .env                 ← Credenciais (nunca público)
│   ├── .env.example
│   ├── config/
│   │   ├── bootstrap.php
│   │   ├── env.php
│   │   └── database.php
│   ├── src/
│   │   ├── controllers/
│   │   ├── models/
│   │   └── views/
│   └── storage/
│       └── database.sqlite  ← Criado automaticamente
│
├── Dockerfile
├── sample.csv               ← CSV de exemplo
└── README.md
```

---

## Deploy na Hostinger (Passo a Passo)

### Opção A: Via Gerenciador de Arquivos (mais fácil)

#### 1. Acesse o hPanel da Hostinger
- Entre em https://hpanel.hostinger.com
- Selecione seu plano de hospedagem
- Clique em **Gerenciador de Arquivos**

#### 2. Suba a pasta `app/`
- Navegue até `/home/usuario/` (a raiz, NÃO dentro de public_html)
- Crie a pasta `app` clicando em **Nova Pasta**
- Entre na pasta `app` e faça upload de TODOS os arquivos e subpastas:
  ```
  /home/usuario/app/
  ├── .env
  ├── config/
  ├── src/
  └── storage/     ← crie vazia, o sistema cria o .sqlite sozinho
  ```

#### 3. Suba o conteúdo de `public/` para `public_html/`
- Navegue até `/home/usuario/public_html/`
- **Apague** o conteúdo padrão (index.html, etc.)
- Faça upload de TODOS os arquivos de dentro de `public/`:
  ```
  /home/usuario/public_html/
  ├── index.php
  ├── .htaccess
  └── assets/
      ├── css/design-system.css
      └── img/logo.png
  ```

#### 4. Configure o `.env`
- Navegue até `/home/usuario/app/.env`
- Clique para editar e preencha:
  ```env
  ADMIN_PASSWORD=SuaSenhaForteAqui

  PIX_API_URL=https://zlevsmbkjcfcanxrqzhb.supabase.co/functions/v1/public-api
  PIX_PUBLIC_KEY=pk_SuaChaveReal
  PIX_API_KEY=sk_SuaChaveReal

  PIX_DISCOUNT=30

  HOTEL_NAME=Windsor Plaza
  HOTEL_PHONE=(21) 2195-5000
  HOTEL_EMAIL=reservas@windsorplaza.com.br

  SITE_URL=https://seudominio.com.br

  CARD_BUTTON_ACTIVE=0
  PAYMENT_EXPIRY_HOURS=48
  ```

#### 5. Configure as permissões
- Pasta `app/storage/` → permissão **755** ou **775**
- Arquivo `app/.env` → permissão **644**

#### 6. Configure o PHP
- No hPanel, vá em **Avançado > Configuração PHP**
- Selecione **PHP 8.1** ou **8.2**
- Verifique se estas extensões estão ativas:
  - `pdo_sqlite`
  - `curl`
  - `mbstring`

#### 7. Ative o SSL
- No hPanel, vá em **Segurança > SSL**
- Ative o SSL gratuito (Let's Encrypt)
- Depois, edite `public_html/.htaccess` e descomente as 2 linhas de HTTPS:
  ```apache
  RewriteCond %{HTTPS} off
  RewriteRule ^ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
  ```

#### 8. Teste
- Acesse `https://seudominio.com.br/admin`
- Faça login com a senha do `.env`
- Importe o CSV de reservas
- Copie um link e teste em aba anônima

---

### Opção B: Via Git

#### 1. Crie o repositório
```bash
cd FrontHotel
git init
git add .
git commit -m "Initial commit"
git remote add origin https://github.com/seuusuario/seurepositorio.git
git push -u origin main
```

#### 2. Conecte na Hostinger
- No hPanel, vá em **Avançado > Git**
- Cole a URL do repositório e faça o deploy
- Ou use SSH para clonar manualmente:
  ```bash
  # Via SSH no terminal da Hostinger:
  cd /home/usuario
  git clone https://github.com/seuusuario/seurepositorio.git temp_repo

  # Mover os arquivos para os lugares certos:
  cp -r temp_repo/app /home/usuario/app
  cp -r temp_repo/public/* /home/usuario/public_html/
  rm -rf temp_repo
  ```

#### 3. Configure `.env` e permissões (mesmos passos 4-7 da Opção A)

---

### Resultado Final na Hostinger

```
/home/usuario/
├── app/                    ← Lógica (invisível da internet)
│   ├── .env                ← Suas credenciais seguras
│   ├── config/
│   ├── src/
│   └── storage/
│       └── database.sqlite ← Banco de dados
│
└── public_html/            ← Document root (o que a internet acessa)
    ├── index.php
    ├── .htaccess
    └── assets/
```

**Segurança**: a pasta `app/` fica FORA do `public_html`, logo é **impossível** acessar `.env`, banco de dados ou código PHP pelo navegador.

---

## Rodar Localmente

```bash
# Requisitos: PHP 8.1+ com pdo_sqlite, curl, mbstring

# Clonar e configurar
cp app/.env.example app/.env
# Edite app/.env com suas credenciais

# Coloque a logo em public/assets/img/logo.png

# Iniciar servidor
php -S localhost:8080 -t public public/index.php

# Acessar
# http://localhost:8080/admin
```

---

## CSV Esperado (formato Booking.com)

```csv
Confirmation Number;Name;Arrival;Departure;Email;Phone;Payment Method;Card Last 4;Card Holder;Payee;Rate Total (Estimated)
333894593;Alves, Isabella;05/03/2026;08/03/2026;email@guest.booking.com;+5511947257030;MC;5886;Payment Instructions;;2,029.41 BRL
```

O sistema detecta automaticamente: separador (`;` ou `,`), formato de data, formato BR de valor, nomes invertidos.

---

## Rotas

| Rota | Método | Descrição |
|---|---|---|
| `/admin` | GET | Painel administrativo |
| `/admin/login` | GET/POST | Login |
| `/admin/upload` | POST | Upload de CSV |
| `/admin/toggle-card` | POST | Ativa/desativa botão cartão |
| `/admin/export` | GET | Exporta CSV |
| `/admin/delete-all` | POST | Remove todas as reservas |
| `/r/{CODE}` | GET | Página pública da reserva |
| `/api/pix/create` | POST | Cria pagamento Pix |
| `/api/pix/status` | GET | Consulta status pagamento |
| `/api/pix/webhook` | POST | Webhook de confirmação |

---

## Troubleshooting

| Problema | Solução |
|---|---|
| Erro 500 | Verifique se PHP 8.1+ está ativo e extensões `pdo_sqlite` e `curl` habilitadas |
| Erro 403 | Verifique permissões: `storage/` precisa ser 755 |
| Página em branco | Ative `display_errors` temporariamente no `.htaccess`: `php_flag display_errors on` |
| CSV não importa | Verifique encoding (UTF-8) e se as colunas Name, Arrival, Departure existem |
| Pix não gera QR | Confira `PIX_PUBLIC_KEY` e `PIX_API_KEY` no `.env` |
| Links copiados errados | Ajuste `SITE_URL` no `.env` para a URL real do seu domínio |
