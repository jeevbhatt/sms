<?php
header('Content-Type: application/json');
require_once(__DIR__ . '/../db_connect.php');

$result = $conn->query("SELECT DISTINCT email FROM contact_messages WHERE email IS NOT NULL AND email != ''");
$emails = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $emails[] = $row['email'];
    }
}
echo json_encode(['success' => true, 'emails' => $emails]);
$conn->close();
?>
