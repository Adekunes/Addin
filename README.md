# Source_folder_final_v2
A detailed overview for an administrative dashboard tailored for managing Hifz classes at Dar Al-'Ulum Montréal; this overview should encompass key elements such as a dashboard for vital statistics and recent activities, robust student management features, teacher management tools for scheduling and assignments, Hifz progress tracking mechanisms, attendance monitoring systems, and class organization capabilities, while also addressing admin responsibilities, user interface design principles, and the technical architecture using relevant programming languages and technologies like HTML, CSS, JavaScript, and any other necessary frameworks.





SoSource
│
├── View
│   ├── html
│   │   ├── admin
│   │   │   ├── dashboard.html
│   │   │   ├── manage_teachers.html
│   │   │   ├── manage_students.html
│   │   │   ├── reports.html
│   │   │   ├── add_new_teacher.html    // New
│   │   │   └── add_new_student.html    // New
│   │   └── teacher
│   │       ├── dashboard.html
│   │       ├── attendance.html
│   │       └── progress_update.html
│   └── css
│       ├── admin
│       │   ├── styles.css
│       │   └── modal.css               // New
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
│       ├── add_new_teacher.js         // New
│       ├── add_new_student.js         // New
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

































Source
│
├── View
│   ├── html
│   │   ├── admin
│   │   └── teacher
│   └── css
│       ├── admin
│       └── teacher
│
├── components
│   ├── php
│   └── js
│
└── model
    ├── sql
    ├── auth
    └── password
