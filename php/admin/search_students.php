<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    http_response_code(403);
    exit('Unauthorized');
}
require_once('../db_connect.php');

// Only allow sorting by id and name
$allowedSort = ['id', 'name'];
$sort = isset($_GET['sort']) && in_array($_GET['sort'], $allowedSort) ? $_GET['sort'] : 'id';
// Always use uppercase for direction and only allow ASC/DESC
$dir = (isset($_GET['dir']) && strtolower($_GET['dir']) === 'asc') ? 'ASC' : 'DESC';

$q = isset($_GET['q']) ? trim($_GET['q']) : '';
$sql = "SELECT id, name, semester, rollno, gender, b_ed, admitted_year, photo FROM students";
$params = [];
$types = '';
if ($q !== '') {
    $sql .= " WHERE name LIKE ? OR rollno LIKE ? OR semester LIKE ? OR b_ed LIKE ? OR admitted_year LIKE ?";
    $q_like = "%$q%";
    $params = [$q_like, $q_like, $q_like, $q_like, $q_like];
    $types = 'sssss';
}
// Fix: Remove backticks from direction, and always use ASC/DESC safely
$sql .= " ORDER BY `" . $sort . "` " . ($dir === 'ASC' ? 'ASC' : 'DESC') . " LIMIT 50";
$stmt = $conn->prepare($sql);
if ($params) $stmt->bind_param($types, ...$params);
$stmt->execute();
$res = $stmt->get_result();

// Inline style for rounded photo and sortable headers
echo "<style>
    .student-photo { width: 50px; height: 50px; border-radius: 50%; object-fit: cover; border: 2px solid #ccc; }
    .sortable { cursor: pointer; user-select: none; color: #007bff; text-decoration: underline; }
    .sort-arrow { font-size: 0.9em; margin-left: 2px; }
</style>";

// Helper for sort arrows
function sort_arrow($col, $sort, $dir) {
    if ($col !== $sort) return '';
    return $dir === 'ASC' ? '▲' : '▼';
}

// Helper for sort links (AJAX only, no href)
function sort_link($col, $label, $sort, $dir) {
    $nextDir = ($col === $sort && $dir === 'ASC') ? 'desc' : 'asc';
    $arrow = sort_arrow($col, $sort, $dir);
    return "<span class='sortable' onclick=\"window.sortStudentsTable && sortStudentsTable('$col', '$nextDir');\">$label <span class='sort-arrow'>$arrow</span></span>";
}

// Table header: only ID and Name are sortable
echo "<table class='data-table'><thead>
<tr>
<th>Photo</th>
<th>" . sort_link('id', 'ID', $sort, $dir) . "</th>
<th>" . sort_link('name', 'Name', $sort, $dir) . "</th>
<th>Grade</th>
<th>Roll No</th>
<th>Gender</th>
<th>Subject</th>
<th>Admitted Year</th>
</tr>
</thead><tbody>";

if ($res->num_rows > 0) {
    while ($row = $res->fetch_assoc()) {
        echo "<tr>";
        // Photo: support both BLOB (DB) and filename (uploads folder)
        if (!empty($row['photo'])) {
            if (strlen($row['photo']) < 255 && preg_match('/\.(jpg|jpeg|png)$/i', $row['photo'])) {
                // Looks like a filename, try to load from uploads
                $filepath = "../../uploads/" . $row['photo'];
                if (file_exists($filepath)) {
                    $ext = strtolower(pathinfo($filepath, PATHINFO_EXTENSION));
                    $mime = ($ext === 'png') ? 'image/png' : 'image/jpeg';
                    $img = base64_encode(file_get_contents($filepath));
                    echo "<td><img class='student-photo' src='data:$mime;base64,$img' alt='Photo'></td>";
                } else {
                    echo "<td><div class='student-photo' style='background:#eee;display:inline-block;'></div></td>";
                }
            } else {
                // Assume BLOB from DB
                $img = base64_encode($row['photo']);
                echo "<td><img class='student-photo' src='data:image/jpeg;base64,$img' alt='Photo'></td>";
            }
        } else {
            echo "<td><div class='student-photo' style='background:#eee;display:inline-block;'></div></td>";
        }
        echo "<td>" . htmlspecialchars($row['id']) . "</td>";
        echo "<td>" . htmlspecialchars($row['name']) . "</td>";
        echo "<td>" . htmlspecialchars($row['semester']) . "</td>";
        echo "<td>" . htmlspecialchars($row['rollno']) . "</td>";
        echo "<td>" . htmlspecialchars($row['gender']) . "</td>";
        echo "<td>" . htmlspecialchars($row['b_ed']) . "</td>";
        echo "<td>" . htmlspecialchars($row['admitted_year']) . "</td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='8'>No students found.</td></tr>";
}
echo "</tbody></table>";

// JS for AJAX sorting (always AJAX, never reload)
?>
<script>
window.sortStudentsTable = function(col, dir) {
    var q = '';
    var searchInput = document.getElementById('studentSearch');
    if (searchInput) q = searchInput.value;
    fetch('../php/admin/search_students.php?sort=' + encodeURIComponent(col) + '&dir=' + encodeURIComponent(dir) + '&q=' + encodeURIComponent(q))
        .then(res => res.text())
        .then(html => {
            document.getElementById('studentsList').innerHTML = html;
        });
    return false;
};
</script>
<?php
$stmt->close();
$conn->close();
?>
