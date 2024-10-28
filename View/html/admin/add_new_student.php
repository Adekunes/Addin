<?php
session_start();
require_once '../../../model/auth/admin_auth.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../../login.php');
    exit();
}

// Check if we're editing an existing student
$editMode = isset($_GET['id']);
$studentData = null;
if ($editMode) {
    require_once '../../../model/sql/admin_queries.php';
    $studentData = getStudentById($_GET['id']);
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
</head>
<body>
    <?php include '../../../components/layouts/sidebar.html'; ?>
    
    <div class="main-content">
        <h1><?php echo $editMode ? 'Edit Student' : 'Add New Student'; ?></h1>
        
        <form id="studentForm" class="input-form">
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
                        <label for="class">Class/Level*</label>
                        <select id="class" name="class" required>
                            <option value="">Select Class</option>
                            <?php
                            $classes = getClasses(); // You'll need to create this function
                            foreach($classes as $class) {
                                $selected = ($editMode && $studentData['class_id'] == $class['id']) ? 'selected' : '';
                                echo "<option value='{$class['id']}' {$selected}>{$class['name']}</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="startDate">Start Date*</label>
                        <input type="date" id="startDate" name="startDate" required
                               value="<?php echo $editMode ? $studentData['start_date'] : date('Y-m-d'); ?>">
                    </div>

                    <div class="form-group">
                        <label for="previousMadrasa">Previous Madrasa</label>
                        <input type="text" id="previousMadrasa" name="previousMadrasa"
                               value="<?php echo $editMode ? $studentData['previous_madrasa'] : ''; ?>">
                    </div>

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