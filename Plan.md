# Dar Al-'Ulum Montréal Hifz Management System

## Project Overview
This web app is a comprehensive student progress management system for Dar Al-'Ulum Montréal. This system is designed to help manage students' Quran memorization progress, track attendance, and facilitate communication between teachers, administrators, and parents.

As of now, the system is in the early stages of development. The hifz progress is being implemented but the academics progress is not yet implemented.


## System Structure
The project follows a modular structure:

```
Source
│
├── View (Frontend)
│   ├── html
│   │   ├── admin (Admin exclusive interface files)
│   │   └── teacher (Teacher exclusive interface files)
│   └── css (Styling files)
│       ├── admin (Admin exclusive css files)
│       └── teacher (Teacher exclusive css files)
│
├── components (Reusable components like sidebar, navbar, etc.)
│   ├── php (PHP includes)
│   └── js (JavaScript functionality)

│
└── model (Backend)
    ├── sql (Database queries)
    ├── auth (Authentication)
    └── password (Password management)
    └── config (Database configuration files)
```

## User Roles

1. **Admin**
   - Teachers and students management
   - Schedule management
   - Attendance overview
   - Hifz progress overview
   - Academics progress overview
   - Assignments and materials overview/management
   - Communication with parents
   - Communication with teachers
   - User management


2. **Quran Teacher**
   - Mark attendance
   - Update student progress
   - View class schedules
   - Communicate with parents

   **Academic Teacher** (Future implementation)
   - Mark attendance
   - Update student progress
   - View class schedules
   - Communicate with parents

3. **Parent** (Future implementation)
   - View child's academic progress
   - Set attendance
   - Communicate with teachers
   - Add notes to student records
   - Add assignments to student records
   - Upload grades


## Current Implementation Status
- Hifz progress is being implemented
- Academics progress is not yet implemented


#### Admin Section:
1. HTML Files:
   - dashboard.html
   - manage_teachers.html
   - manage_students.html
   - reports.html
   - add_new_teacher.html
   - add_new_student.html

2. CSS Files:
   - styles.css
   - modal.css

3. JavaScript Files:
   - admin_dashboard.js
   - admin_manage_teachers.js
   - admin_manage_students.js
   - admin_reports.js
   - add_new_teacher.js
   - add_new_student.js

#### Teacher Section:
1. HTML Files:
   - dashboard.html
   - attendance.html
   - progress_update.html

2. CSS Files:
   - styles.css

3. JavaScript Files:
   - teacher_dashboard.js
   - teacher_attendance.js
   - teacher_progress_update.js

### Key Features Implemented:

1. **Admin Dashboard**
   - System overview
   - Statistical data
   - Quick actions
   - User management interfaces

2. **Teacher Dashboard**
   - Class overview
   - Attendance marking
   - Progress tracking
   - Communication tools

3. **Modal Forms**
   - Add new teacher
   - Add new student
   - Edit existing records

### Design Features:
- Modern, clean interface
- Responsive design
- Consistent branding
- Accessible UI components
- Dark mode support

## Database Structure

```sql
-- Key Tables
Teachers
Students
Classes
Attendance
Progress
Users
Permissions
```

## Next Steps

1. **Frontend Development**
   - Complete remaining UI components
   - Implement real-time updates
   - Add data visualization

2. **Backend Development**
   - Set up API endpoints
   - Implement authentication
   - Database integration

3. **Features to Add**
   - Parent portal
   - Report generation
   - Notification system
   - File uploads

## Technical Stack

- Frontend: HTML5, CSS3, JavaScript
- Backend: PHP
- Database: MySQL
- Additional: Chart.js for visualizations

## Current Focus
We're currently working on enhancing the admin and teacher interfaces, particularly the forms for adding new teachers and students, and implementing the necessary backend functionality to support these features.

## Recent Updates
- Added modal forms for new teacher/student creation
- Enhanced CSS with improved responsiveness
- Implemented dark mode support
- Added form validation and error handling

## To Continue Development
1. Review existing files in the structure
2. Check the CSS files for styling guidelines
3. Follow the established naming conventions
4. Maintain consistent code formatting
5. Test new features across different devices
6. Ensure accessibility standards are met

## Project Goals
1. Streamline Hifz education management
2. Improve communication between stakeholders
3. Provide better tracking of student progress
4. Generate meaningful insights through reports
5. Create a user-friendly, efficient system

## Getting Started
1. Clone the repository or set up the file structure
2. Ensure server requirements are met (PHP, MySQL)
3. Configure database connections
4. Test existing functionality
5. Begin implementing new features