<?php
session_start();
// Check if user is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

// Include database connection
require_once('../db_connect.php');

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $currentPassword = $_POST['currentPassword'];
    $newPassword = $_POST['newPassword'];
    $confirmPassword = $_POST['confirmPassword'];
    
    // Validate input
    if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'All fields are required']);
        exit();
    }
    
    if ($newPassword !== $confirmPassword) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'New passwords do not match']);
        exit();
    }
    
    // Get current admin data
    $stmt = $conn->prepare("SELECT password FROM admin WHERE id = ?");
    $stmt->bind_param("i", $_SESSION['admin_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        // Verify current password
        if (password_verify($currentPassword, $row['password'])) {
            // Hash new password
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            
            // Update password
            $update_stmt = $conn->prepare("UPDATE admin SET password = ? WHERE id = ?");
            $update_stmt->bind_param("si", $hashedPassword, $_SESSION['admin_id']);
            
            if ($update_stmt->execute()) {
                header('Content-Type: application/json');
                echo json_encode(['success' => true, 'message' => 'Password updated successfully']);
            } else {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Error updating password: ' . $update_stmt->error]);
            }
            
            $update_stmt->close();
        } else {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Current password is incorrect']);
        }
    } else {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Admin not found']);
    }
    
    // Close statement and connection
    $stmt->close();
    $conn->close();
}
?>