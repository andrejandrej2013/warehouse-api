# Warehouse API

A simple warehouse management API built with Laravel 12. This project allows managing products and orders, ensuring stock integrity when creating or updating orders.

---

## Features

- Manage **Products** with fields: name, code, description, price, quantity.
- Manage **Orders** consisting of multiple products.
- When creating or updating an order, the product stock is automatically decremented.
- Prevents orders if requested product quantity is not available.
- API endpoints for CRUD operations on Products and Orders.
- Includes automated tests for API endpoints and business logic.

---

## Requirements

- PHP >= 8.2
- Composer
- SQLite
- Laravel 12

---

## Setup

1. Clone the repository:

   ```bash
   git clone https://github.com/yourusername/your-repo-name.git
   cd your-repo-name
   ```

2. Install dependencies:

   ```bash
   composer install
   ```
   
4. Copy .env and generate application key:

   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   
5. Run migrations and seeders:

   ```bash
   php artisan migrate --seed
   ```
   
7. Serve the application:

   ```bash
   php artisan serve
   ```

API Endpoints:
Products
| Method | URI                  | Description        |
| ------ | -------------------- | ------------------ |
| GET    | `/api/products`      | List all products  |
| GET    | `/api/products/{id}` | Get product by ID  |
| POST   | `/api/products`      | Create new product |
| PUT    | `/api/products/{id}` | Update product     |
| DELETE | `/api/products/{id}` | Delete product     |

Orders
| Method | URI                | Description                          |
| ------ | ------------------ | ------------------------------------ |
| GET    | `/api/orders`      | List all orders with products        |
| GET    | `/api/orders/{id}` | Get order by ID with products        |
| POST   | `/api/orders`      | Create new order with products       |
| PUT    | `/api/orders/{id}` | Update order products and quantities |
| DELETE | `/api/orders/{id}` | Delete order                         |

Tests:

```bash
php artisan test
```
