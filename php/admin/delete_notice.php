<?php
// Include database connection
require_once '../db_connect.php';

// Set header to return JSON
header('Content-Type: application/json');

// Check if ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Notice ID is required'
    ]);
    exit;
}

$id = $_GET['id'];

try {
    // Check if notice exists
    $checkSql = "SELECT id FROM notices WHERE id = :id";
    $checkStmt = $conn->prepare($checkSql);
    $checkStmt->bindParam(':id', $id, PDO::PARAM_INT);
    $checkStmt->execute();
    
    if ($checkStmt->rowCount() === 0) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Notice not found'
        ]);
        exit;
    }
    
    // Prepare SQL query
    $sql = "DELETE FROM notices WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    
    // Execute the query
    $stmt->execute();
    
    // Return success response
    echo json_encode([
        'status' => 'success',
        'message' => 'Notice deleted successfully'
    ]);
} catch (PDOException $e) {
    // Return error response
    echo json_encode([
        'status' => 'error',
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
?>
