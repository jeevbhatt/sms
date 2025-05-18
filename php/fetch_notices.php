<?php
// Include database connection
require_once('db_connect.php');

// Set content type to JSON
header('Content-Type: application/json');

// Get parameters
$offset = isset($_GET['offset']) ? intval($_GET['offset']) : 0;
$limit = isset($_GET['limit']) ? intval($_GET['limit']) : 5;

// Validate parameters
if ($offset < 0) $offset = 0;
if ($limit <= 0 || $limit > 20) $limit = 5;

// Initialize notices array
$notices = [];

try {
    // Prepare SQL statement
    $stmt = $conn->prepare("SELECT id, title, content, created_at FROM notices ORDER BY created_at DESC LIMIT ?, ?");
    $stmt->bind_param("ii", $offset, $limit);

    // Execute the statement
    $stmt->execute();
    $result = $stmt->get_result();

    // Fetch all notices
    while ($row = $result->fetch_assoc()) {
        $notices[] = $row;
    }

    // Close statement
    $stmt->close();
} catch (Exception $e) {
    // Handle errors
    $notices = [];
}

// Return JSON response
echo json_encode(['success' => true, 'notices' => $notices]);

// Close connection
$conn->close();
?>
