<?php
// Include database connection
require_once '../db_connect.php';

// Set header to return JSON
header('Content-Type: application/json');

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $id = $_POST['id'] ?? '';
    $title = $_POST['title'] ?? '';
    $content = $_POST['content'] ?? '';

    // Validate required fields
    if (empty($id) || empty($title) || empty($content)) {
        echo json_encode([
            'success' => false,
            'error' => 'Please fill all required fields'
        ]);
        exit;
    }

    try {
        // Check if notice exists
        $checkSql = "SELECT id FROM notices WHERE id = :id";
        $checkStmt = $conn->prepare($checkSql);
        $checkStmt->bindParam(':id', $id, PDO::PARAM_INT);
        $checkStmt->execute();

        if ($checkStmt->rowCount() === 0) {
            echo json_encode([
                'success' => false,
                'error' => 'Notice not found'
            ]);
            exit;
        }

        // Prepare SQL query
        $sql = "UPDATE notices SET title = :title, content = :content WHERE id = :id";
        $stmt = $conn->prepare($sql);

        // Bind parameters
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':title', $title, PDO::PARAM_STR);
        $stmt->bindParam(':content', $content, PDO::PARAM_STR);

        // Execute the query
        $stmt->execute();

        // Return success response
        echo json_encode([
            'success' => true
        ]);
    } catch (PDOException $e) {
        // Return error response
        echo json_encode([
            'success' => false,
            'error' => 'Database error: ' . $e->getMessage()
        ]);
    }
} else {
    // Return error for invalid request method
    echo json_encode([
        'success' => false,
        'error' => 'Invalid request method'
    ]);
}
?>
