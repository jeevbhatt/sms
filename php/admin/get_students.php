<?php
// Include database connection
require_once '../db_connect.php';

// Set header to return JSON
header('Content-Type: application/json');

// Get search term if provided
$searchTerm = isset($_GET['search']) ? trim($_GET['search']) : '';

try {
    // Prepare SQL query with search functionality
    if (!empty($searchTerm)) {
        $sql = "SELECT * FROM students WHERE
                name LIKE ? OR
                rollno LIKE ? OR
                b_ed LIKE ? OR
                gender LIKE ? OR
                admitted_year LIKE ?
                ORDER BY id DESC";
        $stmt = $conn->prepare($sql);
        $likeTerm = "%$searchTerm%";
        $stmt->bind_param('sssss', $likeTerm, $likeTerm, $likeTerm, $likeTerm, $likeTerm);
    } else {
        $sql = "SELECT * FROM students ORDER BY id DESC";
        $stmt = $conn->prepare($sql);
    }

    // Execute the query
    $stmt->execute();
    $result = $stmt->get_result();
    $students = [];
    while ($row = $result->fetch_assoc()) {
        $students[] = $row;
    }

    // Return success response with students data
    echo json_encode([
        'status' => 'success',
        'students' => $students
    ]);
} catch (Exception $e) {
    // Return error response
    echo json_encode([
        'status' => 'error',
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
?>
