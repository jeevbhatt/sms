<?php
// Include database connection
require_once('db_connect.php');

// Set header to return JSON for AJAX requests
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
    header('Content-Type: application/json');
}

// Check if the request is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get student ID
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;

    // Validate input
    if ($id <= 0) {
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            echo json_encode([
                'success' => false,
                'message' => 'Invalid student ID.'
            ]);
        } else {
            header('Location: ../index.html?error=invalid_id');
        }
        exit;
    }

    try {
        // Get student photo before deleting
        $query = "SELECT photo FROM students WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $student = $result->fetch_assoc();

        // Delete student from database
        $query = "DELETE FROM students WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('i', $id);
        $result = $stmt->execute();

        if ($result) {
            // Delete photo file if exists
            if ($student && !empty($student['photo'])) {
                $photoPath = "../uploads/" . $student['photo'];
                if (file_exists($photoPath)) {
                    unlink($photoPath);
                }
            }

            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                echo json_encode([
                    'success' => true,
                    'message' => 'Student deleted successfully!'
                ]);
            } else {
                header('Location: ../index.html?success=deleted');
            }
        } else {
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                echo json_encode([
                    'success' => false,
                    'message' => 'Failed to delete student.'
                ]);
            } else {
                header('Location: ../index.html?error=delete_failed');
            }
        }
    } catch (Exception $e) {
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            echo json_encode([
                'success' => false,
                'message' => 'Database error: ' . $e->getMessage()
            ]);
        } else {
            header('Location: ../index.html?error=database');
        }
    }
} else {
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
        echo json_encode([
            'success' => false,
            'message' => 'Invalid request method.'
        ]);
    } else {
        header('Location: ../index.html?error=invalid_method');
    }
}
?>
