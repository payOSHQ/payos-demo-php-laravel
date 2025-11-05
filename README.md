# payOS Demo Laravel Application

This is a demo Laravel application integrate with payOS API base on template [`laravel/react-starter-kit`](https://github.com/laravel/react-starter-kit).

## Prerequisites

- PHP >= 8.2
- Composer
- payOS credentials for payment-requests or payouts

## How to Run the Application

```bash
composer run setup

# fill the required environment variables in the .env file

composer run dev
```

The application will be accessible at `http://localhost:8000`.

## Features

- [OrderController](./app/Http/Controllers/Payment/OrderController.php): Implement payment-requests API.

    ```bash
    php artisan route:list --name=api.payment.orders
    ```

- [WebhookController](./app/Http/Controllers/Payment/WebhookController.php): Handle payment webhooks.

    ```bash
    php artisan route:list --name=api.payment.webhooks
    ```

- [TransferController](./app/Http/Controllers/Payment/TransferController.php): Implement payouts API.

    ```bash
    php artisan route:list --name=api.payment.transfers
    ```
