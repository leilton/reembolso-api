# reembolso-api
API de reembolso para submissão de teste da empresa Trand Up

INSTALL

`composer install`

after

`php artisan key:generate`

create database and configure your database in file .env

`DB_DATABASE=refounds
DB_USERNAME=root
DB_PASSWORD=estatica`

after config .env file, run

`php artisan migrate`

install passport

`php artisan passport:install`

run api

`php artisan serve`

Here <a href="https://github.com/leilton/reembolso-api/blob/master/public/Reembolso-API.postman_collection.json">Posman File</a>
