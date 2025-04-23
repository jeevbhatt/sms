<?php
// Include database connection
require_once '../db_connect.php';

// Set header to return JSON
header('Content-Type: application/json');

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $title = $_POST['title'] ?? '';
    $content = $_POST['content'] ?? '';
    $date = date('Y-m-d'); // Current date
    
    // Validate required fields
    if (empty($title) || empty($content)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Please fill all required fields'
        ]);
        exit;
    }
    
    try {
        // Prepare SQL query
        $sql = "INSERT INTO notices (title, content, date) VALUES (:title, :content, :date)";
        $stmt = $conn->prepare($sql);
        
        // Bind parameters
        $stmt->bindParam(':title', $title, PDO::PARAM_STR);
        $stmt->bindParam(':content', $content, PDO::PARAM_STR);
        $stmt->bindParam(':date', $date, PDO::PARAM_STR);
        
        // Execute the query
        $stmt->execute();
        
        // Return success response
        echo json_encode([
            'status' => 'success',
            'message' => 'Notice added successfully'
        ]);
    } catch (PDOException $e) {
        // Return error response
        echo json_encode([
            'status' => 'error',
            'message' => 'Database error: ' . $e->getMessage()
        ]);
    }
} else {
    // Return error for invalid request method
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid request method'
    ]);
}
?>
