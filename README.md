# Task Manager API

REST API for managing users, tasks, categories, authentication and task assignments.

## Base URL

```
{{base_uri}}
```

## Authentication

Most endpoints require a Bearer Token.

```
Authorization: Bearer {{auth_token}}
```

The token is returned after login.

## Auth

### Login

```
POST /api/login
```

Login user and receive authentication token.

**Body**

```json
{
    "email": "ofarrell@example.net",
    "password": "password123"
}
```

**Response**

Returns:
- access token
- user information

**Example Accounts**

| Email | Password | Role |
|---|---|---|
| hadinojvan6@gmail.com | hadi123 | super_admin |
| ofarrell@example.net | password123 | user |
| hesam@example.com | hadi123 | admin |

### Register

```
POST /api/register
```

Create a new normal user.

**Body**

```json
{
    "name": "Hadi",
    "email": "hadi.test2@example.com",
    "password": "password123",
    "password_confirmation": "password123"
}
```

### Logout

```
DELETE /api/logout
```

Logout the authenticated user.

## Users

### Get Users

```
GET /api/users
```

Get users. Admin users can also filter users by role.

**Query Parameters**

```
?role=user
?role=admin
?role=super_admin
```

### Delete User

```
DELETE /api/users/{user}
```

Delete a user.

**Example**

```
DELETE /api/users/3
```

## Tasks

### Get Tasks

```
GET /api/tasks
```

Get tasks available to the authenticated user.

**Query Parameters**

```
?page=1
?status=pending
?priority=high
```

### Get Single Task

```
GET /api/tasks/{task}
```

Get information about a specific task.

**Example**

```
GET /api/tasks/6
```

### Create Task

```
POST /api/tasks
```

Create a new task.

**Body**

```json
{
    "title": "New Task",
    "description": "Practice API authentication",
    "status": "pending",
    "priority": "high",
    "due_date": "2027-09-25 18:00:00",
    "category_id": 1
}
```

### Update Task

```
PATCH /api/tasks/{task}
```

Update an existing task.

**Example**

```
PATCH /api/tasks/6
```

**Body**

```json
{
    "title": "Updated Task",
    "priority": "medium"
}
```

### Delete Task

```
DELETE /api/tasks/{task}
```

Soft delete a task.

**Example**

```
DELETE /api/tasks/4
```

### Get Trashed Tasks

```
GET /api/tasks/trashed
```

Get tasks that have been soft deleted.

### Restore Task

```
PATCH /api/tasks/{task}/restore
```

Restore a soft-deleted task.

**Example**

```
PATCH /api/tasks/2/restore
```

### Force Delete Task

```
DELETE /api/tasks/{task}/force-delete
```

Permanently delete a task.

**Example**

```
DELETE /api/tasks/4/force-delete
```

## Categories

### Get Categories

```
GET /api/categories
```

Get all categories.

### Create Category

```
POST /api/categories
```

Create a new category.

**Body**

```json
{
    "name": "Programming"
}
```

## Task Assignment

### Get Assignment Data

```
GET /api/tasks/assign-data
```

Get tasks and users that can be used on the task assignment page.

No body is required.

### Assign Task

```
POST /api/tasks/{task}/assign
```

Assign one task to multiple users.

**Example**

```
POST /api/tasks/6/assign
```

**Body**

```json
{
    "user_ids": [6, 12]
}
```

### Unassign User From Task

```
DELETE /api/tasks/{task}/unassign/{user}
```

Remove a user from a task.

**Example**

```
DELETE /api/tasks/6/unassign/12
```

No body is required.

## Super Admin

### Add Admin

```
POST /api/admins
```

Create a new admin user.

**Body**

```json
{
    "name": "Hesam",
    "email": "hesam@example.com",
    "password": "hadi123",
    "password_confirmation": "hadi123"
}
```

## Example Flow

A typical task assignment flow:

```
1. Login
2. Get Tasks
3. Get Users
4. Select a Task
5. Select multiple Users
6. POST /api/tasks/{task}/assign
7. User IDs are stored in the task_user pivot table
```

## Roles

```
user
admin
super_admin
```

Admin and Super Admin have additional permissions depending on the action.
