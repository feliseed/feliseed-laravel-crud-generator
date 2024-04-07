# Laravel CRUD Generator

You can automatically generate CRUD functionality using the command below. Everything necessary, including the controller, view, model, request, routing, test, factory, seeder, and migration, will be generated.

```bash
# Usage example 1: Generate CRUD by specifying the model name
php artisan make:crud MasterProduct

# Usage example 2: Generate CRUD from a JSON file defining the table structure
php artisan make:crud MasterProduct --schema="./master_products.json"

# Usage example 3: Generate CRUD from an existing table (please set the database connection information appropriately in .env, etc.)
php artisan make:crud MasterProduct --table=master_products

# Reflect in the DB
php artisan migrate:refresh --seed
```
