# Instructions

## Requirements

-   Installed PHP version >= 8.1
-   Created [payOS](https://my.payos.vn) payment channel

## Run project

Create an `.env` file by cloning the `.env.example` file. Fill in the `PAYOS_CLIENT_ID`, `PAYOS_API_KEY`, `PAYOS_CHECKSUM_KEY` fields with the payment channel information you created on the [payOS](https://my.payos.vn).

Install dependencies by command:

```sh
php ./composer.phar i
```

Before run the project, you need to generate the application key by running the following command:

```sh
php artisan key:generate
```

Run this command:

```sh
php artisan serve
```

Visit http://localhost:8000 to access the demo page using payOS payment method implemented in PHP.

In addition to the demo page using payOS payment method, we also implemented some API to call from client and webhook to process data received from payOS.

APIs are implemented in the `app/Http/Controllers/OrderController.php` file. Webhook is implemented in the `app/Http/Controllers/PaymentController.php` file. Routes of APIs and Webhook are declared in the `routes/web.php` file.

## References

https://stackoverflow.com/questions/44839648/no-application-encryption-key-has-been-specified
