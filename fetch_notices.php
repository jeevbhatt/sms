<?php
$offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 5;

$conn = new mysqli('localhost', 'root', '', 'sms');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT title, description, post_date FROM notices ORDER BY post_date DESC LIMIT $offset, $limit";
$result = $conn->query($sql);

while ($row = $result->fetch_assoc()) {
    echo "<div class='notice-card'>";
    echo "<h3>{$row['title']}</h3>";
    echo "<p>{$row['description']}</p>";
    echo "<p class='date'>Posted on: <em>{$row['post_date']}</em></p>";
    echo "</div>";
}

$conn->close();
?>