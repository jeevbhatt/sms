<?php
include 'connection.php';

// Display header
echo "<div class='header'>
    <img src='logo.png' alt='Logo' class='logo'>
    <h1>Student Management System</h1>
</div>";

// HTML structure and styles
echo '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Table Example</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #ffe259;  /* fallback for old browsers */
background: -webkit-linear-gradient(to right, #ffa751, #ffe259);  /* Chrome 10-25, Safari 5.1-6 */
background: linear-gradient(to right, #ffa751, #ffe259); /* W3C, IE 10+/ Edge, Firefox 16+, Chrome 26+, Opera 12+, Safari 7+ */
        }
        table {
            border-collapse: collapse;
            width: 80%;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        button {
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
    </style>
    <script>
        function printTable(printableId) {
            const printableContent = document.getElementById(printableId).innerHTML;
            const printWindow = window.open("", "_blank", "width=800,height=600");
            printWindow.document.write(`
                <html>
                <head>
                    <title>Print Table</title>
                    <style>
                        body { font-family: Arial, sans-serif; margin: 20px; }
                        table { border-collapse: collapse; width: 100%; }
                        th, td { border: 1px solid #000; padding: 8px; text-align: left; }
                        th { background-color: #f2f2f2; }
                    </style>
                </head>
                <body>
                    ${printableContent}
                </body>
                </html>
            `);
            printWindow.document.close();
            printWindow.focus();
            printWindow.print();
            printWindow.close();
        }
    </script>
</head>
<body>';

// Main functionality
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $semester = $_POST['semester'] ?? null;
    $action = $_POST['form'] ?? null;

    // Validate semester input
    if (!is_numeric($semester) && strpos($semester, ',') === false) {
        die("Invalid semester input. Enter a single semester or multiple separated by commas (e.g., 1,2).");
    }

    $semesters = array_map('trim', explode(',', $semester));
    $results = [];

    foreach ($semesters as $sem) {
        if (!is_numeric($sem) || $sem < 1 || $sem > 8) {
            die("Invalid semester value: $sem. Semester must be between 1 and 8.");
        }

        $table_name = "semester_" . $sem;

        // Check if table exists
        $check_table = $conn->query("SHOW TABLES LIKE '$table_name'");
        if ($check_table->num_rows == 0) {
            echo "Table for semester $sem does not exist.<br>";
            continue;
        }

        // Prepare query
        if ($action === 'Load One') {
            if (!$id) {
                die("Please provide an ID to load a single record.");
            }
            $sql = "SELECT * FROM $table_name WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $id);
        } elseif ($action === 'Load All') {
            $sql = "SELECT * FROM $table_name";
            $stmt = $conn->prepare($sql);
        } else {
            die("Invalid action.");
        }

        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $results[$sem] = $result->fetch_all(MYSQLI_ASSOC);
        } else {
            echo "No records found in $table_name.<br>";
        }

        $stmt->close();
    }

    // Display the results
    foreach ($results as $sem => $rows) {
        echo "<div id='printableTable_$sem'>";
        echo "<h2>Data from Table: semester_$sem</h2>";
        echo "<table>";
        echo "<tr><th>ID</th><th>Name</th><th>Roll No</th><th>Gender</th><th>B.Ed.</th><th>Admitted Year</th><th>Photo</th></tr>";
        foreach ($rows as $row) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['id']) . "</td>";
            echo "<td>" . htmlspecialchars($row['name']) . "</td>";
            echo "<td>" . htmlspecialchars($row['rollno']) . "</td>";
            echo "<td>" . htmlspecialchars($row['gender']) . "</td>";
            echo "<td>" . htmlspecialchars($row['b_ed']) . "</td>";
            echo "<td>" . htmlspecialchars($row['admitted_year']) . "</td>";
            
            // Photo handling
            if (!empty($row['photo'])) {
                $photo = base64_encode($row['photo']);
                echo "<td><img src='data:image/jpeg;base64,$photo' alt='Photo' width='50'></td>";
            } else {
                echo "<td>No Photo</td>";
            }
            echo "</tr>";
        }
        echo "</table>";
        echo "</div>";
        echo "<button onclick='printTable(\"printableTable_$sem\")'>Print Table $sem</button><br><br>";
    }
}

$conn->close();

echo '</body></html>';
?>