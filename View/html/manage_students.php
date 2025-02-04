<?php
session_start();
require_once '../../model/auth/admin_auth.php';
require_once '../../model/sql/admin_db.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin'&& $_SESSION['role'] !== 'teacher') {
    header('Location: ../login.php');
    exit();
}

// Initialize database connection and add debugging
$adminDb = new AdminDatabase();
$students = $adminDb->getAllStudents();

// Debug output
error_log("Number of students fetched: " . ($students ? count($students) : 0));
error_log("Student data: " . print_r($students, true));

// Add visible debug output in HTML comments
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
    <link rel="stylesheet" href="../../components/layouts/sidebar.css">
    <link rel="stylesheet" href="../../View/css/manage_students.css">
    <link rel="stylesheet" href="../css/admin/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    
    <!-- JavaScript Files - Load before body -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="../../components/layouts/sidebar.js"></script>
    <script src="../../components/js/admin_manage_students.js"></script>
    <style>
      
    </style>
</head>
<body>
    <?php include '../../components/php/sidebar.php'; ?>

    <div class="main-content">
        <div class="header">
            <div class="search-container">
                <input type="text" id="searchInput" placeholder="Search by name, guardian, or juz..." class="search-input">
                <button id="clearSearch" class="clear-search" style="display: none;">&times;</button>
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
            <!-- Add Student button - only show for admin -->
            <?php if ($_SESSION['role'] === 'admin'): ?>
                <div class="action-buttons">
                    <button class="btn-primary" onclick="window.location.href='../html/admin/add_new_student.php'">
                        <i class="fas fa-plus"></i> Add Student
                    </button>
                </div>
            <?php endif; ?>
        </div>

        <div class="table-container">
            <div class="search-container">
                <input type="text" id="searchInput" class="search-input" placeholder="Search students...">
                <i class="fas fa-search search-icon"></i>
                <button type="button" class="clear-search" id="clearSearch">&times;</button>
            </div>
            <table id="studentsTable" class="display">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Current Surah</th>
                        <th>Progress</th>
                        <th>Quality Rating</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($students && count($students) > 0): ?>
                        <?php foreach ($students as $student): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($student['name']); ?></td>
                                <td><?php echo htmlspecialchars($student['current_surah'] ?? 'Not started'); ?></td>
                                <td>
                                    <div class="progress-container">
                                        <div class="progress-bar" 
                                             style="width: <?php echo ($student['current_juz'] / 30) * 100; ?>%"
                                             title="<?php echo $student['current_juz']; ?> of 30 Juz">
                                        </div>
                                        <span class="progress-text">
                                            <?php echo $student['current_juz']; ?>/30 Juz
                                        </span>
                                    </div>
                                </td>
                                <td>
                                    <?php if (isset($student['quality_rating'])): ?>
                                        <span class="quality-badge <?php echo strtolower($student['quality_rating']); ?>">
                                            <?php echo htmlspecialchars($student['quality_rating']); ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="quality-badge not-rated">Not rated</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="status-badge <?php echo strtolower($student['status']); ?>">
                                        <?php echo htmlspecialchars($student['status']); ?>
                                    </span>
                                </td>
                                <td class="actions">
                                    <button 
                                        type="button"
                                        class="btn-icon edit"
                                        data-student-id="<?php echo (int)$student['id']; ?>"
                                        title="Edit Student"
                                    >
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button 
                                        type="button"
                                        class="btn-icon status"
                                        data-student-id="<?php echo (int)$student['id']; ?>"
                                        onclick="toggleStudentStatus(<?php echo (int)$student['id']; ?>, '<?php echo htmlspecialchars($student['status']); ?>')"
                                        title="<?php echo $student['status'] === 'active' ? 'Deactivate' : 'Activate'; ?> Student"
                                    >
                                        <i class="fas fa-power-off"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center">No students found</td>
                        </tr>
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
                <button class="tab-button" data-tab="sabaq-para">Sabaq Para</button>
                <button class="tab-button" data-tab="history">History</button>
            </div>
            <div class="modal-body">
                <form id="memorizationForm" class="tab-content active" data-tab="memorization">
                    <input type="hidden" id="studentId" name="student_id">
                    <input type="hidden" name="action" value="update_progress">
                    
                    <div class="form-section">
                        <h3>Juz Progress</h3>
                        <div class="form-group">
                            <label for="currentJuz">Current Juz</label>
                            <select id="currentJuz" name="current_juz" required>
                                <option value="">Select Current Juz</option>
                                <?php for($i = 1; $i <= 30; $i++): ?>
                                    <option value="<?php echo $i; ?>">
                                        Juz <?php echo $i; ?>
                                    </option>
                                <?php endfor; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="currentSurah">Current Surah</label>
                            <select id="currentSurah" name="current_surah">
                                <option value="">Select Surah</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="startAyat">Starting Ayat</label>
                            <select id="startAyat" name="start_ayat">
                                <option value="">Select Starting Ayat</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="endAyat">Ending Ayat</label>
                            <select id="endAyat" name="end_ayat">
                                <option value="">Select Ending Ayat</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="memorizationQuality">Memorization Quality</label>
                            <select id="memorizationQuality" name="memorization_quality" required>
                                <option value="excellent">Excellent</option>
                                <option value="good">Good</option>
                                <option value="average">Average</option>
                                <option value="needs_improvement">Needs Improvement</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="teacherNotes">Notes</label>
                            <textarea id="teacherNotes" name="teacher_notes"></textarea>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn-primary">Save Progress</button>
                            <button type="button" class="btn-secondary" onclick="closeModal()">Cancel</button>
                        </div>
                    </div>
                </form>

                <form id="revisionForm" class="tab-content" data-tab="revision">
                    <input type="hidden" name="student_id" value="">
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

                <form id="sabaqParaForm" class="tab-content" data-tab="sabaq-para">
                    <input type="hidden" name="student_id" value="">
                    <input type="hidden" name="action" value="add_sabaq_para">
                    
                    <div class="form-section">
                        <h3>Sabaq Para (Short Term Revision)</h3>
                        <div class="form-group">
                            <label for="sabaqJuz">Juz Number</label>
                            <select id="sabaqJuz" name="sabaqJuz" required>
                                <option value="">Select Juz</option>
                                <?php for($i = 1; $i <= 30; $i++): ?>
                                    <option value="<?php echo $i; ?>">Juz <?php echo $i; ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="quartersRevised">Quarters Revised</label>
                            <select id="quartersRevised" name="quartersRevised" required>
                                <option value="">Select Quarters</option>
                                <option value="1st_quarter">First Quarter</option>
                                <option value="2_quarters">Two Quarters</option>
                                <option value="3_quarters">Three Quarters</option>
                                <option value="4_quarters">Full Juz</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="sabaqQuality">Quality Rating</label>
                            <select id="sabaqQuality" name="sabaqQuality" required>
                                <option value="excellent">Excellent</option>
                                <option value="good">Good</option>
                                <option value="average">Average</option>
                                <option value="needsWork">Needs Work</option>
                                <option value="horrible">Horrible</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="sabaqNotes">Notes</label>
                            <textarea id="sabaqNotes" name="sabaqNotes" rows="3"></textarea>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn-primary">Save Sabaq Para</button>
                            <button type="button" class="btn-secondary" onclick="closeModal()">Cancel</button>
                        </div>
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