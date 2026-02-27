FROM php:8.2-cli

RUN apt-get update && apt-get install -y libsqlite3-dev libcurl4-openssl-dev && \
    docker-php-ext-install pdo pdo_sqlite curl && \
    apt-get clean && rm -rf /var/lib/apt/lists/*

WORKDIR /project
COPY . .

RUN mkdir -p /project/app/storage && chmod 777 /project/app/storage

EXPOSE ${PORT:-8080}

CMD php -S 0.0.0.0:${PORT:-8080} -t public public/index.php
