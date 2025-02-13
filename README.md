## Run the stack

```docker compose up -d```
Database is running on port 3306.
Credentials are located in docker-compose.yml

## Setup database

Setup database structure:
```php artisan migrate```

Populate with some data: 
```php artisan db:seed```

## Rollback database

```php artisan db:wipe```

## Create an order

php artisan order:create {email} {sku}:{quantity}  
e.g. ```php artisan order:create yundt.raina@yahoo.com 0658135374789:1```

## Create order with multiple products

php artisan order:create {email} {sku}:{quantity},{sku}:{quantity},{sku}:{quantity}  
e.g. ```php artisan order:create yundt.raina@yahoo.com 0658135374789:1,0658135374777:2```

## Update an order
php artisan order:update {orderId} {orderStatus}  
e.g. ```php artisan order:update 1 completed```