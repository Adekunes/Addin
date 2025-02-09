<?php
session_start();
require_once '../../../model/auth/admin_auth.php';
require_once '../../../model/sql/admin_db.php';


if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../../login.php');
    exit();
}

// Check if we're editing an existing student
$editMode = isset($_GET['id']);
$studentData = null;
if ($editMode) {
    $adminDb = new AdminDatabase();
    $studentData = $adminDb->getStudentById($_GET['id']);
    
    if (!$studentData) {
        // Handle the case where student is not found
        header('Location: ../../manage_students.php');
        exit();

    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $editMode ? 'Edit Student' : 'Add New Student'; ?> - Hifz Management System</title>
    
    <!-- CSS Files -->
    <link rel="stylesheet" href="../../css/admin/styles.css">
    <link rel="stylesheet" href="../../../components/layouts/sidebar.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        /* Additional styles for form enhancement */
        .input-form {
            background-color: #ffffff;
            border-radius: 8px;
            padding: 2rem;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            max-width: 800px;
            margin: 0 auto;
        }
        .form-section {
            margin-bottom: 2rem;
        }
        .form-section h3 {
            margin-bottom: 1rem;
            color: #333;
        }
        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
        }
        .form-group {
            display: flex;
            flex-direction: column;
        }
        .form-group label {
            margin-bottom: 0.5rem;
            font-weight: bold;
        }
        .form-group input, .form-group select, .form-group textarea {
            padding: 0.75rem;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 1rem;
        }
        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 1rem;
            margin-top: 1.5rem;
        }
        .btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1rem;
            transition: background-color 0.3s, transform 0.2s;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .btn-primary {
            background-color: #28a745;
            color: white;
        }
        .btn-primary:hover {
            background-color: #218838;
            transform: translateY(-2px);
        }
        .btn-secondary {
            background-color: #6c757d;
            color: white;
        }
        .btn-secondary:hover {
            background-color: #5a6268;
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <?php include '../../../components/php/sidebar.php'; ?>
    
    <div class="main-content">
        <h1><?php echo $editMode ? 'Edit Student' : 'Add New Student'; ?></h1>
        
        <form id="studentForm" class="input-form" method="POST">
            <input type="hidden" name="action" value="<?php echo $editMode ? 'update_student' : 'add_student'; ?>">
            <?php if ($editMode) { ?>
                <input type="hidden" name="student_id" value="<?php echo $_GET['id']; ?>">
            <?php } ?>

            <div class="form-section">
                <h3>Personal Information</h3>
                
                <div class="form-grid">
                    <div class="form-group">
                        <label for="firstName">First Name*</label>
                        <input type="text" id="firstName" name="firstName" required 
                               value="<?php echo $editMode ? $studentData['first_name'] : ''; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="lastName">Last Name*</label>
                        <input type="text" id="lastName" name="lastName" required
                               value="<?php echo $editMode ? $studentData['last_name'] : ''; ?>">
                    </div>

                    <div class="form-group">
                        <label for="dateOfBirth">Date of Birth*</label>
                        <input type="date" id="dateOfBirth" name="dateOfBirth" required
                               value="<?php echo $editMode ? $studentData['date_of_birth'] : ''; ?>">
                    </div>

                    <div class="form-group">
                        <label for="gender">Gender*</label>
                        <select id="gender" name="gender" required>
                            <option value="">Select Gender</option>
                            <option value="M" <?php echo ($editMode && $studentData['gender'] == 'M') ? 'selected' : ''; ?>>Male</option>
                            <option value="F" <?php echo ($editMode && $studentData['gender'] == 'F') ? 'selected' : ''; ?>>Female</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="form-section">
                <h3>Contact Information</h3>
                
                <div class="form-grid">
                    <div class="form-group">
                        <label for="guardianName">Guardian Name*</label>
                        <input type="text" id="guardianName" name="guardianName" required
                               value="<?php echo $editMode ? $studentData['guardian_name'] : ''; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="guardianPhone">Guardian Phone*</label>
                        <input type="tel" id="guardianPhone" name="guardianPhone" required
                               value="<?php echo $editMode ? $studentData['guardian_phone'] : ''; ?>">
                    </div>

                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email"
                               value="<?php echo $editMode ? $studentData['email'] : ''; ?>">
                    </div>

                    <div class="form-group full-width">
                        <label for="address">Address</label>
                        <textarea id="address" name="address" rows="3"><?php echo $editMode ? $studentData['address'] : ''; ?></textarea>
                    </div>
                </div>
            </div>

            <div class="form-section">
                <h3>Academic Information</h3>
                
                <div class="form-grid">
                    <div class="form-group">
                        <label for="currentJuz">Current Juz*</label>
                        <select id="currentJuz" name="currentJuz" required>
                            <option value="">Select Current Juz</option>
                            <?php for($i = 1; $i <= 30; $i++): ?>
                                <option value="<?php echo $i; ?>" <?php echo ($editMode && $studentData['current_juz'] == $i) ? 'selected' : ''; ?>>
                                    Juz <?php echo $i; ?>
                                </option>
                            <?php endfor; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="completedJuz">Completed Juz*</label>
                        <select id="completedJuz" name="completedJuz" required>
                            <option value="">Select Completed Juz</option>
                            <?php for($i = 0; $i <= 30; $i++): ?>
                                <option value="<?php echo $i; ?>" <?php echo ($editMode && $studentData['completed_juz'] == $i) ? 'selected' : ''; ?>>
                                    <?php echo $i; ?>/30
                                </option>
                            <?php endfor; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="lastCompletedSurah">Last Completed Surah</label>
                        <input type="text" id="lastCompletedSurah" name="lastCompletedSurah"
                               value="<?php echo $editMode ? $studentData['last_completed_surah'] : ''; ?>"
                               placeholder="e.g., Al-Baqarah">
                    </div>

                    <div class="form-group">
                        <label for="memorizationQuality">Memorization Quality*</label>
                        <select id="memorizationQuality" name="memorizationQuality" required>
                            <option value="">Select Quality</option>
                            <option value="excellent" <?php echo ($editMode && $studentData['memorization_quality'] == 'excellent') ? 'selected' : ''; ?>>Excellent</option>
                            <option value="good" <?php echo ($editMode && $studentData['memorization_quality'] == 'good') ? 'selected' : ''; ?>>Good</option>
                            <option value="average" <?php echo ($editMode && $studentData['memorization_quality'] == 'average') ? 'selected' : ''; ?>>Average</option>
                            <option value="needsWork" <?php echo ($editMode && $studentData['memorization_quality'] == 'needsWork') ? 'selected' : ''; ?>>Needs Work</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="tajweedLevel">Tajweed Level*</label>
                        <select id="tajweedLevel" name="tajweedLevel" required>
                            <option value="">Select Tajweed Level</option>
                            <option value="beginner" <?php echo ($editMode && $studentData['tajweed_level'] == 'beginner') ? 'selected' : ''; ?>>Beginner</option>
                            <option value="intermediate" <?php echo ($editMode && $studentData['tajweed_level'] == 'intermediate') ? 'selected' : ''; ?>>Intermediate</option>
                            <option value="advanced" <?php echo ($editMode && $studentData['tajweed_level'] == 'advanced') ? 'selected' : ''; ?>>Advanced</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="revisionStatus">Revision Status</label>
                        <select id="revisionStatus" name="revisionStatus">
                            <option value="">Select Status</option>
                            <option value="onTrack" <?php echo ($editMode && $studentData['revision_status'] == 'onTrack') ? 'selected' : ''; ?>>On Track</option>
                            <option value="needsRevision" <?php echo ($editMode && $studentData['revision_status'] == 'needsRevision') ? 'selected' : ''; ?>>Needs Revision</option>
                            <option value="behind" <?php echo ($editMode && $studentData['revision_status'] == 'behind') ? 'selected' : ''; ?>>Behind Schedule</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="lastRevisionDate">Last Revision Date</label>
                        <input type="date" id="lastRevisionDate" name="lastRevisionDate"
                               value="<?php echo $editMode ? $studentData['last_revision_date'] : date('Y-m-d'); ?>">
                    </div>

                    <div class="form-group full-width">
                        <label for="teacherNotes">Teacher Notes</label>
                        <textarea id="teacherNotes" name="teacherNotes" rows="4"
                                  placeholder="Add any additional notes about the student's progress"><?php echo $editMode ? $studentData['teacher_notes'] : ''; ?></textarea>
                    </div>
                </div>
            </div>

            <div class="form-section">
                <h3>Quran Progress</h3>
                
                <div class="form-grid">
                    <div class="form-group">
                        <label for="quranLevel">Current Quran Level</label>
                        <input type="text" id="quranLevel" name="quranLevel"
                               value="<?php echo $editMode ? $studentData['quran_level'] : ''; ?>">
                    </div>
                </div>
            </div>

            <div class="form-section">
                <h3>Additional Information</h3>
                
                <div class="form-grid">
                    <div class="form-group full-width">
                        <label for="medicalInfo">Medical Information</label>
                        <textarea id="medicalInfo" name="medicalInfo" rows="3"><?php echo $editMode ? $studentData['medical_info'] : ''; ?></textarea>
                    </div>

                    <div class="form-group full-width">
                        <label for="notes">Additional Notes</label>
                        <textarea id="notes" name="notes" rows="3"><?php echo $editMode ? $studentData['notes'] : ''; ?></textarea>
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <?php echo $editMode ? 'Update Student' : 'Add Student'; ?>
                </button>
                <button type="button" class="btn btn-secondary" onclick="history.back()">Cancel</button>
            </div>
        </form>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="../../../components/layouts/sidebar.js"></script>
    <script src="../../../components/js/admin_add_student.js"></script>
</body>
</html>