# Burger Restaurant

Small Restaurant is a Laravel-based web application that allows users to place orders for burgers only for now.

## Installation

To install Burger Restaurant, follow these steps:

1. Clone the repository:

        git clone https://github.com/MarwanMohamed/burger-restaurant.git

2. Change to the project directory:

         cd burger-restaurant

3. Copy the `.env.example` file to `.env`:

         cp .env.example .env

4. Configure the `.env` file by adding your database credentials and merchant email.

5. Install the required dependencies using Composer:

          composer install

6. Run the database migrations:

         php artisan migrate

7. Import products from a CSV file located in `public/Products.csv`. Move it to storage first

         mv public/Products.csv storage/app/public
         php artisan add-products 

 -you also can use the seed command to seed products to the database:
 -php artisan migrate


9. Generate an application key:

        php artisan key:generate

10. Start the Laravel development server:

         php artisan serve

11. To place an order, send a POST request to `http://127.0.0.1:8000/api/orders` with the following JSON payload:

``` json
{
    "products": [  
        {
            "product_id": 1, 
            "quantity": 2 
        } 
    ]  
}  

```

## Run Tests

To run the tests for this application, use the following command:

- php artisan test

[For Developers code structure and functionality](./READMEFORDEVELOPERS.md)
