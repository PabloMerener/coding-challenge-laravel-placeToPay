# placeToPlay

## Requirements

<a href="/Requerimientos.pdf">Documentation</a>

Docker


## Instalation


- Clone

```sh
git clone https://github.com/PabloMerener/placeToPay.git && cd placeToPay
```
- Setting environment variables

```sh
cp .env.example .env
```

Then update the following variables (.env) : PLACETOPAY_LOGIN & PLACETOPAY_SECRET_KEY

- Composer
```sh
composer install
```

- Sail

```sh
vendor/bin/sail up -d

vendor/bin/sail artisan key:generate

vendor/bin/sail artisan migrate --seed

vendor/bin/sail artisan serve
```

- npm

```sh
npm install && npm run dev
```

Finaly go to http://localhost:8080/

You can use the following users:

- charles@chaplin.com (password: 12345678)
- admin@admin.com (password: 12345678)
