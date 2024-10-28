Source
│
├── View
│   ├── html
│   │   ├── admin
│   │   │   ├── dashboard.html
│   │   │   ├── manage_teachers.html
│   │   │   ├── manage_students.html
│   │   │   └── reports.html
│   │   └── teacher
│   │       ├── dashboard.html
│   │       ├── attendance.html
│   │       └── progress_update.html
│   └── css
│       ├── admin
│       │   └── styles.css
│       └── teacher
│           └── styles.css
│
├── components
│   ├── php
│   │   ├── admin_header.php
│   │   ├── admin_footer.php
│   │   ├── admin_sidebar.php
│   │   ├── teacher_header.php
│   │   ├── teacher_footer.php
│   │   └── teacher_sidebar.php
│   └── js
│       ├── admin_dashboard.js
│       ├── admin_manage_teachers.js
│       ├── admin_manage_students.js
│       ├── admin_reports.js
│       ├── teacher_dashboard.js
│       ├── teacher_attendance.js
│       └── teacher_progress_update.js
│
└── model
    ├── sql
    │   ├── admin_queries.sql
    │   └── teacher_queries.sql
    ├── auth
    │   ├── admin_auth.php
    │   └── teacher_auth.php
    └── password
        ├── admin_password_reset.php
        └── teacher_password_reset.php