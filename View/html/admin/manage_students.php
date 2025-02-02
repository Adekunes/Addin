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

        .quality-badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.85em;
            font-weight: 500;
        }

        .quality-badge.excellent {
            background-color: #d4edda;
            color: #155724;
        }

        .quality-badge.good {
            background-color: #cce5ff;
            color: #004085;
        }

        .quality-badge.average {
            background-color: #fff3cd;
            color: #856404;
        }

        .quality-badge.needswork {
            background-color: #f8d7da;
            color: #721c24;
        }

        .quality-badge.not-rated {
            background-color: #e2e3e5;
            color: #383d41;
        }

        .form-group select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            background-color: white;
            font-size: 14px;
            cursor: pointer;
        }

        .form-group select:focus {
            border-color: #2ecc71;
            outline: none;
            box-shadow: 0 0 0 2px rgba(46, 204, 113, 0.2);
        }

        .form-group select option {
            padding: 8px;
        }

        .form-group select:hover {
            border-color: #2ecc71;
        }

        .progress-container {
            width: 100%;
            background-color: #f0f0f0;
            border-radius: 4px;
            position: relative;
            height: 20px;
            overflow: hidden;
        }

        .progress-bar {
            height: 100%;
            background-color: #4CAF50;
            transition: width 0.3s ease;
            border-radius: 4px;
            position: absolute;
            left: 0;
            top: 0;
        }

        .progress-text {
            position: absolute;
            width: 100%;
            text-align: center;
            color: #000;
            font-size: 12px;
            line-height: 20px;
            z-index: 1;
            text-shadow: 
                -1px -1px 0 #fff,
                1px -1px 0 #fff,
                -1px 1px 0 #fff,
                1px 1px 0 #fff;
        }

        /* Progress bar color variations based on progress */
        .progress-bar[style*="width: 100%"] {
            background-color: #4CAF50; /* Full - Green */
        }

        .progress-bar[style*="width: 6"] { /* 60-99% */
            background-color: #2196F3; /* Blue */
        }

        .progress-bar[style*="width: 3"] { /* 30-59% */
            background-color: #FF9800; /* Orange */
        }

        .progress-bar[style*="width: 1"],
        .progress-bar[style*="width: 2"] { /* 0-29% */
            background-color: #f44336; /* Red */
        }

        /* Style for ayat select dropdowns */
.form-group select[id^="startAyat"],
.form-group select[id^="endAyat"] {
    max-height: 200px;
    overflow-y: auto;
}

/* Fix modal positioning for dropdowns */
.modal-content {
    overflow: visible !important;
}

.form-group select {
    position: relative;
    z-index: 1050; /* Higher than modal's z-index */
}

/* Make select options visible when dropdown is open */
.form-group select option {
    padding: 8px 12px;
    background-color: white;
}

/* Add scrollbar styling */
.form-group select::-webkit-scrollbar {
    width: 8px;
}

.form-group select::-webkit-scrollbar-track {
    background: #f1f1f1;
}

.form-group select::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 4px;
}

.form-group select::-webkit-scrollbar-thumb:hover {
    background: #555;
}
/* History Tab Styling */
.history-sections {
    display: flex;
    flex-direction: column;
    gap: 2rem;
    padding: 1rem;
}

.history-section {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    overflow: hidden;
}

.history-section h3 {
    background: #f8f9fa;
    margin: 0;
    padding: 1rem;
    border-bottom: 1px solid #e9ecef;
    color: #495057;
    font-size: 1.1rem;
}

.history-table-container {
    padding: 1rem;
    overflow-x: auto;
}

.history-table {
    width: 100%;
    border-collapse: collapse;
    margin: 0;
}

.history-table th {
    background: #f8f9fa;
    color: #495057;
    font-weight: 600;
    text-align: left;
    padding: 12px;
    border-bottom: 2px solid #dee2e6;
}

.history-table td {
    padding: 12px;
    border-bottom: 1px solid #e9ecef;
    color: #212529;
}

.history-table tr:hover {
    background-color: #f8f9fa;
}

.quality-badge {
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 0.9em;
    font-weight: 500;
}

.quality-badge.excellent {
    background-color: #d4edda;
    color: #155724;
}

.quality-badge.good {
    background-color: #cce5ff;
    color: #004085;
}

.quality-badge.average {
    background-color: #fff3cd;
    color: #856404;
}

.quality-badge.needswork {
    background-color: #f8d7da;
    color: #721c24;
}

.quality-badge.horrible {
    background-color: #dc3545;
    color: white;
}

.no-data {
    text-align: center;
    color: #6c757d;
    font-style: italic;
    padding: 2rem !important;
}

/* Responsive Design */
@media (max-width: 768px) {
    .history-table th, 
    .history-table td {
        padding: 8px;
    }
    
    .quality-badge {
        padding: 2px 6px;
        font-size: 0.8em;
    }
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

        <div class="table-responsive">
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
                    <input type="hidden" name="action" value="update_student">
                    
                    <div class="form-section">
                        <h3>Juz Progress</h3>
                        <div class="form-group">
                            <label for="currentJuz">Current Juz</label>
                            <select id="currentJuz" name="currentJuz" required>
                                <option value="">Select Current Juz</option>
                                <?php for($i = 1; $i <= 30; $i++): ?>
                                    <option value="<?php echo $i; ?>" <?php echo (isset($student['current_juz']) && $student['current_juz'] == $i) ? 'selected' : ''; ?>>
                                        Juz <?php echo $i; ?>
                                    </option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="memorizationQuality">Quality Rating</label>
                            <select name="memorizationQuality" id="memorizationQuality" class="form-control" required>
                                <option value="excellent">Excellent</option>
                                <option value="good">Good</option>
                                <option value="average" selected>Average</option>
                                <option value="needsWork">Needs Work</option>
                                <option value="horrible">Horrible</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="teacherNotes">Notes</label>
                            <textarea id="teacherNotes" name="teacherNotes" rows="3"></textarea>
                        </div>

                        <div class="form-group">
                            <label for="currentSurah">Current Surah</label>
                            <select id="currentSurah" name="currentSurah" class="form-control" required>
                                <option value="">Select Surah</option>
                                <?php
                                // Surahs will be populated dynamically based on selected juz
                                ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="startAyat">Starting Ayat</label>
                            <select id="startAyat" name="startAyat" class="form-control" required>
                                <option value="">Select Starting Ayat</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="endAyat">Last Memorized Ayat</label>
                            <select id="endAyat" name="endAyat" class="form-control" required>
                                <option value="">Select Ending Ayat</option>
                            </select>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn-primary">Save Changes</button>
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