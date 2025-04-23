<?php 
include "connection.php";
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
</head>
<body>';
echo "<div class='header'>
    <img src='logo.png' alt='Logo' class='logo'>
    <h1>Student Management System</h1>
</div>";
if (isset($_POST["id"], $_POST["semester"])) {
    $i = $_POST["id"];
    $s = $_POST["semester"];
} else {
    echo "<p>Error: Missing ID or Semester value.</p>";
    exit;
}

// Validate the semester input
if (!is_numeric($s) || $s < 1 || $s > 8) {
    die("<p>Invalid semester value. Please enter a number between 1 and 8.</p>");
}

// Construct the table name dynamically
$table_name = "semester_" . $s;

// Check if the table exists
$check_table = "SHOW TABLES LIKE '$table_name'";
$result = mysqli_query($conn, $check_table);
if (mysqli_num_rows($result) === 0) {
    die("<p>Table for semester $s does not exist. Please check your input.</p>");
}

// Prepare the DELETE query
$query = "DELETE FROM `$table_name` WHERE `id`=?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $i);

// Execute the query
if (mysqli_stmt_execute($stmt)) {
    echo "<p>Record with ID $i deleted successfully from $table_name.</p>";
} else {
    echo "<p>Error deleting record: " . mysqli_stmt_error($stmt) . "</p>";
}

// Close the statement and connection
mysqli_stmt_close($stmt);
mysqli_close($conn);
?>