# Simple Teamwork API - Laravel App Example

This is an API example app that serves endpoints to create users and tasks.It has an ability to retrieve tasks per user and list users that have been assigned with the task.

The application includes database seeders in order to populate users, tasks and make relations between them.

Endpoints:
```
GET /api/v1/users - List all users
GET /api/v1/users/{user_id} - Retrieve a user with the {user_id}
POST /api/v1/users/ - Create a user containing first_name, last_name, email, password
DELETE /api/v1/users/{user_id} - Delete a user with the {user_id}
GET /api/v1/tasks/{task_id}/users - Retrieve users that are assigned to the {task_id}

GET /api/v1/tasks - List all tasks
GET /api/v1/tasks/{task_id} - Retrieve a task with the {task_id}
POST /api/v1/tasks/ - Create a task containing name, description, completed
DELETE /api/v1/tasks/{task_id} - Delete a task with the {task_id}
GET /api/v1/users/{user_id}/tasks - Retrieve tasks that are assigned to the {user_id}
```


## Unit tests

Tests are located within the `tests` folder