# Store Order & Inventory Mini-System

A Laravel-based retail store order and inventory management application. It allows a store counter to create customer orders, calculate product-level taxes, maintain inventory automatically, and monitor low-stock products.

## 1. Technology Stack

- Backend: Laravel 12
- Language: PHP 8.2+
- Database: MySQL 8+
- Database management: phpMyAdmin
- Frontend: Blade, HTML, CSS, JavaScript
- ORM: Eloquent
- Queue: Laravel Database Queue
- Testing: PHPUnit
- Development environment: XAMPP

## 2. Main Features

### Product Management
- Product name and unique product code.
- Unit price.
- Tax percentage.
- Available stock quantity.
- Low-stock monitoring.

### Customer Management
- Customer name and unique email.
- Automatic customer creation when placing an order.
- Existing customers can place additional orders.

### Order Management
- One customer per order.
- Multiple product lines.
- Product quantity selection.
- Automatic subtotal calculation.
- Product-level tax calculation.
- Automatic grand total calculation.
- Inventory deduction after successful order creation.

### Inventory Management
- Stock availability validation.
- Automatic stock deduction.
- Database transactions.
- Row-level locking using `lockForUpdate()`.
- Protection against overselling during concurrent orders.

### Customer Order History
- Retrieve customer orders using email.
- Include order totals and purchased products.

### Low-Stock Alerts
- Retrieve products below the configured stock threshold.
- Display low-stock products in the billing interface.

### Queued Order Confirmation
- Dispatch an order-confirmation job after successful order creation.
- Process the job asynchronously using Laravel queues.
- Simulate email delivery through Laravel logging.
- No external SMTP configuration required.

## 3. Installation and Setup

### Requirements

- PHP 8.2 or higher.
- Composer.
- MySQL.
- XAMPP.
- Node.js and npm.

### Step 1: Clone the repository

```bash
git clone YOUR_PRIVATE_GITHUB_REPOSITORY_URL
cd store-inventory
```

### Step 2: Install dependencies

```bash
composer install
npm install
```

### Step 3: Configure environment

```bash
copy .env.example .env
php artisan key:generate
```

Update the database configuration in `.env`:

```env
APP_NAME="Store Inventory"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=store_inventory
DB_USERNAME=root
DB_PASSWORD=

QUEUE_CONNECTION=database
MAIL_MAILER=log
```

Create a MySQL database named `store_inventory` using phpMyAdmin.

Update the database username and password according to your MySQL configuration.

### Step 4: Run migrations

```bash
php artisan optimize:clear
php artisan migrate
```

### Step 5: Seed sample data

```bash
php artisan db:seed
```

Sample products and customers will be inserted into the database.

### Step 6: Start the application

Terminal 1:

```bash
php artisan serve
```

Terminal 2:

```bash
php artisan queue:work
```

Terminal 3:

```bash
npm run dev
```

Open:

http://127.0.0.1:8000/billing

## 4. API Documentation

### Create an Order

**Endpoint**

`POST /api/orders`

**Request**

```json
{
    "customer": {
        "name": "Arun Kumar",
        "email": "arun@example.com"
    },
    "items": [
        {
            "product_id": 1,
            "quantity": 2
        }
    ]
}
```

**Expected response**

HTTP 201 Created.

The response includes customer information, order totals and order items.

### Customer Order History

**Endpoint**

`GET /api/customers/orders?email=arun@example.com`

Returns the customer's order history.

### Low-Stock Products

**Endpoint**

`GET /api/products/low-stock`

Returns products whose stock is below the configured threshold.

## 5. Database Design

The application uses the following primary tables:

| Table | Purpose |
|---|---|
| products | Product catalogue and stock |
| customers | Customer information |
| orders | Customer order totals |
| order_items | Individual products within orders |
| jobs | Queued background jobs |
| failed_jobs | Failed queue jobs |

Relationships:

- Customer has many orders.
- Order belongs to a customer.
- Order has many order items.
- Order item belongs to an order.
- Order item belongs to a product.

## 6. Important Design Decisions

### Money Calculations

Money calculations are performed using integer cents inside the order service to reduce floating-point rounding errors.

### Inventory Concurrency

Order creation uses a database transaction and row-level locking.

Product rows are locked in a consistent ID order before stock validation and deduction.

This prevents two simultaneous orders from purchasing stock that is no longer available.

**Database requirement:** MySQL InnoDB is used to support transactional row-level locking.

### Queue Processing

The confirmation job is dispatched after the transaction commits.

This ensures that confirmation processing does not start for an order that has failed to commit.

### Tax Calculation

Tax is calculated separately for each product line using that product's configured tax percentage.

## 7. Automated Testing

Run all automated tests:

```bash
php artisan test
```

The test suite covers:

- Successful order creation.
- Automatic stock deduction.
- Insufficient-stock rejection.
- Customer order history.
- Unknown customer handling.
- Low-stock endpoint.
- Concurrent stock protection.
- Order confirmation job dispatch.
- Confirmation job execution.
- Missing-order handling.

### Latest Test Results

**10 tests passed, 33 assertions.**

## 8. Assumptions

1. Customer email addresses are unique.
2. Product codes are unique.
3. Customers are created automatically when an order is placed with a new email address.
4. Product prices and tax percentages are read from the database, not trusted from the API request.
5. Stock cannot become negative through order creation.
6. Tax is calculated per product line and rounded to two decimal places.
7. Confirmation emails are simulated using Laravel logging; no actual email is sent.
8. MySQL InnoDB is required for the intended concurrency protection.

## 9. Project Structure

```text
app/
 ├── Http/
 │    ├── Controllers/
 │    │    └── Api/
 │    └── Requests/
 ├── Jobs/
 │    └── SendOrderConfirmation.php
 ├── Models/
 └── Services/
      └── OrderService.php

database/
 ├── factories/
 ├── migrations/
 └── seeders/

resources/
 └── views/
      └── store-billing.blade.php

routes/
 ├── api.php
 └── web.php

tests/
 ├── Feature/
 └── Unit/
```

## 10. Future Improvements

- Product CRUD interface.
- Customer management dashboard.
- Order cancellation and stock restoration.
- Actual email integration.
- Sales reports and analytics.
- Authentication and role-based access control.

---

**Project:** Store Order & Inventory Mini-System  
**Framework:** Laravel 12  
**Database:** MySQL  
**Testing:** PHPUnit