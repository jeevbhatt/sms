<?php
header('Content-Type: application/json');

$conn = new mysqli('localhost', 'root', '', 'school_management');
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'error' => 'DB connection failed']);
    exit;
}

$offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 5; // Use 5 for frontend infinite scroll

$sql = "SELECT id, title, content, created_at FROM notices ORDER BY created_at DESC LIMIT ?, ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $offset, $limit);
$stmt->execute();
$result = $stmt->get_result();

$notices = [];
while ($row = $result->fetch_assoc()) {
    $notices[] = [
        'id' => $row['id'],
        'title' => $row['title'],
        'content' => $row['content'],
        'created_at' => $row['created_at']
    ];
}

// Always return a valid JSON structure for the JS
echo json_encode([
    'success' => true,
    'notices' => $notices
]);

$stmt->close();
$conn->close();
?>
