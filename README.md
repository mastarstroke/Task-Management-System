# Task Management API (Laravel 10 + Sanctum)

A RESTful API for managing tasks with **user registration**, **login**, **task CRUD operations**, **activity logging**, **rate-limited login**, and **Sanctum-based API authentication**.

---

## Features

* **User Authentication**

  * Register and login using Laravel Sanctum
  * Token-based authentication for API
  * Rate limiting: max 5 login attempts per minute per user/email/IP
* **Tasks CRUD**

  * Create, read, update, delete tasks
  * Each task belongs to a user (`user_id`)
  * Fields: `title`, `description`, `status` (`pending`, `in-progress`, `completed`)
  * Only authenticated users can access their own tasks
  * Supports **filtering by status** and **pagination**
  * **Query optimization implemented** for quality performance and to prevent lagging when fetching tasks
* **Activity Logging**

  * Logs all notable actions: registration, login, logout, task creation/update/deletion
  * Stored in a centralized helper function (`activity_log()`)
* **Error Handling**

  * Consistent JSON responses for success and error
  * Handles invalid login, unauthorized access, and CRUD failures gracefully

---

## Setup Instructions

### 1. Clone Repository

```bash
git clone https://github.com/yourusername/task-management-api.git
cd task-management-api
```

### 2. Install Dependencies

```bash
composer install
npm install
```

### 3. Environment Setup

Copy `.env.example` to `.env`:

```bash
cp .env.example .env
```

Update database configuration in `.env`:

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=task_management
DB_USERNAME=root
DB_PASSWORD=
```

Generate app key:

```bash
php artisan key:generate
```

---

### 4. Database Migration & Seeding

```bash
php artisan migrate
```

(Optional) Seed sample users/tasks if needed:

```bash
php artisan db:seed
```

---

### 5. Composer Autoload for Helpers

Make sure your `activity_log()` helper is autoloaded. In `composer.json`:

```json
"autoload": {
    "psr-4": {
        "App\\": "app/"
    },
    "files": [
        "app/Helpers/activity.php"
    ]
}
```

Then run:

```bash
composer dump-autoload
```

---

### 6. Run Application

```bash
php artisan serve
```

The API will be available at `http://127.0.0.1:8000`

---

### 7. API Endpoints

| Action      | Endpoint          | Method    | Auth |
| ----------- | ----------------- | --------- | ---- |
| Register    | `/api/register`   | POST      | ❌    |
| Login       | `/api/login`      | POST      | ❌    |
| Logout      | `/api/logout`     | POST      | ✅    |
| List Tasks  | `/api/tasks`      | GET       | ✅    |
| Create Task | `/api/tasks`      | POST      | ✅    |
| View Task   | `/api/tasks/{id}` | GET       | ✅    |
| Update Task | `/api/tasks/{id}` | PUT/PATCH | ✅    |
| Delete Task | `/api/tasks/{id}` | DELETE    | ✅    |

**Task Listing Filters (optional query parameters):**

* `status`: `pending`, `in-progress`, `completed`
* `per_page`: number of tasks per page

Example:
`GET /api/tasks?status=pending&per_page=5`

---

### 8. Rate Limiting for Login

Implemented in `AuthController@login` using Laravel’s `RateLimiter`:

* Max 5 login attempts per user/email/IP
* Temporary lockout for 1 minute if exceeded
* Returns JSON response:

```json
{
  "status": "error",
  "message": "Too many login attempts. Please try again in 1 minute."
}
```

---

### 9. Activity Logging

All notable actions are logged via the `activity_log()` helper. Examples:

```php
activity_log('User registered', ['email' => $user->email]);
activity_log('User logged in', ['email' => $user->email]);
activity_log('Task created', ['title' => $task->title]);
```

Logs can be stored in database, file, or a logging service depending on your implementation.

---

### 10. Testing

Run tests with:

```bash
php artisan test
```

Example included tests:

* Task creation
* Task listing with filter
* (Extendable to login, registration, and other endpoints)

---

### 11. Approach Summary

* **Controller-Service-Request-Resource Pattern**

  * Controllers handle HTTP requests/responses
  * Services handle business logic
  * Requests handle validation
  * Resources handle consistent API output
* **Authentication**

  * Laravel Sanctum for API token management
* **Security**

  * Rate limiting login to prevent brute force
  * Auth middleware protects task routes
* **Query Optimization**

  * Fetch only necessary fields
  * Use conditional filtering and eager loading for relations
  * Indexed columns for faster queries
  * Prevents N+1 queries and reduces database load
* **Error Handling**

  * All endpoints return structured JSON responses
  * Graceful exception handling for invalid input, missing resources, or server errors
* **Activity Logs**

  * Centralized helper logs critical actions across the system
  * Useful for auditing and monitoring

---

### 12. Future Enhancements

* Role-based access control for admin vs regular users
* Task assignment to multiple users
* Search by title/description
* Notifications for task updates
* Store activity logs in database for querying and reporting

---

### Author

**James Adeyemo** – Laravel Engineer
