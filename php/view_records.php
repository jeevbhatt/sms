<?php
// Include database connection
require_once('db_connect.php');

// Initialize the WHERE clause and parameters array
$where_clause = [];
$params = [];
$types = "";

// Add support for search via GET
$searchTerm = isset($_GET['search']) ? trim($_GET['search']) : '';

// If this is an AJAX request for live search, return only the table rows
if (isset($_GET['ajax']) && $_GET['ajax'] == '1') {
    $searchTerm = isset($_GET['search']) ? trim($_GET['search']) : '';
    $where_clause = [];
    $params = [];
    $types = "";

    if (!empty($searchTerm)) {
        $where_clause[] = "name LIKE ?";
        $likeTerm = "%$searchTerm%";
        $params[] = $likeTerm;
        $types = "s";
    }

    $sql = "SELECT * FROM students";
    if (!empty($where_clause)) {
        $sql .= " WHERE " . implode(" AND ", $where_clause);
    }
    $sql .= " ORDER BY semester, rollno";

    $stmt = $conn->prepare($sql);
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo '<tr>
                <td>' . $row['id'] . '</td>
                <td>';
            if ($row['photo'] && file_exists('../uploads/' . $row['photo'])) {
                echo '<img src="../uploads/' . $row['photo'] . '" alt="Student Photo" class="student-photo">';
            } else {
                echo '<img src="../img/default-avatar.png" alt="No Photo" class="student-photo">';
            }
            echo '</td>
                <td>' . htmlspecialchars($row['name']) . '</td>
                <td>' . $row['semester'] . '</td>
                <td>' . htmlspecialchars($row['rollno']) . '</td>
                <td>' . htmlspecialchars($row['gender']) . '</td>
                <td>' . htmlspecialchars($row['b_ed']) . '</td>
                <td>' . htmlspecialchars($row['admitted_year']) . '</td>
            </tr>';
        }
    } else {
        echo '<tr><td colspan="8" class="no-records">No records found.</td></tr>';
    }
    $stmt->close();
    $conn->close();
    exit;
}

// Check if form is submitted (advanced filter) or search is used
if ($_SERVER["REQUEST_METHOD"] == "POST" || !empty($searchTerm)) {
    // Get form data
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (!empty($_POST['id'])) {
            $where_clause[] = "id = ?";
            $params[] = $_POST['id'];
            $types .= "i";
        }

        if (!empty($_POST['semester'])) {
            $where_clause[] = "semester = ?";
            $params[] = $_POST['semester'];
            $types .= "i";
        }

        if (!empty($_POST['rollno'])) {
            $where_clause[] = "rollno = ?";
            $params[] = $_POST['rollno'];
            $types .= "s";
        }

        if (!empty($_POST['gender'])) {
            $where_clause[] = "gender = ?";
            $params[] = $_POST['gender'];
            $types .= "s";
        }

        if (!empty($_POST['b_ed'])) {
            $where_clause[] = "b_ed = ?";
            $params[] = $_POST['b_ed'];
            $types .= "s";
        }

        if (!empty($_POST['admitted_year'])) {
            $where_clause[] = "admitted_year = ?";
            $params[] = $_POST['admitted_year'];
            $types .= "s";
        }
    }

    // If search term is provided, override where_clause for search
    if (!empty($searchTerm)) {
        $where_clause = [
            "(name LIKE ? OR rollno LIKE ? OR b_ed LIKE ? OR gender LIKE ? OR admitted_year LIKE ?)"
        ];
        $likeTerm = "%$searchTerm%";
        $params = [$likeTerm, $likeTerm, $likeTerm, $likeTerm, $likeTerm];
        $types = "sssss";
    }

    // Build the SQL query
    $sql = "SELECT * FROM students";

    if (!empty($where_clause)) {
        $sql .= " WHERE " . implode(" AND ", $where_clause);
    }

    $sql .= " ORDER BY semester, rollno";

    // Prepare and execute the statement
    $stmt = $conn->prepare($sql);

    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    // Start output
    echo '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Student Records - School Management System</title>
    <link rel="stylesheet" type="text/css" href="../css/style.css">
    <link rel="icon" type="image/x-icon" href="../img/sms.ico">
    <style>
        .records-container {
            max-width: 1000px;
            margin: 100px auto 50px;
            padding: 20px;
        }
        .records-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: #fff;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        .records-table th, .records-table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        .records-table th {
            background-color: #f8f9fa;
            font-weight: 600;
        }
        .records-table tr:hover {
            background-color: #f5f5f5;
        }
        .student-photo {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
        }
        .no-records {
            text-align: center;
            padding: 30px;
            color: #666;
        }
        .actions {
            margin-top: 20px;
            display: flex;
            justify-content: space-between;
        }
    </style>
