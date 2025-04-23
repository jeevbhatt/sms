<?php
include 'connection.php';

echo "<div class='header'>
    <img src='logo.png' alt='Logo' class='logo'>
    <h1>Student Management System</h1>
</div>";
echo '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search and Print</title>
    <style>
        body {
            font-family: Times New Roman, sans-serif;
            background: #ffe259;  /* fallback for old browsers */
background: -webkit-linear-gradient(to right, #ffa751, #ffe259);  /* Chrome 10-25, Safari 5.1-6 */
background: linear-gradient(to right, #ffa751, #ffe259); /* W3C, IE 10+/ Edge, Firefox 16+, Chrome 26+, Opera 12+, Safari 7+ */

        }
        .logo img{
            .middle {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        }
        .id-cards {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin: 20px 0;
        }
        .id-card {
            width: 400px;
            border: 1px solid #ddd;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
            background-color: #f9f9f9;
            text-align: center;
            background: #B79891;  /* fallback for old browsers */
background: -webkit-linear-gradient(to right, #94716B, #B79891);  /* Chrome 10-25, Safari 5.1-6 */
background: linear-gradient(to right, #94716B, #B79891); /* W3C, IE 10+/ Edge, Firefox 16+, Chrome 26+, Opera 12+, Safari 7+ */

        }
        .id-card img {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            margin-bottom: 10px;
            object-fit: cover;
        }
        .id-card h3 {
            margin: 10px 0;
            font-size: 1.2em;
        }
        .id-card p {
            margin: 5px 0;
            font-size: 0.9em;
            color: #555;
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
        function printTable() {
            const printableContent = document.getElementById("printableArea").innerHTML;
            const printWindow = window.open("", "_blank", "width=800,height=600");
            printWindow.document.write(`
                <html>
                <head>
                    <title>Print Cards</title>
                    <style>
                        body { font-family: Tines New Roman, sans-serif; margin: 20px; }
                        .id-cards { display: flex; flex-wrap: wrap; gap: 20px; margin: 20px 0; }
                        .id-card {
                            width: 400px;
                            border: 1px solid #ddd;
                            border-radius: 10px;
                            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                            padding: 20px;
                            background-color: #f9f9f9;
                            text-align: center;
                            background: #B79891;  /* fallback for old browsers */
background: -webkit-linear-gradient(to right, #94716B, #B79891);  /* Chrome 10-25, Safari 5.1-6 */
background: linear-gradient(to right, #94716B, #B79891); /* W3C, IE 10+/ Edge, Firefox 16+, Chrome 26+, Opera 12+, Safari 7+ */
                        }
                        .id-card img {
                            width: 80px;
                            height: 80px;
                            border-radius: 50%;
                            margin-bottom: 10px;
                            object-fit: cover;
                            background: #B79891;  /* fallback for old browsers */
background: -webkit-linear-gradient(to right, #94716B, #B79891);  /* Chrome 10-25, Safari 5.1-6 */
background: linear-gradient(to right, #94716B, #B79891); /* W3C, IE 10+/ Edge, Firefox 16+, Chrome 26+, Opera 12+, Safari 7+ */
                        }
                        .id-card h3 {
                            margin: 10px 0;
                            font-size: 1.2em;
                        }
                        .id-card p {
                            margin: 5px 0;
                            font-size: 0.9em;
                            color: #555;
                        }
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

// Processing user input
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Fetch and sanitize input values
    $id = !empty($_POST['id']) ? intval($_POST['id']) : null;
    $gender = !empty($_POST['gender']) ? trim($_POST['gender']) : null;
    $rollno = !empty($_POST['rollno']) ? trim($_POST['rollno']) : null;
    $semester = !empty($_POST['semester']) ? trim($_POST['semester']) : null;
    $b_ed = !empty($_POST['b_ed']) ? trim($_POST['b_ed']) : null;
    $admitted_year = !empty($_POST['admitted_year']) ? trim($_POST['admitted_year']) : null;

    // Ensure at least one filter is provided
    if (!$id && !$gender && !$rollno && !$semester && !$b_ed && !$admitted_year) {
        die("Please provide at least one search criterion.");
    }

    // Initialize query filters and parameters
    $filters = [];
    $params = [];
    $param_types = "";

    if ($id) {
        $filters[] = "`id` = ?";
        $params[] = $id;
        $param_types .= "i";
    }
    if ($gender) {
        $filters[] = "`gender` LIKE ?";
        $params[] = "%$gender%";
        $param_types .= "s";
    }
    if ($rollno) {
        $filters[] = "`rollno` LIKE ?";
        $params[] = "%$rollno%";
        $param_types .= "s";
    }
    if ($semester) {
        $filters[] = "`semester` = ?";
        $params[] = $semester;
        $param_types .= "s";
    }
    if ($b_ed) {
        $filters[] = "`b_ed` LIKE ?";
        $params[] = "%$b_ed%";
        $param_types .= "s";
    }
    if ($admitted_year) {
        $filters[] = "`admitted_year` LIKE ?";
        $params[] = "%$admitted_year%";
        $param_types .= "s";
    }

    // Retrieve results from all semester tables
    $results = [];
    for ($sem = 1; $sem <= 8; $sem++) {
        $table_name = "semester_" . $sem;

        // Check if the table exists
        $check_table = $conn->query("SHOW TABLES LIKE '$table_name'");
        if ($check_table->num_rows == 0) {
            continue; // Skip if the table does not exist
        }

        // Prepare the query with dynamic filters
        $query = "SELECT * FROM $table_name";
        if (!empty($filters)) {
            $query .= " WHERE " . implode(" AND ", $filters);
        }

        $stmt = $conn->prepare($query);
        if ($stmt) {
            if (!empty($params)) {
                $stmt->bind_param($param_types, ...$params);
            }

            // Execute the query and fetch results
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $results[$sem] = $result->fetch_all(MYSQLI_ASSOC);
            }
            $stmt->close();
        }
    }

    // Display the results
    if (!empty($results)) {
        echo "<div id='printableArea'><div class='id-cards'>";
        foreach ($results as $sem => $rows) {
            foreach ($rows as $row) {
                echo "<div class='id-card'>";
                echo "<img src='data:image/jpeg;base64," . base64_encode($row['photo']) . "' alt='Photo'>";
                echo "<h3>" . htmlspecialchars($row['name']) . "</h3>";
                echo "<p>ID: " . htmlspecialchars($row['id']) . "</p>";
                echo "<p>Roll No: " . htmlspecialchars($row['rollno']) . "</p>";
                echo "<p>Gender: " . htmlspecialchars($row['gender']) . "</p>";
                echo "<p>B.Ed.: " . htmlspecialchars($row['b_ed']) . "</p>";
                echo "<p>Admitted Year: " . htmlspecialchars($row['admitted_year']) . "</p>";
                echo "</div>";
            }
        }
        echo "</div></div>";
        echo "<button onclick='printTable()'>Print ID Cards</button>";
    } else {
        echo "No matching records found.";
    }
}

$conn->close();

echo '</body></html>';
?>