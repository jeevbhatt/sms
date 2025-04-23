<?php
require_once '../db_connect.php';
header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $created_at = date('Y-m-d H:i:s');

    if (empty($title) || empty($content)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Title and content are required.'
        ]);
        exit;
    }

    try {
        $stmt = $conn->prepare("INSERT INTO notices (title, content, created_at) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $title, $content, $created_at);
        if ($stmt->execute()) {
            echo json_encode([
                'status' => 'success',
                'message' => 'Notice added successfully.'
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Failed to add notice.'
            ]);
        }
        $stmt->close();
    } catch (Exception $e) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Database error: ' . $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid request method.'
    ]);
}
?>
