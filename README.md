# Task Management API

> **Laravel 11 · MySQL · Vanilla JS Frontend**
> Submission for the Software Engineering Internship Coding Challenge 2026

---

## Table of Contents

1. [What Was Built](#1-what-was-built)
2. [Project Structure](#2-project-structure)
3. [Tech Stack](#3-tech-stack)
4. [How to Run Locally (Step-by-Step)](#4-how-to-run-locally-step-by-step)
5. [API Endpoints — Full Reference](#5-api-endpoints--full-reference)
6. [Business Rules Explained](#6-business-rules-explained)
7. [How to Deploy on Railway](#7-how-to-deploy-on-railway)
8. [Database — MySQL Dump](#8-database--mysql-dump)
9. [Testing All Endpoints (curl Examples)](#9-testing-all-endpoints-curl-examples)
10. [Code Design Decisions](#10-code-design-decisions)

---

## 1. What Was Built

A fully functional **Task Management REST API** built with **Laravel 11** and **MySQL**, with a **Vanilla JavaScript** single-page frontend.

### Features Implemented

| # | Feature | Status |
|---|---------|--------|
| 1 | Create a task | ✅ |
| 2 | List tasks (with status filter + correct priority sort) | ✅ |
| 3 | Update task status (strict one-way progression) | ✅ |
| 4 | Delete task (only if done, returns 403 otherwise) | ✅ |
| 5 | Daily report by priority × status | ✅ Bonus |
| 6 | Vanilla JS single-page UI | ✅ |
| 7 | MySQL database with migration + seeder | ✅ |
| 8 | README with deployment instructions | ✅ |
| 9 | SQL dump file | ✅ |

---

## 2. Project Structure

Below is a map of every file in this project and what it does.

```
task-management-api/
│
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   └── API/
│   │   │       └── TaskController.php   ← All 5 API endpoints live here
│   │   │
│   │   └── Requests/
│   │       ├── CreateTaskRequest.php    ← Validation rules for creating tasks
│   │       └── UpdateTaskStatusRequest.php  ← Validation for status updates
│   │
│   └── Models/
│       └── Task.php                     ← The Task database model
│
├── database/
│   ├── migrations/
│   │   └── 2026_03_30_000000_create_tasks_table.php  ← Creates the tasks table
│   │
│   └── seeders/
│       ├── DatabaseSeeder.php           ← Master seeder (calls TaskSeeder)
│       └── TaskSeeder.php               ← Inserts 11 sample tasks for testing
│
├── routes/
│   └── api.php                          ← All API route definitions
│
├── public/
│   └── index.html                       ← Vanilla JS frontend (single file)
│
├── tasks_dump.sql                        ← MySQL dump (submit this file)
├── .env.example                          ← Template for your .env config
└── README.md                             ← This file
```

**Files you copy into a fresh Laravel project:** everything except the top-level Laravel boilerplate files (`composer.json`, `bootstrap/`, `vendor/`, etc.) which come with Laravel automatically.

---

## 3. Tech Stack

| Layer | Technology | Why |
|-------|-----------|-----|
| Backend framework | **Laravel 11** | PHP framework — specified in brief |
| Database | **MySQL** | Specified in brief |
| ORM | **Eloquent** (Laravel built-in) | Cleaner, more readable than raw SQL |
| Validation | **Form Requests** (Laravel built-in) | Separates validation logic from controllers |
| Frontend | **Vanilla JavaScript** | No framework needed, fast, meets spec |
| HTTP Client (frontend) | **Fetch API** (browser built-in) | No dependencies |
| Hosting | **Railway** | Free, supports Laravel + MySQL |

---

## 4. How to Run Locally (Step-by-Step)

> **Prerequisites — install these before starting:**
> - [PHP 8.2+](https://www.php.net/downloads) (check with `php -v`)
> - [Composer](https://getcomposer.org/) (check with `composer -v`)
> - [MySQL 8.0+](https://dev.mysql.com/downloads/) (or use [XAMPP](https://www.apachefriends.org/))
> - [Git](https://git-scm.com/downloads) (optional but recommended)

---

### Step 1 — Create a fresh Laravel project

Open your terminal (Command Prompt on Windows, Terminal on Mac/Linux) and run:

```bash
composer create-project laravel/laravel task-management-api
cd task-management-api
```

This downloads Laravel and all its dependencies. It will take 1–3 minutes.

---

### Step 2 — Copy the project files

Copy the following files and folders from this submission **into** the Laravel project, replacing any existing files:

```
app/Http/Controllers/API/TaskController.php   → copy in full
app/Http/Requests/CreateTaskRequest.php       → copy in full
app/Http/Requests/UpdateTaskStatusRequest.php → copy in full
app/Models/Task.php                            → replace the default User.php approach
database/migrations/2026_03_30_000000_create_tasks_table.php → add to migrations folder
database/seeders/TaskSeeder.php               → copy in full
database/seeders/DatabaseSeeder.php           → replace existing
routes/api.php                                → replace existing
public/index.html                             → add this file
```

> **Tip:** You can also clone this project directly from GitHub (if uploaded) and skip the copy step.

---

### Step 3 — Create your MySQL database

Open your MySQL client and run:

```sql
CREATE DATABASE task_management CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

If you are using **XAMPP**, open phpMyAdmin at http://localhost/phpmyadmin and create the database from the GUI.

---

### Step 4 — Configure your .env file

In the project root, copy the example env file:

```bash
cp .env.example .env
```

Then open `.env` in a text editor and update the database section:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=task_management
DB_USERNAME=root
DB_PASSWORD=            ← leave blank if your MySQL has no password (common in XAMPP)
```

---

### Step 5 — Generate the app key

Laravel requires a unique encryption key. Generate it with:

```bash
php artisan key:generate
```

You will see: `Application key set successfully.`

---

### Step 6 — Run the database migration

This creates the `tasks` table in your MySQL database:

```bash
php artisan migrate
```

Expected output:
```
  INFO  Running migrations.
  2026_03_30_000000_create_tasks_table .... 17ms DONE
```

---

### Step 7 — Seed the database with sample data

This inserts 11 sample tasks so you can test the API immediately:

```bash
php artisan db:seed
```

Expected output:
```
  INFO  Seeding database.
  ✅  TaskSeeder: 11 tasks created successfully.
```

> **Want to start fresh at any time?** Run:
> `php artisan migrate:fresh --seed`
> This drops all tables, recreates them, and re-inserts the sample data.

---

### Step 8 — Start the development server

```bash
php artisan serve
```

You will see:
```
  INFO  Server running on [http://127.0.0.1:8000].
```

Your API is now live at **http://localhost:8000/api**

---

### Step 9 — Open the frontend

Open your browser and go to:

```
http://localhost:8000
```

You will see the Task Manager UI. The API Base URL is pre-filled as `http://localhost:8000/api`. Click **Connect** to load all tasks.

> Everything runs in one command — no separate frontend server needed.

---

## 5. API Endpoints — Full Reference

**Base URL (local):** `http://localhost:8000/api`

All responses are JSON. All requests that send data should include the header:
`Content-Type: application/json`

---

### POST /api/tasks — Create a Task

Creates a new task.

**Request body (JSON):**

```json
{
  "title":    "Fix login bug",
  "due_date": "2026-04-05",
  "priority": "high"
}
```

| Field | Required | Rules |
|-------|----------|-------|
| `title` | Yes | String, max 255 chars. Must be unique per due_date. |
| `due_date` | Yes | Date in `YYYY-MM-DD` format. Must be today or future. |
| `priority` | Yes | One of: `low`, `medium`, `high` |

**Success response (201 Created):**
```json
{
  "message": "Task created successfully.",
  "data": {
    "id": 12,
    "title": "Fix login bug",
    "due_date": "2026-04-05",
    "priority": "high",
    "status": "pending",
    "created_at": "2026-03-30T10:00:00.000000Z",
    "updated_at": "2026-03-30T10:00:00.000000Z"
  }
}
```

**Error response (422 Unprocessable):**
```json
{
  "message": "Validation failed. Please check your input.",
  "errors": {
    "title": ["A task with this title already exists for the chosen due date."],
    "due_date": ["The due date must be today or a future date."]
  }
}
```

---

### GET /api/tasks — List Tasks

Returns all tasks, sorted by priority (high first) then due_date (earliest first).

**Optional query parameter:**

```
GET /api/tasks?status=pending
GET /api/tasks?status=in_progress
GET /api/tasks?status=done
```

**Success response (200 OK):**
```json
{
  "message": "Tasks retrieved successfully.",
  "total": 3,
  "data": [
    {
      "id": 1,
      "title": "Fix critical authentication bug",
      "due_date": "2026-03-31",
      "priority": "high",
      "status": "in_progress",
      "created_at": "...",
      "updated_at": "..."
    }
  ]
}
```

**Empty response (200 OK — not 404):**
```json
{
  "message": "No tasks found with status 'done'.",
  "data": [],
  "total": 0
}
```

---

### PATCH /api/tasks/{id}/status — Update Task Status

Moves a task forward through its status lifecycle.

**Status progression (one-way only):**
```
pending  →  in_progress  →  done
```

**Request body:**
```json
{
  "status": "in_progress"
}
```

**Success response (200 OK):**
```json
{
  "message": "Task status updated successfully: 'in_progress'.",
  "data": { ... }
}
```

**Error — invalid transition (422):**
```json
{
  "message": "Invalid status transition. Task 'Fix bug' is currently 'pending'. It can only move to 'in_progress', not 'done'.",
  "current_status": "pending",
  "allowed_next": "in_progress"
}
```

---

### DELETE /api/tasks/{id} — Delete a Task

Deletes a task. **Only tasks with status `done` can be deleted.**

**Success response (200 OK):**
```json
{
  "message": "Task 'Fix bug' (ID: 4) was deleted successfully."
}
```

**Error — task not done (403 Forbidden):**
```json
{
  "message": "Forbidden. Only completed tasks (status: 'done') can be deleted. This task has status: 'pending'."
}
```

**Error — task not found (404):**
```json
{
  "message": "Task with ID 999 not found."
}
```

---

### GET /api/tasks/report — Daily Report (Bonus)

Returns a summary of task counts grouped by priority and status for a specific date.

```
GET /api/tasks/report?date=2026-04-01
```

**Success response (200 OK):**
```json
{
  "date": "2026-04-01",
  "summary": {
    "high":   { "pending": 2, "in_progress": 1, "done": 0 },
    "medium": { "pending": 1, "in_progress": 0, "done": 3 },
    "low":    { "pending": 0, "in_progress": 0, "done": 1 }
  }
}
```

---

## 6. Business Rules Explained

This section explains **why** certain design decisions were made.

### Rule 1 — Unique title per due_date (not globally)

> "title cannot duplicate a task with the same due_date"

This means:
- "Write report" due **2026-04-01** + "Write report" due **2026-04-05** → ✅ Allowed (different dates)
- "Write report" due **2026-04-01** twice → ❌ Rejected (same title AND date)

Implemented as a **composite unique index** in the database migration AND validated in `CreateTaskRequest.php`.

---

### Rule 2 — Priority sorting uses FIELD(), not alphabetical order

> "Sort by priority (high → low), then due_date ascending"

Alphabetically: high, low, medium (wrong!)
Correct order:  high, medium, low

We use MySQL's `FIELD()` function in the Eloquent query:

```php
->orderByRaw("FIELD(priority, 'high', 'medium', 'low')")
->orderBy('due_date', 'asc')
```

`FIELD(priority, 'high', 'medium', 'low')` returns:
- `high` → position 1 (first)
- `medium` → position 2
- `low` → position 3 (last)

---

### Rule 3 — Status is a strict one-way progression

The `STATUS_TRANSITIONS` map in `Task.php` defines what is allowed:

```php
const STATUS_TRANSITIONS = [
    'pending'     => 'in_progress',  // Can only go to in_progress
    'in_progress' => 'done',         // Can only go to done
    'done'        => null,           // Terminal — no further transitions
];
```

The controller checks: `if (!$task->canTransitionTo($requestedStatus))` before saving.

---

### Rule 4 — Delete returns 403, not 404

The spec says tasks that are not `done` return **403 Forbidden**.
This is a business rule (you are forbidden from deleting it), not "not found".
The task EXISTS, you are just not ALLOWED to delete it. Hence 403.

---

### Rule 5 — Report route must come before /{id} route

In `routes/api.php`, the `/tasks/report` route is declared **before** `/tasks/{id}`.

If it were after, Laravel would interpret the word `report` as an ID value and try to find task ID "report", returning a confusing 404.

---

## 7. How to Deploy on Railway

Railway is a free hosting platform that supports PHP + MySQL. Here's how to deploy:

### Prerequisites
- A free account at [railway.app](https://railway.app)
- Your project pushed to a GitHub repository

### Step 1 — Push your project to GitHub

```bash
git init
git add .
git commit -m "Initial commit — Task Management API"
git remote add origin https://github.com/YOUR_USERNAME/task-management-api.git
git push -u origin main
```

### Step 2 — Create a Railway project

1. Go to [railway.app](https://railway.app) and log in
2. Click **New Project → Deploy from GitHub repo**
3. Select your repository
4. Railway will auto-detect it as a PHP project

### Step 3 — Add a MySQL database

1. In your Railway project, click **+ New Service**
2. Select **Database → MySQL**
3. Railway creates a MySQL instance and shows you the connection variables

### Step 4 — Set environment variables

In Railway, go to your PHP service → **Variables** tab and add:

```
APP_NAME=Task Management API
APP_ENV=production
APP_KEY=          ← generate with: php artisan key:generate --show
APP_DEBUG=false
APP_URL=          ← Railway gives you this URL after deployment

DB_CONNECTION=mysql
DB_HOST=          ← copy from Railway MySQL service Variables → MYSQLHOST
DB_PORT=          ← copy from Railway MySQL service Variables → MYSQLPORT
DB_DATABASE=      ← copy from Railway MySQL service Variables → MYSQLDATABASE
DB_USERNAME=      ← copy from Railway MySQL service Variables → MYSQLUSER
DB_PASSWORD=      ← copy from Railway MySQL service Variables → MYSQLPASSWORD
```

### Step 5 — Add a Procfile

Create a file called `Procfile` in the project root:

```
web: php artisan serve --host=0.0.0.0 --port=$PORT
release: php artisan migrate --force && php artisan db:seed --force
```

The `release` line automatically runs migrations and seeds when you deploy.

### Step 6 — Deploy

Push your changes to GitHub — Railway will automatically redeploy.

```bash
git add Procfile
git commit -m "Add Procfile for Railway"
git push
```

After deployment, Railway gives you a public URL like:
`https://task-management-api-production.up.railway.app`

Your API is live at: `https://YOUR-URL.railway.app/api/tasks`
Your frontend is at: `https://YOUR-URL.railway.app`

---

## 8. Database — MySQL Dump

The file **`tasks_dump.sql`** is included in this submission.

It contains:
- `CREATE DATABASE` statement
- `CREATE TABLE tasks` with all columns, types, and indexes
- 11 sample rows of demo data

### To import the dump:

**Via terminal:**
```bash
mysql -u root -p < tasks_dump.sql
```

**Via phpMyAdmin:**
1. Open phpMyAdmin → Import tab
2. Choose the `tasks_dump.sql` file
3. Click Go

> **Note:** If you run `php artisan migrate && php artisan db:seed`, you get the same result as importing the dump. Both approaches work.

---

## 9. Testing All Endpoints (curl Examples)

> **What is curl?** A command-line tool for making HTTP requests. You can also use [Postman](https://www.postman.com/) (free GUI tool) if you prefer.

Replace `localhost:8000` with your Railway URL for live testing.

---

### Create a task
```bash
curl -X POST http://localhost:8000/api/tasks \
  -H "Content-Type: application/json" \
  -d '{"title":"Fix login bug","due_date":"2026-04-05","priority":"high"}'
```

### List all tasks
```bash
curl http://localhost:8000/api/tasks
```

### List only pending tasks
```bash
curl "http://localhost:8000/api/tasks?status=pending"
```

### Advance task #1 to in_progress
```bash
curl -X PATCH http://localhost:8000/api/tasks/1/status \
  -H "Content-Type: application/json" \
  -d '{"status":"in_progress"}'
```

### Advance task #1 to done
```bash
curl -X PATCH http://localhost:8000/api/tasks/1/status \
  -H "Content-Type: application/json" \
  -d '{"status":"done"}'
```

### Delete task #4 (must already be done)
```bash
curl -X DELETE http://localhost:8000/api/tasks/4
```

### Get daily report
```bash
curl "http://localhost:8000/api/tasks/report?date=2026-04-01"
```

### Test the 403 error (try deleting a pending task)
```bash
curl -X DELETE http://localhost:8000/api/tasks/2
# Expected: {"message":"Forbidden. Only completed tasks..."}
```

### Test invalid status skip (pending → done, skipping in_progress)
```bash
curl -X PATCH http://localhost:8000/api/tasks/2/status \
  -H "Content-Type: application/json" \
  -d '{"status":"done"}'
# Expected: 422 with "can only move to 'in_progress'"
```

---

## 10. Code Design Decisions

### Why Form Requests instead of inline validation?

Laravel lets you validate inside the controller with `$request->validate()`, but using **Form Request classes** (`CreateTaskRequest`, `UpdateTaskStatusRequest`) is cleaner because:

- The controller stays focused on business logic
- Each request class has a single responsibility: validate
- It is standard Laravel best practice and shows knowledge of the framework

### Why OOP (Object-Oriented Programming)?

The codebase uses OOP throughout:
- **Models** (`Task`) encapsulate database logic and constants in one place
- **Controllers** encapsulate related endpoint logic
- **Helper methods** (`canTransitionTo()`, `nextStatus()`) on the model mean the transition logic lives in one place, not scattered across the controller

### Why constants on the Model?

```php
const PRIORITIES = ['low', 'medium', 'high'];
const STATUSES   = ['pending', 'in_progress', 'done'];
const STATUS_TRANSITIONS = [ ... ];
```

If you ever add a new status (e.g. `archived`), you change it in **one place** — the model — and the rest of the application picks it up automatically. No hunting through multiple files.

### Why is the report route defined first in api.php?

Laravel matches routes **in the order they are defined**. If `/tasks/{id}` came before `/tasks/report`, the string `report` would be matched as an `{id}` parameter, causing a 404. This is a common gotcha and is explicitly handled.

---

*Submitted by: [Your Name] | Date: 1st April 2026 | Challenge: Laravel Engineer Intern Take-Home Assignment*
