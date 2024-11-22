<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Dar Al-'Ulum Montréal</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        :root {
            --primary-color: #006747;
            --secondary-color: #FFA500;
            --background-color: #F4F7FA;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background-color: var(--background-color);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-container {
            background: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }

        .logo-container {
            text-align: center;
            margin-bottom: 2rem;
        }

        .logo {
            max-width: 150px;
            height: auto;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--primary-color);
        }

        input {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
        }

        .btn {
            width: 100%;
            padding: 0.75rem;
            background-color: var(--primary-color);
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 1rem;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .btn:hover {
            background-color: var(--secondary-color);
        }

        .error-message {
            background-color: #ffebee;
            color: #c62828;
            padding: 0.75rem;
            border-radius: 4px;
            margin-bottom: 1rem;
            text-align: center;
        }

        .role-toggle {
            text-align: center;
            margin-top: 1rem;
        }

        .role-toggle a {
            color: var(--primary-color);
            text-decoration: none;
        }

        .role-toggle a:hover {
            text-decoration: underline;
        }

        .success-message {
            background-color: #e8f5e9;
            color: #2e7d32;
            padding: 0.75rem;
            border-radius: 4px;
            margin-bottom: 1rem;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="logo-container">
            <img src="../../../assets/images/logo.png" alt="Dar Al-'Ulum Montréal Logo" class="logo">
            <h1>Welcome Back</h1>
        </div>
<!-- /**testing */bjb -->
        <?php
        if (isset($_GET['message'])) {
            echo '<div class="success-message">' . htmlspecialchars($_GET['message']) . '</div>';
        }
        if (isset($_GET['error'])) {
            echo '<div class="error-message">' . htmlspecialchars($_GET['error']) . '</div>';
        }
        if (isset($_GET['success'])) {
            echo '<div class="success-message">Login successful!</div>';
            echo '<script>console.log("Login successful!");</script>';
        }
        ?>

        <form action="../../model/auth/process_login.php" method="POST">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>

            <div class="form-group">
                <label for="role">Login As</label>
                <select id="role" name="role" required>
                    <option value="admin">Admin</option>
                    <option value="teacher">Teacher</option>
                </select>
            </div>

            <button type="submit" class="btn">Login</button>
        </form>

        <div class="role-toggle">
            <a href="forgot-password.php">Forgot Password?</a>
        </div>
    </div>
</body>
</html>