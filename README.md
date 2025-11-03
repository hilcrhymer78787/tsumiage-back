git clone https://github.com/hilcrhymer78787/tsumiage-back.git 

cd tsumiage-back

docker compose up -d --build

docker compose exec app bash

cp .env.example .env

composer install

php artisan key:generate

php artisan migrate:refresh --seed

php artisan migrate --env=testing


【キャッシュクリア】
composer dump-autoload && php artisan cache:clear && php artisan config:clear && php artisan route:clear && php artisan view:clear

【format】
./vendor/bin/pint

【test】
php artisan test

【myslq】
docker compose exec mysql bash
mysql -u root -ppassword -h mysql -P 3306

【レディスのワーカーを起動させる】
docker compose exec app bash
php artisan queue:work redis --queue=default