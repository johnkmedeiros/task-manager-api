# Task Manager API

A simple RESTful API for managing tasks, built with Laravel.

## Features

- User authentication (registration, login, logout)
- Task management (CRUD operations)
- Built-in request validation
- Uses Laravel Sanctum for API authentication

## Requirements

- PHP >= 8.2
- Composer
- Laravel >= 11.x
- MySQL or another supported database of your choice (e.g., PostgreSQL, SQLite, SQL Server)

## Installation

1. Clone the repository:

   ```bash
   git clone https://github.com/your_username/task-manager-api.git
   ```

2. Navigate to the project directory:

   ```bash
   cd task-manager-api
   ```

3. Install the dependencies:

   ```bash
   composer install
   ```

4. Copy the example environment file:

   ```bash
   cp .env.example .env
   ```

5. Generate the application key:

   ```bash
   php artisan key:generate
   ```

6. Configure your database settings in the `.env` file.

## Database Configuration for Testing

To avoid affecting your development or production database during testing, you should configure a separate database for tests. Follow the steps below:

1. Open the `phpunit.xml` file in the root of your project.
2. Inside the `<php>` section, update the environment variables for the test database, as shown below:

   ```xml
   <php>
       <env name="APP_ENV" value="testing"/>
       <env name="APP_MAINTENANCE_DRIVER" value="file"/>
       <env name="BCRYPT_ROUNDS" value="4"/>
       <env name="CACHE_STORE" value="array"/>
       <env name="MAIL_MAILER" value="array"/>
       <env name="PULSE_ENABLED" value="false"/>
       <env name="QUEUE_CONNECTION" value="sync"/>
       <env name="SESSION_DRIVER" value="array"/>
       <env name="TELESCOPE_ENABLED" value="false"/>

        <!-- Configure your testing database here -->

        <env name="DB_CONNECTION" value="mysql"/>
        <env name="DB_DATABASE" value="task_manager_api_testing"/>

        <!-- Uncomment and set the following lines if needed -->

        <!-- <env name="DB_USERNAME" value="your_username"/> -->
        <!-- <env name="DB_PASSWORD" value="your_password"/> -->
        <!-- <env name="DB_HOST" value="127.0.0.1"/> -->
   </php>
   ```

3. Replace `task_manager_api_testing` with the name of your test database. Uncomment and set the `DB_USERNAME`, `DB_PASSWORD`, and `DB_HOST` lines if necessary.
4. Save the `phpunit.xml` file.

With this configuration, your tests will run against a separate database, preventing any unwanted changes to the development or production database.

## Running Migrations

Before testing the API, run the migrations to set up the database:

```bash
php artisan migrate
```

## Running Tests

To run the tests, use the following command:

```bash
vendor/bin/phpunit
```

## API Endpoints

### Authentication

- **POST** `/api/auth/register` - Register a new user
- **POST** `/api/auth/login` - Log in a user
- **POST** `/api/auth/logout` - Log out a user (requires authentication)

### Tasks

- **GET** `/api/tasks` - Retrieve all tasks (requires authentication)
- **POST** `/api/tasks` - Create a new task (requires authentication)
- **GET** `/api/tasks/{id}` - Retrieve a specific task (requires authentication)
- **PUT** `/api/tasks/{id}` - Update a specific task (requires authentication)
- **DELETE** `/api/tasks/{id}` - Delete a specific task (requires authentication)

## API Documentation

### Authentication

#### Register User
- **Endpoint**: `POST /api/auth/register`
- **Request Body**:
  ```json
  {
    "name": "John Doe",
    "email": "john_doe@test.php",
    "password": "password",
    "password_confirmation": "password"
  }
  ```
- **Response**:
  - **201 Created**
  ```json
  {
    "access_token": "your_token_here",
    "token_type": "Bearer"
  }
  ```

#### Login User
- **Endpoint**: `POST /api/auth/login`
- **Request Body**:
  ```json
  {
    "email": "john_doe@test.php",
    "password": "password"
  }
  ```
- **Response**:
  - **200 OK**
  ```json
  {
    "access_token": "your_token_here",
    "token_type": "Bearer"
  }
  ```

#### Logout User
- **Endpoint**: `POST /api/auth/logout`
- **Headers**:
  ```
  Authorization: Bearer your_token_here
  ```
- **Response**:
  - **200 OK**
  ```json
  {
    "message": "User logged out successfully"
  }
  ```

### Additional Endpoints
WIP
