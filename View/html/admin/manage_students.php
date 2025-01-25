<?php
session_start();
require_once '../../../model/auth/admin_auth.php';
require_once '../../../model/sql/admin_db.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../../login.php');
    exit();
}

// Initialize database connection and add debugging
$adminDb = new AdminDatabase();
$students = $adminDb->getAllStudents();

// Debug output
echo "<!-- Debug Information -->";
echo "<!-- Number of students found: " . count($students) . " -->";
echo "<!-- Student data: " . print_r($students, true) . " -->";

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Students - Dar-al-uloom</title>
    <link rel="stylesheet" href="../../../components/layouts/sidebar.css">
    <link rel="stylesheet" href="../../css/admin/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    
    <!-- JavaScript Files - Load before body -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="../../../components/layouts/sidebar.js"></script>
    <script src="../../../components/js/admin_manage_students.js"></script>
    <style>
        .notification {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 20px;
            border-radius: 4px;
            z-index: 1000;
            display: flex;
            align-items: center;
            gap: 10px;
            animation: slideIn 0.3s ease-out;
        }

        .notification.success {
            background-color: #4caf50;
            color: white;
        }

        .notification.error {
            background-color: #f44336;
            color: white;
        }

        .notification button {
            background: none;
            border: none;
            color: white;
            cursor: pointer;
            font-size: 20px;
            padding: 0 5px;
        }

        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
    </style>
</head>
<body>
    <?php include '../../../components/php/admin_sidebar.php'; ?>

    <div class="main-content">
        <div class="header">
            <div class="search-container">
                <input type="text" placeholder="Search..." class="search-input">
            </div>
            <div class="user-info">
                <div class="notifications">
                    <span class="badge">1</span>
                    <i class="fas fa-bell"></i>
                </div>
                <div class="user">
                    <img src="/assets/admin-avatar.png" alt="Admin" class="avatar">
                    <span>Admin User</span>
                </div>
            </div>
        </div>

        <div class="content-header">
            <div class="title-section">
                <h1>Users Management</h1>
                <p>Manage all users and their roles</p>
            </div>
            <button class="btn-primary" onclick="window.location.href='add_new_student.php'">
                <i class="fas fa-plus"></i> Add User
            </button>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Student Name</th>
                        <th>Guardian</th>
                        <th>Latest Lesson</th>
                        <th>Lines Memorized</th>
                        <th>Surah Progress</th>
                        <th>Quality</th>
                        <th>Tajweed</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($students)): ?>
                        <tr>
                            <td colspan="11" class="no-data">No students found</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach($students as $student): ?>
                            <tr>
                                <td class="name-cell">
                                    <div class="user-icon">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    <div class="user-details">
                                        <div class="user-name"><?php echo htmlspecialchars($student['name']); ?></div>
                                    </div>
                                </td>
                                <td>
                                    <div class="user-details">
                                        <div class="user-name"><?php echo htmlspecialchars($student['guardian_name']); ?></div>
                                        <div class="user-contact"><?php echo htmlspecialchars($student['guardian_contact']); ?></div>
                                    </div>
                                </td>
                                <td>
                                    <?php if(isset($student['lesson_date'])): ?>
                                        <div class="lesson-info">
                                            <div class="date"><?php echo date('Y-m-d', strtotime($student['lesson_date'])); ?></div>
                                            <div class="surah"><?php echo htmlspecialchars($student['surah_name']); ?></div>
                                            <div class="verses">Verses: <?php echo $student['verse_start']; ?>-<?php echo $student['verse_end']; ?></div>
                                        </div>
                                    <?php else: ?>
                                        <span class="no-lesson">No lesson recorded</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="lines-info">
                                        <span class="lines-count"><?php echo htmlspecialchars($student['lines_memorized'] ?? '0'); ?></span>
                                        <span class="lines-label">lines</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="completion-progress">
                                        <div class="progress-bar">
                                            <div class="progress" style="width: <?php echo (($student['completed_juz'] ?? 0) / 30) * 100; ?>%"></div>
                                        </div>
                                        <span class="completion-text"><?php echo htmlspecialchars($student['completed_juz'] ?? '0'); ?>/30</span>
                                    </div>
                                </td>
                                <td>
                                    <span class="quality-badge <?php echo strtolower($student['memorization_quality'] ?? 'none'); ?>">
                                        <?php echo ucfirst($student['memorization_quality'] ?? 'Not Rated'); ?>
                                    </span>
                                </td>
                                <td><?php echo ucfirst($student['tajweed_level'] ?? 'Not Set'); ?></td>
                                <td>
                                    <span class="status-badge <?php echo strtolower($student['status']); ?>">
                                        <?php echo ucfirst($student['status']); ?>
                                    </span>
                                </td>
                                <td class="actions">
                                    <button type="button" class="btn-icon edit" data-student-id="<?php echo $student['id']; ?>">
                                        <i class="fas fa-pen"></i>
                                    </button>
                                    <button type="button" class="btn-icon delete" data-student-id="<?php echo $student['id']; ?>">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div id="editStudentModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Edit Student Progress</h2>
                <span class="close">&times;</span>
            </div>
            <div class="modal-body">
                <form id="editStudentForm">
                    <input type="hidden" id="studentId" name="student_id">
                    <input type="hidden" name="action" value="update_student">
                    
                    <div class="form-group">
                        <label for="surahName">Surah Name</label>
                        <input type="text" id="surahName" name="surahName" required>
                    </div>

                    <div class="form-group">
                        <label for="verseStart">Starting Verse</label>
                        <input type="number" id="verseStart" name="verseStart" required min="1">
                    </div>

                    <div class="form-group">
                        <label for="verseEnd">Ending Verse</label>
                        <input type="number" id="verseEnd" name="verseEnd" required min="1">
                    </div>

                    <div class="form-group">
                        <label for="linesMemorized">Lines Memorized</label>
                        <input type="number" id="linesMemorized" name="linesMemorized" required min="0">
                    </div>

                    <div class="form-group">
                        <label for="lessonDate">Lesson Date</label>
                        <input type="date" id="lessonDate" name="lessonDate" required>
                    </div>

                    <div class="form-group">
                        <label for="memorizationQuality">Memorization Quality</label>
                        <select id="memorizationQuality" name="memorizationQuality">
                            <option value="excellent">Excellent</option>
                            <option value="good">Good</option>
                            <option value="average">Average</option>
                            <option value="needsWork">Needs Work</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="tajweedLevel">Tajweed Level</label>
                        <select id="tajweedLevel" name="tajweedLevel">
                            <option value="beginner">Beginner</option>
                            <option value="intermediate">Intermediate</option>
                            <option value="advanced">Advanced</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="revisionStatus">Revision Status</label>
                        <select id="revisionStatus" name="revisionStatus">
                            <option value="onTrack">On Track</option>
                            <option value="needsRevision">Needs Revision</option>
                            <option value="behind">Behind Schedule</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="teacherNotes">Teacher Notes</label>
                        <textarea id="teacherNotes" name="teacherNotes" rows="4"></textarea>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Update Progress</button>
                        <button type="button" class="btn btn-secondary" onclick="closeModal()">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>