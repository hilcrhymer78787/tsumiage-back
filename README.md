git clone https://github.com/hilcrhymer78787/tsumiage-back.git 

cd tsumiage-back

docker-compose up -d --build

docker-compose exec app bash

cp .env.example .env

composer install

php artisan key:generate

php artisan migrate:refresh --seed