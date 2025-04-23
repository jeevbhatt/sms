<?php
session_start();
// Check if user is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Dashboard - School Management System</title>
    <link rel="stylesheet" type="text/css" href="/sms/css/style.css">
    <link rel="stylesheet" type="text/css" href="/sms/css/admin.css">
    <link rel="icon" type="image/x-icon" href="/sms/img/sms.ico">
    <script src="/sms/js/admin.js" defer></script>
</head>
<body class="dashboard-body">
    <div class="admin-sidebar">
        <div class="admin-profile">
            <img src="/sms/img/admin-avatar.png" alt="Admin" class="admin-avatar">
            <h3 style='color: #000;'>Welcome, <?php echo htmlspecialchars($_SESSION['admin_username']); ?></h3>
        </div>
        <nav class="admin-nav">
            <ul>
                <li><a href="#" class="active" data-section="dashboard">Dashboard</a></li>
                <li><a href="#" data-section="students">Manage Students</a></li>
                <li><a href="#" data-section="notices">Manage Notices</a></li>
                <li><a href="#" data-section="settings">Settings</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
    </div>

    <div class="admin-content">
        <header class="admin-header">
            <h2>Admin Dashboard</h2>
            <div class="admin-actions">
                <button id="toggleSidebar">☰</button>
            </div>
        </header>

        <main>
            <!-- Dashboard Section -->
            <section id="dashboard-section" class="content-section active">
                <div class="dashboard-stats">
                    <div class="stat-card">
                        <h3>Total Students</h3>
                        <p class="stat-number">
                            <?php
                            require_once(__DIR__ . '/../php/db_connect.php');
                            $result = $conn->query("SELECT COUNT(*) as count FROM students");
                            $row = $result->fetch_assoc();
                            echo $row['count'];
                            ?>
                        </p>
                    </div>
                    <div class="stat-card">
                        <h3>Total Notices</h3>
                        <p class="stat-number">
                            <?php
                            $result = $conn->query("SELECT COUNT(*) as count FROM notices");
                            $row = $result->fetch_assoc();
                            echo $row['count'];
                            ?>
                        </p>
                    </div>
                    <div class="stat-card">
                        <h3>Recent Activity</h3>
                        <p>Last login: <?php echo date("Y-m-d H:i:s"); ?></p>
                    </div>
                </div>

                <div class="recent-activity">
                    <h3>Recent Students</h3>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Grade</th>
                                <th>Added Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $result = $conn->query("SELECT id, name, semester, created_at FROM students ORDER BY created_at DESC LIMIT 5");
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>" . $row['id'] . "</td>";
                                echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                                echo "<td>" . $row['semester'] . "</td>";
                                echo "<td>" . $row['created_at'] . "</td>";
                                echo "</tr>";
                            }
                            $conn->close();
                            ?>
                        </tbody>
                    </table>
                </div>
            </section>

            <!-- Students Section -->
            <section id="students-section" class="content-section">
                <h2>Manage Students</h2>
                <div class="action-buttons">
                    <button id="addStudentBtn" class="action-btn">Add New Student</button>
                    <input type="text" id="studentSearch" placeholder="Search students by name...">
                </div>

                <div id="studentsList" class="data-list">
                    <!-- Student data will be loaded here via AJAX -->
                </div>

                <!-- Add/Edit Student Form Modal -->
                <div id="studentFormModal" class="modal">
                    <div class="modal-content">
                        <span class="close-modal">&times;</span>
                        <div id="studentFormContainer"></div>
                    </div>
                </div>
            </section>

            <!-- Notices Section -->
            <section id="notices-section" class="content-section">
                <h2>Manage Notices</h2>
                <div class="action-buttons">
                    <button id="addNoticeBtn" class="action-btn">Add New Notice</button>
                </div>

                <div id="noticesList" class="data-list">
                    <!-- Notices data will be loaded here via AJAX -->
                    <p>Loading notices data...</p>
                </div>

                <!-- Add/Edit Notice Form Modal -->
                <div id="noticeFormModal" class="modal">
                    <div class="modal-content">
                        <span class="close-modal">&times;</span>
                        <div id="noticeFormContainer"></div>
                    </div>
                </div>
            </section>

            <!-- Settings Section -->
            <section id="settings-section" class="content-section">
                <h2>Settings</h2>
                <div class="settings-form">
                    <h3>Change Password</h3>
                    <form id="changePasswordForm" action="../php/admin/change_password.php" method="post">
                        <div class="form-group">
                            <label for="currentPassword">Current Password:</label>
                            <input type="password" id="currentPassword" name="currentPassword" required>
                        </div>
                        <div class="form-group">
                            <label for="newPassword">New Password:</label>
                            <input type="password" id="newPassword" name="newPassword" required>
                        </div>
                        <div class="form-group">
                            <label for="confirmPassword">Confirm New Password:</label>
                            <input type="password" id="confirmPassword" name="confirmPassword" required>
                        </div>
                        <button type="submit" class="action-btn">Update Password</button>
                    </form>
                    <div id="passwordMessage"></div>
                </div>
            </section>
        </main>
    </div>
</body>
</html>
