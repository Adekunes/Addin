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
│   └── css 

│
└── model (Backend)
    ├── sql (Database queries)
    ├── auth (Authentication)
    └── password (Password management)
    └── config (Database configuration files)
    └── db_requests (Database requests)
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



### Key Features Implemented:

1. **Admin Dashboard**
   - System overview
   - Statistical data
   - CRUD operations for teachers and students
   - User management interfaces

2. **Teacher Dashboard**
   - Class overview
   - Attendance marking
   - Progress tracking
   - CRUD operations for student progress
   - Communication tools

3. **Modal Forms**
   - Add new teacher
   - Add new student
   - Edit existing records


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

- Frontend: HTML5, CSS, JavaScript
- Backend: PHP
- Database: MySQL
- Additional: Chart.js for visualizations (future implementation) , Ajax for real time updates (future implementation)

## Current Focus
We're currently working on implement essential features for the teachers' usage. We are also restructuring our codebase to better organize futre feature emplementations. 

## Recent Updates
- Created the admin and teacher sections
- Gave the admin and teachers the ability for CRUD operations
- Enhanced CSS with improved responsiveness
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
2. Provide better tracking of student progress
3. Generate meaningful insights through reports
4. Create a user-friendly, efficient system

