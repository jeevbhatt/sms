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
    // Prepare SQL query
    $sql = "SELECT * FROM notices WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    
    // Execute the query
    $stmt->execute();
    
    // Fetch notice data
    $notice = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($notice) {
        // Return success response with notice data
        echo json_encode([
            'status' => 'success',
            'notice' => $notice
        ]);
    } else {
        // Return error if notice not found
        echo json_encode([
            'status' => 'error',
            'message' => 'Notice not found'
        ]);
    }
} catch (PDOException $e) {
    // Return error response
    echo json_encode([
        'status' => 'error',
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
?>
