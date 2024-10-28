<?php
session_start();
require_once '../../../model/auth/admin_auth.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../../login.php');
    exit();
}

// Check if we're editing an existing teacher
$editMode = isset($_GET['id']);
$teacherData = null;
if ($editMode) {
    require_once '../../../model/sql/admin_queries.php';
    $teacherData = getTeacherById($_GET['id']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $editMode ? 'Edit Teacher' : 'Add New Teacher'; ?> - Hifz Management System</title>
    
    <!-- CSS Files -->
    <link rel="stylesheet" href="../../css/admin/styles.css">
    <link rel="stylesheet" href="../../../components/layouts/sidebar.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <?php include '../../../components/layouts/sidebar.html'; ?>
    
    <div class="main-content">
        <h1><?php echo $editMode ? 'Edit Teacher' : 'Add New Teacher'; ?></h1>
        
        <form id="teacherForm" class="input-form">
            <input type="hidden" name="action" value="<?php echo $editMode ? 'update_teacher' : 'add_teacher'; ?>">
            <?php if ($editMode) { ?>
                <input type="hidden" name="teacher_id" value="<?php echo $_GET['id']; ?>">
            <?php } ?>

            <div class="form-section">
                <h3>Personal Information</h3>
                
                <div class="form-grid">
                    <div class="form-group">
                        <label for="firstName">First Name*</label>
                        <input type="text" id="firstName" name="firstName" required
                               value="<?php echo $editMode ? $teacherData['first_name'] : ''; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="lastName">Last Name*</label>
                        <input type="text" id="lastName" name="lastName" required
                               value="<?php echo $editMode ? $teacherData['last_name'] : ''; ?>">
                    </div>

                    <div class="form-group">
                        <label for="email">Email Address*</label>
                        <input type="email" id="email" name="email" required
                               value="<?php echo $editMode ? $teacherData['email'] : ''; ?>">
                    </div>

                    <div class="form-group">
                        <label for="phone">Phone Number*</label>
                        <input type="tel" id="phone" name="phone" required
                               value="<?php echo $editMode ? $teacherData['phone'] : ''; ?>">
                    </div>
                </div>
            </div>

            <div class="form-section">
                <h3>Professional Information</h3>
                
                <div class="form-grid">
                    <div class="form-group">
                        <label for="qualification">Qualification*</label>
                        <input type="text" id="qualification" name="qualification" required
                               value="<?php echo $editMode ? $teacherData['qualification'] : ''; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="specialization">Specialization*</label>
                        <select id="specialization" name="specialization" required>
                            <option value="">Select Specialization</option>
                            <option value="hifz" <?php echo ($editMode && $teacherData['specialization'] == 'hifz') ? 'selected' : ''; ?>>Hifz</option>
                            <option value="tajweed" <?php echo ($editMode && $teacherData['specialization'] == 'tajweed') ? 'selected' : ''; ?>>Tajweed</option>
                            <option value="qirat" <?php echo ($editMode && $teacherData['specialization'] == 'qirat') ? 'selected' : ''; ?>>Qirat</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="experience">Years of Experience*</label>
                        <input type="number" id="experience" name="experience" min="0" required
                               value="<?php echo $editMode ? $teacherData['experience'] : ''; ?>">
                    </div>

                    <div class="form-group">
                        <label for="joinDate">Join Date*</label>
                        <input type="date" id="joinDate" name="joinDate" required
                               value="<?php echo $editMode ? $teacherData['join_date'] : date('Y-m-d'); ?>">
                    </div>
                </div>
            </div>

            <div class="form-section">
                <h3>Class Assignment</h3>
                
                <div class="form-grid">
                    <div class="form-group">
                        <label>Assigned Classes</label>
                        <div class="checkbox-group">
                            <?php
                            $classes = getClasses(); // You'll need to create this function
                            $assignedClasses = $editMode ? getTeacherClasses($teacherData['id']) : [];
                            foreach($classes as $class) {
                                $checked = in_array($class['id'], $assignedClasses) ? 'checked' : '';
                                echo "<label class='checkbox-label'>
                                        <input type='checkbox' name='classes[]' value='{$class['id']}' {$checked}>
                                        {$class['name']}
                                      </label>";
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-section">
                <h3>Additional Information</h3>
                
                <div class="form-grid">
                    <div class="form-group full-width">
                        <label for="certifications">Certifications</label>
                        <textarea id="certifications" name="certifications" rows="3"><?php echo $editMode ? $teacherData['certifications'] : ''; ?></textarea>
                    </div>

                    <div class="form-group full-width">
                        <label for="notes">Additional Notes</label>
                        <textarea id="notes" name="notes" rows="3"><?php echo $editMode ? $teacherData['notes'] : ''; ?></textarea>
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <?php echo $editMode ? 'Update Teacher' : 'Add Teacher'; ?>
                </button>
                <button type="button" class="btn btn-secondary" onclick="history.back()">Cancel</button>
            </div>
        </form>
    </div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="../../../components/layouts/sidebar.js"></script>
    <script src="../../../components/js/admin_add_teacher.js"></script>
</body>
</html>