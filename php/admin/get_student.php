<?php
// Include database connection
require_once '../db_connect.php';

// Set header to return JSON
header('Content-Type: application/json');

// Live search by name if 'search' parameter is provided
if (isset($_GET['search']) && !empty(trim($_GET['search']))) {
    $searchTerm = '%' . trim($_GET['search']) . '%';
    try {
        $sql = "SELECT * FROM students WHERE name LIKE ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('s', $searchTerm);
        $stmt->execute();
        $result = $stmt->get_result();
        $students = [];
        while ($row = $result->fetch_assoc()) {
            $students[] = $row;
        }
        echo json_encode([
            'status' => 'success',
            'students' => $students
        ]);
    } catch (Exception $e) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Database error: ' . $e->getMessage()
        ]);
    }
    exit;
}

// Check if ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Student ID is required'
    ]);
    exit;
}

$id = $_GET['id'];

try {
    // Prepare SQL query
    $sql = "SELECT * FROM students WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $student = $result->fetch_assoc();

    if ($student) {
        // Return success response with student data
        echo json_encode([
            'status' => 'success',
            'student' => $student
        ]);
    } else {
        // Return error if student not found
        echo json_encode([
            'status' => 'error',
            'message' => 'Student not found'
        ]);
    }
} catch (Exception $e) {
    // Return error response
    echo json_encode([
        'status' => 'error',
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
?>
