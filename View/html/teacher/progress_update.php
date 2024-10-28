<?php
session_start();
require_once '../../../model/auth/teacher_auth.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
    header('Location: ../../login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Progress Update - Hifz Management System</title>
    
    <!-- CSS Files -->
    <link rel="stylesheet" href="../../css/teacher/styles.css">
    <link rel="stylesheet" href="../../../components/layouts/sidebar.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <?php include '../../../components/layouts/sidebar.html'; ?>
    
    <div class="main-content">
        <h1>Progress Update</h1>

        <div class="progress-container">
            <div class="student-selector">
                <select id="studentSelect">
                    <?php
                    require_once '../../../model/sql/teacher_queries.php';
                    $students = getTeacherStudents($_SESSION['user_id']);
                    foreach($students as $student) {
                        echo "<option value='{$student['id']}'>{$student['name']}</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="quran-progress">
                <div class="surah-selection">
                    <select id="surahSelect">
                        <!-- Will be populated with Surahs -->
                    </select>
                    <div class="verse-range">
                        <input type="number" id="startVerse" placeholder="Start Verse">
                        <input type="number" id="endVerse" placeholder="End Verse">
                    </div>
                </div>

                <div class="assessment">
                    <h3>Assessment</h3>
                    <div class="assessment-criteria">
                        <div class="criterion">
                            <label>Memorization Quality</label>
                            <input type="range" min="1" max="10" id="memorizationScore">
                        </div>
                        <div class="criterion">
                            <label>Tajweed</label>
                            <input type="range" min="1" max="10" id="tajweedScore">
                        </div>
                        <div class="criterion">
                            <label>Fluency</label>
                            <input type="range" min="1" max="10" id="fluencyScore">
                        </div>
                    </div>
                    
                    <div class="notes-section">
                        <textarea id="progressNotes" placeholder="Add notes about the student's progress..."></textarea>
                    </div>

                    <button onclick="saveProgress()" class="btn btn-primary">
                        Save Progress
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="../../../components/layouts/sidebar.js"></script>
    <script src="../../../components/js/teacher_progress_update.js"></script>
</body>
</html>