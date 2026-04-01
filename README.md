# TASK MANAGEMENT API
-Hosted on Railway via 
-Built with Laravel and MySQL with a Vanilla JS frontend.
-Functions:
  Create tasks with a title, due date, and priority
  List tasks sorted by priority (high, medium, low) then due date
  Update task status following a strict progression (pending,in progress, done)
  Delete tasks (only when status is done)
  Generate a daily report showing task counts by priority and status

# Requirements
PHP 8.4, Composer, MySQL

# How to run locally
1. git clone https://github.com/ctrlmumo/Task_Management_API.git
2. cd Task_Management_API
3. composer install (install dependencies)
4. cp .env.example .env (set up environment file)
5. Open .env and update the database section with your MySQL credentials
6. php artisan key:generate (generate app key)
7. Open MySQL client or phpMyAdmin and run: sqlCREATE DATABASE task_management;
6. Run the migrations: bashphp artisan migrate (creates the task table)
7. Seed the database: bashphp artisan db:seed (adds 11 data samples)
8. Start the server: bashphp artisan serve (http://localhost:8000)

Frontend UI: http://localhost:8000
API: http://localhost:8000/api/tasks