</head>
<body>
    <div class="head">
        <ul class="nav">
            <li class="niv">
                <a href="../index.html" class="logo-link">
                    <img src="../img/sms.png" alt="SMS" id="ima">
                    <h2 class="logo">SMS</h2>
                </a>
            </li>
            <ul class="lin">
                <li><a href="../index.html#home" class="ligrad">Home</a></li>
                <li><a href="../index.html#about" class="ligrad">About</a></li>
                <li><a href="../index.html#services" class="ligrad">Services</a></li>
                <li><a href="../index.html#contac" class="ligrad">Contact</a></li>
                <li><a href="../notices.html" class="ligrad">Notices</a></li>
            </ul>
            <ul class="site">
                <li>
                    <a href="http://www.facebook.com" class="web">
                        <img src="../img/facebook.svg" alt="Facebook" id="im">
                    </a>
                </li>
                <li>
                    <a href="http://www.twitter.com" class="web">
                        <img src="../img/twitter.svg" alt="Twitter" id="im">
                    </a>
                </li>
                <li>
                    <a href="../admin/login.php" class="admin-link">
                        <img src="../img/admin.svg" alt="Admin" id="im" title="Admin Login">
                    </a>
                </li>
            </ul>
        </ul>
    </div>

    <div class="records-container">
        <h1>Student Records</h1>
        <!-- Live Search Box -->
        <input type="text" id="liveSearch" placeholder="Search student names..." style="margin-bottom:20px;display:block;width:100%;padding:8px;">
        <!-- End Live Search Box -->
        <div id="printableArea">
            <table class="records-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Photo</th>
                        <th>Name</th>
                        <th>Grade</th>
                        <th>Roll No</th>
                        <th>Gender</th>
                        <th>Subject</th>
                        <th>Admitted Year</th>
                    </tr>
                </thead>
                <tbody id="studentsTableBody">';
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo '<tr>
                            <td>' . $row['id'] . '</td>
                            <td>';
                        if ($row['photo'] && file_exists('../uploads/' . $row['photo'])) {
                            echo '<img src="../uploads/' . $row['photo'] . '" alt="Student Photo" class="student-photo">';
                        } else {
                            echo '<img src="../img/default-avatar.png" alt="No Photo" class="student-photo">';
                        }
                        echo '</td>
                            <td>' . htmlspecialchars($row['name']) . '</td>
                            <td>' . $row['semester'] . '</td>
                            <td>' . htmlspecialchars($row['rollno']) . '</td>
                            <td>' . htmlspecialchars($row['gender']) . '</td>
                            <td>' . htmlspecialchars($row['b_ed']) . '</td>
                            <td>' . htmlspecialchars($row['admitted_year']) . '</td>
                        </tr>';
                    }
                } else {
                    echo '<tr><td colspan="8" class="no-records">No records found.</td></tr>';
                }
echo '      </tbody>
            </table>
        </div>
        <div class="actions">
            <button onclick="window.history.back()">Back</button>
            <button onclick="printTable()">Print</button>
        </div>
    </div>

    <footer>
        <div class="footer-content">
            <h3>Student life is the most important life of your journey</h3>
            <p>Copyright &copy; 2024 School Management System</p>
        </div>
        <div class="marquee-bar">
            <div class="marquee-content">
                <span>📞 Phone: +1-234-567-8900 | 📧 Email: info@schoolms.com | 🌐 Website: http://www.schoolms.com</span>
            </div>
        </div>
    </footer>

    <script>
        // Live search functionality
        document.getElementById("liveSearch").addEventListener("input", function() {
            var search = this.value;
            var xhr = new XMLHttpRequest();
            xhr.open("GET", "?ajax=1&search=" + encodeURIComponent(search), true);
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    document.getElementById("studentsTableBody").innerHTML = xhr.responseText;
                }
            };
            xhr.send();
        });

        function printTable() {
            var printContents = document.getElementById("printableArea").innerHTML;
            var originalContents = document.body.innerHTML;
            document.body.innerHTML = printContents;
            window.print();
            document.body.innerHTML = originalContents;
            window.location.reload();
        }
    </script>
</body>
</html>';

    // Close statement and connection
    $stmt->close();
    $conn->close();
}
?>
