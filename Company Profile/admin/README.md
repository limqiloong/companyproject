# Project Management Backend

This is the admin backend system for managing projects in the database.

## Access

- **Login URL**: `admin/login.php`
- **Default Credentials**:
  - Username: `admin`
  - Password: `admin123`

**⚠️ IMPORTANT: Change the default password in `admin/login.php` for security!**

## Features

- **Login System**: Simple session-based authentication
- **View Projects**: List all projects from database
- **Add Project**: Create new projects with all details
- **Edit Project**: Update existing project information
- **Delete Project**: Remove projects from database

## Database Structure

The system works with the following database tables:

### `projects` table
- `id` (INT, Primary Key, Auto Increment)
- `project_name` (VARCHAR) - Required
- `reference_no` (VARCHAR) - Optional
- `contract_value` (DECIMAL) - Required
- `commence_date` (DATE) - Optional
- `completion_date` (DATE) - Optional
- `client_name` (VARCHAR) - Optional
- `status_id` (INT, Foreign Key) - Required

### `project_status` table
- `status_id` (INT, Primary Key)
- `status_name` (VARCHAR)
- `status_code` (VARCHAR) - e.g., 'COMPLETED', 'ONGOING', 'FUTURE'

## Usage

1. Navigate to `admin/login.php`
2. Login with credentials
3. Use "Add New Project" to create projects
4. Click "Edit" to modify existing projects
5. Click "Delete" to remove projects (with confirmation)

## Security Note

This is a basic authentication system. For production use, consider:
- Stronger password hashing (bcrypt)
- CSRF protection
- Input validation and sanitization
- Role-based access control
- Secure session management



