# Feedback System

[![PHP](https://img.shields.io/badge/PHP-v8.2-blue.svg)](https://www.php.net/)
[![Laravel](https://img.shields.io/badge/Laravel-v12.0-red.svg)](https://laravel.com)
[![Filament](https://img.shields.io/badge/Filament-v3.3-purple.svg)](https://filamentphp.com/)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)
![PHPStan](https://github.com/yourusername/feedback-system/actions/workflows/phpstan.yml/badge.svg)
![Laravel Tests](https://github.com/yourusername/feedback-system/actions/workflows/laravel-tests.yml/badge.svg)
![Prettier Check](https://github.com/yourusername/feedback-system/actions/workflows/prettier.yml/badge.svg)

A comprehensive feedback management system built with Laravel and Filament, designed to facilitate structured feedback between students, coaches, and mentors in educational environments.

## Overview

The Feedback System is an educational platform that enables structured feedback exchange between students, personal coaches, and subject mentors. It's built to support skill development by providing a centralized system for tracking progress, offering feedback, and managing mentor-student relationships.

### Purpose

This application addresses the need for organized feedback in educational settings where:

- Students need constructive feedback on specific skills and practices
- Personal coaches work with students on their overall development
- Subject mentors provide specialized guidance in their areas of expertise
- Administrators need oversight of the feedback process and team dynamics

## Core Features

### User Management
- **Role-Based Access**: Four distinct roles (Admin, Student, Subject Mentor, Personal Coach)
- **Permissions System**: Granular permissions using Spatie's Laravel Permission package
- **Google OAuth Integration**: Sign in with Google accounts
- **Team-Based Organization**: Users are organized into collaborative teams

### Feedback System
- **Targeted Feedback**: Feedback tied to specific skills and practices
- **Positive/Negative Classification**: Mark feedback as positive or constructive
- **Skill Areas**: Organize skills into categories for better tracking
- **Feedback Analytics**: Track progress through various metrics and visualizations

### Team Management
- **Team Creation**: Admins can create teams and add members
- **Email Invitations**: Invite new users via email with automatic role assignment
- **Coach-Student Assignment**: Assign personal coaches to students
- **Multi-Team Support**: Users can belong to multiple teams

### Dashboards & Analytics
- **Role-Specific Dashboards**: Tailored widgets based on user role
- **Performance Metrics**: Track feedback statistics and engagement
- **Team Statistics**: View team composition and activity
- **Student Progress**: Monitor skill development and feedback trends

### UI & UX Features
- **Modern Interface**: Clean, responsive design using TailwindCSS
- **Admin Panel**: Complete admin interface built with Filament
- **Mobile-Friendly**: Responsive design for all devices
- **User Session Management**: Improved session handling with friendly error messages

## Tech Stack

### Backend
- **PHP 8.2+**: Taking advantage of modern PHP features
- **Laravel 12**: Latest version of the Laravel framework
- **MySQL/SQLite**: Flexible database configuration

### Frontend
- **Livewire 3**: For dynamic frontend interactions
- **Volt**: For component-based frontend development
- **TailwindCSS**: For responsive styling
- **AlpineJS**: For lightweight JavaScript functionality

### Packages & Tools
- **Filament 3.3**: Admin panel framework
- **Spatie Laravel Permission**: Role and permission management
- **Laravel Socialite**: OAuth integration
- **Laravel Breeze**: Authentication scaffolding
- **GitHub Actions**: CI/CD pipelines for testing and code quality

## Installation

### Prerequisites
- PHP 8.2 or higher
- Composer
- Node.js and NPM
- Database (MySQL, PostgreSQL, or SQLite)
- (Optional) SMTP server for email functionality

### Step-by-Step Setup

1. **Clone the repository**:
   ```bash
   git clone https://github.com/yourusername/feedback-system.git
   cd feedback-system
   ```

2. **Install PHP dependencies**:
   ```bash
   composer install
   ```

3. **Install JavaScript dependencies**:
   ```bash
   npm install
   ```

4. **Set up environment variables**:
   ```bash
   cp .env.example .env
   ```
   
   Configure your database and mail settings in the `.env` file:
   ```dotenv
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=feedback_system
   DB_USERNAME=root
   DB_PASSWORD=

   MAIL_MAILER=smtp
   MAIL_HOST=your-smtp-host
   MAIL_PORT=587
   MAIL_USERNAME=your-username
   MAIL_PASSWORD=your-password
   MAIL_ENCRYPTION=tls
   MAIL_FROM_ADDRESS="noreply@example.com"
   MAIL_FROM_NAME="${APP_NAME}"
   ```

5. **Generate application key**:
   ```bash
   php artisan key:generate
   ```

6. **Run database migrations and seeders**:
   ```bash
   php artisan migrate --seed
   ```

7. **Build frontend assets**:
   ```bash
   npm run build
   ```

8. **Start the application**:
   ```bash
   # For development with hot reloading
   npm run dev
   php artisan serve
   
   # Or, for a more complete development environment
   composer dev
   ```

9. **Access the admin panel**:
   Visit `http://localhost:8000/admin` and log in with the default admin credentials:
   - Email: `admin@example.com`
   - Password: `password`

## User Guide

### Admin Access
After installation, you can access the admin panel with the default admin account:
- URL: `http://localhost:8000/admin`
- Email: `admin@example.com`
- Password: `password`

### User Roles and Access

#### Admin
- Full system access
- Can manage all teams, users, roles, and permissions
- Can view all feedback and system statistics
- Access to all administrative functions

#### Subject Mentor
- Can join teams
- Can provide feedback to students on specific skills
- Can view team and feedback statistics
- Cannot manage system-wide settings

#### Personal Coach
- Can be assigned to specific students
- Can provide personalized feedback to assigned students
- Can view coached students' progress
- Limited to interactions with assigned students

#### Student
- Can view feedback received
- Can track progress across skills
- Can see assigned coaches and mentors
- Limited dashboard with personal statistics

### Key Workflows

#### Creating a Team
1. Log in as an admin
2. Navigate to Teams > Create Team
3. Fill in team details and create
4. Add members through team invitations

#### Inviting Users
1. Navigate to Team Invitations > Create Invitation
2. Enter email and select role
3. Send invitation
4. User receives email with link to join

#### Assigning Coaches to Students
1. Navigate to Coach Assignments > Create Assignment
2. Select team, coach, and student
3. Create the assignment
4. Coach can now provide feedback to the student

#### Providing Feedback
1. Navigate to Feedback > Create Feedback
2. Select recipient, skill, and practice
3. Add comments and mark as positive/constructive
4. Submit feedback

## Database Structure

### Core Models

- **User**: Application users with role assignments
  - Relations: teams, createdTeams, coachingStudents, personalCoaches, sentFeedback, receivedFeedback

- **Team**: Groupings of users
  - Relations: users, creator, students, subjectMentors, personalCoaches, feedback, coachStudentRelationships

- **TeamInvitation**: Email invitations to join teams
  - Relations: team, inviter

- **Feedback**: Feedback instances between users
  - Relations: sender, recipient, team, skill, practice

- **Skill**: Trackable skills that can receive feedback
  - Relations: skillArea, feedback, practices

- **SkillArea**: Categories for grouping skills
  - Relations: skills

- **Practice**: Specific practices related to skills
  - Relations: skill, feedback

- **CoachStudent**: Relationships between coaches and students
  - Relations: coach, student, team

## Customization

### Adding New Skills and Practices
1. Navigate to Skills > Create Skill
2. Fill in skill details and save
3. Navigate to Practices > Create Practice
4. Associate practice with a skill and save

### Adding New Skill Areas
1. Navigate to Skill Areas > Create Skill Area
2. Fill in details and save
3. Skills can now be associated with this area

### Customizing Permissions
1. Navigate to Roles > Edit Role
2. Modify permissions for each role
3. Save changes

## Development and Customization

### Project Structure Highlights

- `app/Filament/Resources/`: Filament resource definitions
- `app/Filament/Widgets/`: Dashboard widgets for different roles
- `app/Models/`: Eloquent model definitions
- `app/Policies/`: Permission policies
- `database/seeders/`: Data seeders for initial setup

### Creating Custom Widgets
1. Create a new widget class in `app/Filament/Widgets/`
2. Implement the `canView()` method for role-based visibility
3. Define widget content in the `getStats()` method

### Extending the System
The application is built with extensibility in mind:
- Add new roles by modifying the `RolesAndPermissionsSeeder`
- Extend the feedback system by adding new fields to the Feedback model
- Create custom dashboards by adding new widgets

## Contributing

Contributions are welcome! Please follow these steps:

1. Fork the repository
2. Create a feature branch: `git checkout -b feature-name`
3. Make your changes and commit them: `git commit -m 'Add some feature'`
4. Push to the branch: `git push origin feature-name`
5. Create a pull request

### Development Guidelines
- Follow PSR-12 coding standards
- Write tests for new features
- Document changes in code and README
- Use descriptive commit messages

## Troubleshooting

### Common Issues

#### Session Expiration Errors
- The application will redirect to login with a friendly message instead of showing a 419 error page

#### Database Connection Issues
- Verify your database credentials in `.env`
- Ensure your database server is running

#### Mail Configuration
- If invitation emails aren't being sent, check your SMTP configuration
- For local development, consider using a service like Mailtrap

## License

This project is licensed under the MIT License - see the LICENSE file for details.

## Acknowledgements

- [Laravel](https://laravel.com/) - The PHP framework used
- [Filament](https://filamentphp.com/) - The admin panel framework
- [Spatie Laravel Permission](https://github.com/spatie/laravel-permission) - For role and permission management
- [Laravel Socialite](https://laravel.com/docs/socialite) - For OAuth integration

## Contact

For questions or support, please contact [your-email@example.com](mailto:your-email@example.com).
#   f i a l m e n t - 1 2 
 
 
# sage_experience
# sage_experience
