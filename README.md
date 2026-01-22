# Exam Portal System

A secure and modern Exam Portal built with Laravel featuring Admin and User dashboards with an iPhone-inspired UI using Tailwind CSS.

## Features

### Authentication & Security
- Role-based access control (Admin/User)
- Face capture verification for login and exam start
- Activity logging for all actions
- CSRF protection and input validation

### Admin Dashboard
- iOS-inspired clean interface
- User management (CRUD with status control)
- Exam management with questions (MCQ/Descriptive)
- View exam attempts and results
- Face capture logs viewer
- Activity audit logs

### User Dashboard
- Mobile-first responsive design
- Profile management with face update
- Browse and attempt exams
- Real-time exam timer with auto-submit
- Tab switch detection and logging
- Exam history and results

## Installation

1. **Clone and install dependencies:**
```bash
cd exam-portal
composer install
```

2. **Configure environment:**
```bash
cp .env.example .env
php artisan key:generate
```

3. **Configure database in `.env`:**
```
DB_CONNECTION=sqlite
# Or for MySQL:
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=exam_portal
# DB_USERNAME=root
# DB_PASSWORD=
```

4. **Run migrations and seed:**
```bash
php artisan migrate:fresh --seed
php artisan storage:link
```

5. **Start the server:**
```bash
php artisan serve
```

## Default Credentials

### Admin
- Email: `admin@examportal.com`
- Password: `password123`

### User
- Register a new account at `/register`

## Database Schema

- `users` - User accounts with roles
- `roles` - Admin/User roles
- `exams` - Exam definitions
- `questions` - MCQ and descriptive questions
- `exam_attempts` - User exam attempts
- `answers` - User answers per attempt
- `face_captures` - Face verification images
- `activity_logs` - System audit trail

## Technology Stack

- **Backend:** Laravel 12
- **Frontend:** Blade + Tailwind CSS (CDN)
- **Database:** SQLite/MySQL
- **Camera API:** WebRTC (getUserMedia)

## UI Features

- iPhone-inspired design language
- Rounded corners and soft shadows
- Smooth transitions and animations
- Responsive mobile-first layout
- Card-based interface components

## Security Features

- Face capture on login and exam start
- Tab switch detection during exams
- Activity logging for audit
- Role-based middleware protection
- Encrypted password storage
