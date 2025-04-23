<?php
// Include database connection
require_once '../db_connect.php';

// Set header to return JSON
header('Content-Type: application/json');

try {
    // Prepare SQL query
    $sql = "SELECT * FROM notices ORDER BY date DESC";
    $stmt = $conn->prepare($sql);
    
    // Execute the query
    $stmt->execute();
    
    // Fetch all notices
    $notices = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Return success response with notices data
    echo json_encode([
        'status' => 'success',
        'notices' => $notices
    ]);
} catch (PDOException $e) {
    // Return error response
    echo json_encode([
        'status' => 'error',
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
?>
