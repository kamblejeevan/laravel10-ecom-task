# laravel10-ecom-task

<h2>Inital Setup</h2>

1. After cloning, navigate to the project directory "laravel10-ecom-task."

2. Execute the command "composer update" to download all the necessary dependency packages.

3. Added database credentials in the .env file

4. Run the command "php artisan migrate" to apply database migrations.

5. Execute the command "php artisan passport:client --password."

 ![laravel_task_commands](https://github.com/kamblejeevan/laravel10-ecom-task/assets/28289772/ba5248e8-c037-4039-9d80-d2aa644c8722)
6. Copy the generated Client ID and Client Secret from the command prompt and paste them into the respective fields in the .env file.

 ![laravel_task_env](https://github.com/kamblejeevan/laravel10-ecom-task/assets/28289772/27d43fa6-273d-4dbe-b4e2-730156fbb68b)<br> 
7. Finally, run the command "php artisan config:clear." This step is essential; otherwise, the changes may not take effect when using the API.

<h2>Packages Used</h2>

1. Laravel Excel - used to import csv data to tables.
2. Laravel Passport  - used for api authentiation

<h2>Import users and products data from csv file</h2>

Use Below Commands to import the data for users and products

1. php artisan import:products {path} - For example replace {path} with "php artisan import:products /var/www/html/test_product.csv"
2. php artisan import:users {path} - For example replace {path} with "php artisan import:products /var/www/html/test_users.csv"

<h2>Api's</h2>

1. login and register api's
2. Products CRUD Api's
3. Add and remove Cart and Wishlist Api's
5. Order CRUD Api'.
6. Payment Api's
