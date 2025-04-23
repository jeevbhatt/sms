<?php
// Include database connection
require_once '../db_connect.php';

// Set header to return JSON for AJAX
$isAjax = (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest');
if ($isAjax) {
    header('Content-Type: application/json');
}

// Check if ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    if ($isAjax) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Student ID is required'
        ]);
    } else {
        echo '<div style="padding:2em;color:red;">Student ID is required.</div>';
    }
    exit;
}

$id = intval($_GET['id']);

try {
    // Get student info before deleting
    $sql = "SELECT * FROM students WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $student = $result->fetch_assoc();

    if (!$student) {
        if ($isAjax) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Student not found'
            ]);
        } else {
            echo '<div style="padding:2em;color:red;">Student not found.</div>';
        }
        exit;
    }

    // Delete student
    $sql = "DELETE FROM students WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $id);
    $stmt->execute();

    // Delete photo file if exists
    if (!empty($student['photo'])) {
        $photoPath = '../../uploads/' . $student['photo'];
        if (file_exists($photoPath)) {
            unlink($photoPath);
        }
    }

    if ($isAjax) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Student deleted successfully',
            'student' => $student
        ]);
    } else {
        // HTML confirmation page
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="utf-8">
            <title>Student Deleted - School Management System</title>
            <link rel="stylesheet" type="text/css" href="../../css/style.css">
            <link rel="icon" type="image/x-icon" href="../../img/sms.ico">
            <style>
                body { background: #f8fafc; font-family: Arial, sans-serif; }
                .container { max-width: 500px; margin: 80px auto; background: #fff; border-radius: 8px; box-shadow: 0 4px 16px rgba(0,0,0,0.08); padding: 32px; text-align: center; }
                .student-photo { width: 80px; height: 80px; border-radius: 50%; object-fit: cover; margin-bottom: 16px; }
                .success { color: #27ae60; font-size: 1.3em; margin-bottom: 18px; }
                .info-table { margin: 0 auto 20px; text-align: left; }
                .info-table td { padding: 4px 8px; }
                .back-btn { background: #4CAF50; color: #fff; border: none; border-radius: 4px; padding: 10px 24px; font-size: 1em; cursor: pointer; margin-top: 16px; }
                .back-btn:hover { background: #388e3c; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="success">Student deleted successfully!</div>
                <?php if (!empty($student['photo']) && file_exists('../../uploads/' . $student['photo'])): ?>
                    <img src="../../uploads/<?php echo htmlspecialchars($student['photo']); ?>" class="student-photo" alt="Photo">
                <?php else: ?>
                    <img src="../../img/default-avatar.png" class="student-photo" alt="No Photo">
                <?php endif; ?>
                <table class="info-table">
                    <tr><td><b>Name:</b></td><td><?php echo htmlspecialchars($student['name']); ?></td></tr>
                    <tr><td><b>ID:</b></td><td><?php echo $student['id']; ?></td></tr>
                    <tr><td><b>Grade:</b></td><td><?php echo htmlspecialchars($student['semester']); ?></td></tr>
                    <tr><td><b>Roll No:</b></td><td><?php echo htmlspecialchars($student['rollno']); ?></td></tr>
                    <tr><td><b>Gender:</b></td><td><?php echo htmlspecialchars($student['gender']); ?></td></tr>
                    <tr><td><b>Subject:</b></td><td><?php echo htmlspecialchars($student['b_ed']); ?></td></tr>
                    <tr><td><b>Admitted Year:</b></td><td><?php echo htmlspecialchars($student['admitted_year']); ?></td></tr>
                </table>
                <button class="back-btn" onclick="window.location.href='../../index.html#services'">Back to Home Page</button>
            </div>
        </body>
        </html>
        <?php
    }
} catch (Exception $e) {
    if ($isAjax) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Database error: ' . $e->getMessage()
        ]);
    } else {
        echo '<div style="padding:2em;color:red;">Database error: ' . htmlspecialchars($e->getMessage()) . '</div>';
    }
}
?>
