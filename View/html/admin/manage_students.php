<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Students - Dar Al-'Ulum Montréal</title>
    <link rel="stylesheet" href="../../../View/css/admin/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <button class="menu-toggle">
        <i class="fas fa-bars"></i>
    </button>

    <?php include '../../../components/php/admin_sidebar.php'; ?>

    <div class="main-content">
        <div class="student-management">
            <h1>Manage Students</h1>
            <button id="addStudentBtn" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add New Student
            </button>
            
            <div class="table-container">
                <table id="studentsTable">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Class</th>
                            <th>Guardian</th>
                            <th>Progress</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Student data will be populated here -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="../../../components/js/admin_manage_students.js"></script>
    <script>
        document.querySelector('.menu-toggle').addEventListener('click', function() {
            document.querySelector('.sidebar').classList.toggle('active');
        });
    </script>
</body>
</html>