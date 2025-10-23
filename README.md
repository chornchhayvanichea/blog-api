# Blog API

A RESTful API built with Laravel for a blog platform with user authentication, post management, and admin controls.

## Features

### Authentication

- User registration (signup)
- User login (JWT-based)
- Password reset (email token)
- Forgot password
- Token refresh
- Logout

### Posts

- Create, read, update, delete posts
- Image upload for posts
- Post categories
- Soft delete posts
- Admin can restore deleted posts

### Comments

- Add comments to posts
- Update and delete own comments
- View all comments on a post

### Admin Controls

- Ban/unban users
- Manage all users' posts
- Restore deleted posts

### Reports

- Report feature with polymorphic relationships

### Likes

- Like feature for Comment and Post

### Bookmark

- Bookmark allows user to save posts

## Tech Stack

- **Framework**: Laravel 10.x
- **Authentication**: JWT (PHPOpen-Source-Saver/jwt-auth)
- **Database**: MySQL
- **Storage**: Local file storage for images

## Technical Implementation

### Security & Authorization

- **Policies**: Role-based access control for posts, comments, and users
- **Middleware**:
    - `auth:api` - JWT authentication
    - `admin` - Admin-only routes
    - `banned` - Prevent banned users from accessing resources
- **Form Requests**: Input validation for all create/update operations

### API Design

- RESTful API architecture
- JSON responses
- Proper HTTP status codes
- Token-based authentication
