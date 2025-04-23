<?php
// Include database connection
require_once '../db_connect.php';

// Set header to return JSON
header('Content-Type: application/json');

try {
    // Prepare SQL query
    $sql = "SELECT id, title, content, created_at FROM notices ORDER BY created_at DESC";
    $result = $conn->query($sql);

    // Fetch all notices
    $notices = [];
    while ($row = $result->fetch_assoc()) {
        $notices[] = $row;
    }

    // Return success response with notices data
    echo json_encode([
        'status' => 'success',
        'notices' => $notices
    ]);
} catch (Exception $e) {
    // Return error response
    echo json_encode([
        'status' => 'error',
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
?>
