<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Teacher - Hifz Management System</title>
    <link rel="stylesheet" href="../../css/admin/styles.css">
</head>
<body>
    <?php include '../../components/php/admin_header.php'; ?>
    <?php include '../../components/php/admin_sidebar.php'; ?>

    <div class="main-content">
        <div class="container">
            <h2>Add New Teacher</h2>
            <form id="addTeacherForm" action="../model/auth/admin_auth.php" method="POST">
                <!-- Personal Information -->
                <div class="form-section">
                    <h3>Personal Information</h3>
                    <div class="form-group">
                        <label for="firstName">First Name*</label>
                        <input type="text" id="firstName" name="firstName" required>
                    </div>
                    <div class="form-group">
                        <label for="lastName">Last Name*</label>
                        <input type="text" id="lastName" name="lastName" required>
                    </div>
                    <div class="form-group">
                        <label for="dateOfBirth">Date of Birth*</label>
                        <input type="date" id="dateOfBirth" name="dateOfBirth" required>
                    </div>
                    <div class="form-group">
                        <label for="gender">Gender*</label>
                        <select id="gender" name="gender" required>
                            <option value="">Select Gender</option>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                        </select>
                    </div>
                </div>

                <!-- Contact Information -->
                <div class="form-section">
                    <h3>Contact Information</h3>
                    <div class="form-group">
                        <label for="email">Email Address*</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="phone">Phone Number*</label>
                        <input type="tel" id="phone" name="phone" required>
                    </div>
                    <div class="form-group">
                        <label for="address">Address</label>
                        <textarea id="address" name="address" rows="3"></textarea>
                    </div>
                </div>

                <!-- Professional Information -->
                <div class="form-section">
                    <h3>Professional Information</h3>
                    <div class="form-group">
                        <label for="qualification">Qualification*</label>
                        <input type="text" id="qualification" name="qualification" required>
                    </div>
                    <div class="form-group">
                        <label for="experience">Years of Experience*</label>
                        <input type="number" id="experience" name="experience" min="0" required>
                    </div>
                    <div class="form-group">
                        <label for="specialization">Specialization*</label>
                        <select id="specialization" name="specialization" required>
                            <option value="">Select Specialization</option>
                            <option value="hifz">Hifz</option>
                            <option value="tajweed">Tajweed</option>
                            <option value="qirat">Qirat</option>
                        </select>
                    </div>
                </div>

                <!-- Employment Information -->
                <div class="form-section">
                    <h3>Employment Information</h3>
                    <div class="form-group">
                        <label for="joinDate">Join Date*</label>
                        <input type="date" id="joinDate" name="joinDate" required>
                    </div>
                    <div class="form-group">
                        <label for="employmentType">Employment Type*</label>
                        <select id="employmentType" name="employmentType" required>
                            <option value="">Select Type</option>
                            <option value="fullTime">Full Time</option>
                            <option value="partTime">Part Time</option>
                            <option value="contract">Contract</option>
                        </select>
                    </div>
                </div>

                <!-- Additional Information -->
                <div class="form-section">
                    <h3>Additional Information</h3>
                    <div class="form-group">
                        <label for="certifications">Certifications</label>
                        <textarea id="certifications" name="certifications" rows="3"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="notes">Additional Notes</label>
                        <textarea id="notes" name="notes" rows="3"></textarea>
                    </div>
                </div>

                <div class="form-buttons">
                    <button type="submit" class="btn-primary">Add Teacher</button>
                    <button type="reset" class="btn-secondary">Clear Form</button>
                </div>
            </form>
        </div>
    </div>

    <?php include '../../components/php/admin_footer.php'; ?>
    <script src="../../components/js/admin_manage_teachers.js"></script>
</body>
</html>