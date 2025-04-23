<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    http_response_code(403);
    exit('Unauthorized');
}
require_once('../db_connect.php');

echo '<style>
.data-table { width:100%; border-collapse:collapse; margin-top:20px; background:#fff; }
.data-table th, .data-table td { padding:12px 15px; border-bottom:1px solid #eee; text-align:left; }
.data-table th { background:#f8f9fa; font-weight:600; }
.data-table tr:hover { background:#f5f5f5; }
.no-records { text-align:center; padding:30px; color:#666; }
</style>';

echo "<h2>Contact Messages</h2>";
echo "<table class='data-table'>";
echo "<thead>
<tr>
    <th>ID</th>
    <th>Full Name</th>
    <th>Email</th>
    <th>Message</th>
    <th>Received At</th>
</tr>
</thead><tbody>";

$sql = "SELECT id, fullname, email, message, created_at FROM contact_messages ORDER BY created_at DESC";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['id']) . "</td>";
        echo "<td>" . htmlspecialchars($row['fullname']) . "</td>";
        echo "<td>" . htmlspecialchars($row['email']) . "</td>";
        echo "<td style='max-width:350px;word-break:break-word;'>" . nl2br(htmlspecialchars($row['message'])) . "</td>";
        echo "<td>" . htmlspecialchars($row['created_at']) . "</td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='5' class='no-records'>No contact messages found.</td></tr>";
}
echo "</tbody></table>";

$conn->close();
?>
