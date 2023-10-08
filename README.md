## Installation
- git clone https://github.com/MarwanMohamed/burger-restaurant.git
- cp .env.example .env
- configure .env file and add DB credentials and add the merchant email to send notification to him
- composer install
- php artisan migrate
- php artisan add-products to import products or php artisan db:seed
- php artisan key:generate
- php artisan serve

## To Run Tests

- php artisan test
