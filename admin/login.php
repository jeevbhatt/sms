<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Login - School Management System</title>
    <link rel="stylesheet" type="text/css" href="../css/style.css">
    <link rel="stylesheet" type="text/css" href="../css/admin.css">
    <link rel="icon" type="image/x-icon" href="../img/sms.ico">
</head>
<body class="admin-body">
    <div class="admin-container">
        <div class="admin-login-form">
            <div class="admin-header">
                <img src="../img/sms.png" alt="SMS" class="admin-logo">
                <h2>Admin Login</h2>
            </div>

            <?php
            session_start();

            // Check if form is submitted
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                require_once('../php/db_connect.php');

                $username = $_POST['username'];
                $password = $_POST['password'];

                // Prepare SQL statement to prevent SQL injection
                $stmt = $conn->prepare("SELECT id, username, password FROM admin WHERE username = ?");
                $stmt->bind_param("s", $username);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows == 1) {
                    $row = $result->fetch_assoc();
                    // Verify password
                    if (password_verify($password, $row['password'])) {
                        $_SESSION['admin_id'] = $row['id'];
                        $_SESSION['admin_username'] = $row['username'];
                        $_SESSION['role'] = 'admin'; // For admin login
                        header("Location: dashboard.php");
                        exit();
                    } else {
                        echo "<p class='error-message'>Invalid username or password</p>";
                    }
                } else {
                    echo "<p class='error-message'>Invalid username or password</p>";
                }

                $stmt->close();
                $conn->close();
            }
            ?>

            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <div class="form-group">
                    <label for="username">Username:</label>
                    <input type="text" id="username" name="username" required>
                </div>
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <button type="submit" class="admin-btn">Login</button>
            </form>
            <div class="admin-footer">
                <a href="../index.html">Back to Home</a>
            </div>
        </div>
    </div>
</body>
</html>
