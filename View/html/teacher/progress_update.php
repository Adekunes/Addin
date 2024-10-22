<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Progress - Dar Al-'Ulum Montréal</title>
    <link rel="stylesheet" href="../../../View/css/teacher/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <button class="menu-toggle">
        <i class="fas fa-bars"></i>
    </button>

    <?php require_once('../../../components/php/teacher_sidebar.php'); ?>

    <div class="main-content">
        <div class="progress-section">
            <h1>Update Student Progress</h1>

            <form id="progressForm" class="progress-form">
                <div class="form-group">
                    <label for="studentSelect">Select Student:</label>
                    <select id="studentSelect" required>
                        <option value="">Choose a student</option>
                        <!-- Students will be populated dynamically -->
                    </select>
                </div>

                <div class="progress-details">
                    <div class="form-group">
                        <label for="currentSurah">Current Surah:</label>
                        <input type="number" id="currentSurah" min="1" max="114" required>
                    </div>

                    <div class="form-group">
                        <label for="versesMemorized">Verses Memorized:</label>
                        <input type="number" id="versesMemorized" min="0" required>
                    </div>

                    <div class="form-group">
                        <label for="qualityRating">Quality Rating:</label>
                        <select id="qualityRating" required>
                            <option value="excellent">Excellent</option>
                            <option value="good">Good</option>
                            <option value="average">Average</option>
                            <option value="needsWork">Needs Work</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="teacherNotes">Notes:</label>
                        <textarea id="teacherNotes" rows="4"></textarea>
                    </div>
                </div>

                <div class="previous-progress">
                    <h3>Previous Progress</h3>
                    <div class="progress-history" id="progressHistory">
                        <!-- Progress history will be populated dynamically -->
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Save Progress
                    </button>
                    <button type="reset" class="btn btn-secondary">
                        <i class="fas fa-undo"></i> Reset
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="../../../components/js/teacher_progress_update.js"></script>
    <script>
        document.querySelector('.menu-toggle').addEventListener('click', function() {
            document.querySelector('.sidebar').classList.toggle('active');
        });
    </script>
</body>
</html>