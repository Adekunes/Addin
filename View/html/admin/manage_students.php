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

        .juz-info {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }
        
        .current-juz {
            font-weight: bold;
            color: #2c3e50;
        }
        
        .mastery-level {
            font-size: 0.9em;
            color: #666;
        }
        
        .revision-info {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }
        
        .revision-date {
            color: #2c3e50;
        }
        
        .revision-count {
            font-size: 0.9em;
            color: #666;
        }
        
        .no-revision {
            color: #999;
            font-style: italic;
        }
        
        .form-section {
            margin-bottom: 1.5rem;
            padding: 1rem;
            background: #f8f9fa;
            border-radius: 4px;
        }
        
        .form-section h3 {
            margin-bottom: 1rem;
            color: #2c3e50;
        }

        .modal-tabs {
            display: flex;
            border-bottom: 1px solid #dee2e6;
            margin-bottom: 1rem;
        }

        .tab-button {
            padding: 0.75rem 1rem;
            border: none;
            background: none;
            cursor: pointer;
            font-size: 1rem;
            color: #6c757d;
            border-bottom: 2px solid transparent;
            transition: all 0.3s ease;
        }

        .tab-button.active {
            color: #2c3e50;
            border-bottom-color: #3498db;
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }

        .history-section {
            margin-bottom: 2rem;
        }

        .history-table-container {
            max-height: 300px;
            overflow-y: auto;
            border: 1px solid #dee2e6;
            border-radius: 4px;
        }

        .history-table {
            width: 100%;
            border-collapse: collapse;
        }

        .history-table th,
        .history-table td {
            padding: 0.75rem;
            border-bottom: 1px solid #dee2e6;
            text-align: left;
        }

        .history-table th {
            background-color: #f8f9fa;
            position: sticky;
            top: 0;
            z-index: 1;
        }

        .history-table tbody tr:hover {
            background-color: #f8f9fa;
        }

        .quality-indicator {
            padding: 0.25rem 0.5rem;
            border-radius: 3px;
            font-size: 0.875rem;
        }

        .quality-excellent { background-color: #d4edda; color: #155724; }
        .quality-good { background-color: #fff3cd; color: #856404; }
        .quality-average { background-color: #fff3cd; color: #856404; }
        .quality-needsWork { background-color: #f8d7da; color: #721c24; }

        .search-container {
            position: relative;
            display: flex;
            align-items: center;
            max-width: 400px;
            width: 100%;
        }

        .search-input {
            width: 100%;
            padding: 10px 40px 10px 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
            transition: border-color 0.3s ease;
        }

        .search-input:focus {
            border-color: #3498db;
            outline: none;
        }

        .search-icon {
            position: absolute;
            right: 15px;
            color: #666;
        }

        .clear-search {
            position: absolute;
            right: 40px;
            background: none;
            border: none;
            color: #666;
            cursor: pointer;
            font-size: 18px;
            padding: 0 5px;
            display: none;
        }

        .clear-search:hover {
            color: #333;
        }

        .no-results {
            text-align: center;
            padding: 20px;
            color: #666;
            font-style: italic;
        }
    </style>
</head>
<body>
    <?php include '../../../components/php/admin_sidebar.php'; ?>

    <div class="main-content">
        <div class="header">
            <div class="search-container">
                <input type="text" id="studentSearch" placeholder="Search by name, guardian, or juz..." class="search-input">
                <div class="search-icon">
                    <i class="fas fa-search"></i>
                </div>
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
                <h1>Students Management</h1>
                <p>Manage all students</p>
            </div>
            <button class="btn-primary" onclick="window.location.href='add_new_student.php'">
                <i class="fas fa-plus"></i> Add Student
            </button>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Student Name</th>
                        <th>Guardian</th>
                        <th>Current Juz</th>
                        <th>Lines Memorized</th>
                        <th>Juz Progress</th>
                        <th>Last Revision</th>
                        <th>Quality</th>
                        <th>Tajweed</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($students)): ?>
                        <tr>
                            <td colspan="11" class="no-data">No students available</td>
                        </tr>
                    <?php else: ?>
                        <tr class="no-results" style="display: none;">
                            <td colspan="11">No matching students found</td>
                        </tr>
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
                                <td class="juz-info">
                                    <div class="current-juz">Juz <?php echo htmlspecialchars($student['current_juz']); ?></div>
                                    <div class="mastery-level"><?php echo ucfirst($student['mastery_level'] ?? 'Not Started'); ?></div>
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
                                <td class="revision-info">
                                    <?php if(isset($student['last_revision_date'])): ?>
                                        <div class="revision-date"><?php echo date('Y-m-d', strtotime($student['last_revision_date'])); ?></div>
                                        <div class="revision-count">
                                            Revisions: <?php echo $student['revision_count'] ?? 0; ?>
                                        </div>
                                    <?php else: ?>
                                        <span class="no-revision">No revisions</span>
                                    <?php endif; ?>
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
                <h2>Student Progress Management</h2>
                <span class="close">&times;</span>
            </div>
            <div class="modal-tabs">
                <button class="tab-button active" data-tab="memorization">Memorization Progress</button>
                <button class="tab-button" data-tab="revision">Daily Revision</button>
                <button class="tab-button" data-tab="history">History</button>
            </div>
            <div class="modal-body">
                <form id="memorizationForm" class="tab-content active" data-tab="memorization">
                    <input type="hidden" id="studentId" name="student_id">
                    <input type="hidden" name="action" value="update_student">
                    
                    <div class="form-section">
                        <h3>Juz Progress</h3>
                        <div class="form-group">
                            <label for="currentJuz">Current Juz</label>
                            <input type="number" id="currentJuz" name="currentJuz" min="1" max="30" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="masteryLevel">Mastery Level</label>
                            <select id="masteryLevel" name="masteryLevel" required>
                                <option value="not_started">Not Started</option>
                                <option value="in_progress">In Progress</option>
                                <option value="memorized">Memorized</option>
                                <option value="mastered">Mastered</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="memorizationQuality">Overall Memorization Quality</label>
                        <select id="memorizationQuality" name="memorizationQuality" required>
                            <option value="excellent">Excellent</option>
                            <option value="good">Good</option>
                            <option value="average">Average</option>
                            <option value="needsWork">Needs Work</option>
                        </select>
                    </div>

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
                        <label for="tajweedLevel">Tajweed Level</label>
                        <select id="tajweedLevel" name="tajweedLevel" required>
                            <option value="beginner">Beginner</option>
                            <option value="intermediate">Intermediate</option>
                            <option value="advanced">Advanced</option>
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

                <form id="revisionForm" class="tab-content" data-tab="revision">
                    <input type="hidden" id="studentId" name="student_id">
                    <input type="hidden" name="action" value="add_revision">
                    
                    <div class="form-section">
                        <h3>Today's Revision</h3>
                        <div class="form-group">
                            <label for="revisionJuz">Juz Revised</label>
                            <input type="number" id="juzRevised" name="juzRevised" min="1" max="30" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="revisionQuality">Revision Quality</label>
                            <select id="revisionQuality" name="revisionQuality" required>
                                <option value="excellent">Excellent</option>
                                <option value="good">Good</option>
                                <option value="average">Average</option>
                                <option value="needsWork">Needs Work</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="revisionNotes">Revision Notes</label>
                            <textarea id="revisionNotes" name="revisionNotes" rows="3"></textarea>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Record Revision</button>
                        <button type="button" class="btn btn-secondary" onclick="closeModal()">Cancel</button>
                    </div>
                </form>

                <div id="historyTab" class="tab-content" data-tab="history">
                    <div class="history-section">
                        <h3>Revision History</h3>
                        <div class="history-table-container">
                            <table class="history-table revision-history">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Juz</th>
                                        <th>Quality</th>
                                        <th>Notes</th>
                                    </tr>
                                </thead>
                                <tbody id="revisionHistoryBody">
                                    <!-- Will be populated by JavaScript -->
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="history-section">
                        <h3>Memorization Progress</h3>
                        <div class="history-table-container">
                            <table class="history-table memorization-history">
                                <thead>
                                    <tr>
                                        <th>Juz</th>
                                        <th>Status</th>
                                        <th>Last Updated</th>
                                        <th>Revisions</th>
                                    </tr>
                                </thead>
                                <tbody id="memorizationHistoryBody">
                                    <!-- Will be populated by JavaScript -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tabButtons = document.querySelectorAll('.tab-button');
            const tabContents = document.querySelectorAll('.tab-content');

            tabButtons.forEach(button => {
                button.addEventListener('click', () => {
                    const tab = button.dataset.tab;

                    // Update active states
                    tabButtons.forEach(btn => btn.classList.remove('active'));
                    tabContents.forEach(content => content.classList.remove('active'));

                    button.classList.add('active');
                    document.querySelector(`.tab-content[data-tab="${tab}"]`).classList.add('active');
                });
            });
        });
    </script>
</body>
</html>