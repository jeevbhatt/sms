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
if (isset($_POST['id'], $_POST['name'], $_POST['semester'], $_POST['rollno'], $_POST['gender'], $_POST['b_ed'], $_POST['admitted_year'])) {
    $i = $_POST["id"];
    $n = $_POST["name"];
    $s = $_POST["semester"];
    $r = $_POST["rollno"];
    $g = $_POST["gender"];
    $b = $_POST["b_ed"];
    $ay = $_POST["admitted_year"];
} else {
    echo "One or more fields are missing.";
    exit;  // Stop script execution if any required field is missing
}

// Validate semester and construct the table name
if (!is_numeric($s) || $s < 1 || $s > 8) {
    die("<p>Invalid semester value. Please enter a number between 1 and 8.</p>");
}
$table_name = "semester_" . $s;

// Validate file upload and handle the case if no file is uploaded
if (!empty($_FILES['photo']['tmp_name'])) {
    $a = $_FILES['photo']['tmp_name'];
    $ph = file_get_contents($a);
} else {
    $ph = null;  // If no photo is uploaded, we can handle it later in the SQL query
}

// Debugging: Echo the received values for testing purposes
echo "<br>ID: $i<br>Name: $n<br>Semester: $s<br>Roll No: $r<br>Gender: $g<br>Program: $b<br>Admitted Year: $ay<br>";
echo $ph ? "<br>Photo uploaded successfully.<br>" : "<br>No photo uploaded.<br>";

// Check if the table exists
$check_table = "SHOW TABLES LIKE '$table_name'";
$result = mysqli_query($conn, $check_table);
if (mysqli_num_rows($result) === 0) {
    die("<p>Table for semester $s does not exist. Please create the table first.</p>");
}

// Prepare the SQL query
if ($ph) {
    // If a photo is uploaded, update it as well
    $query = "UPDATE `$table_name` SET `name`=?, `rollno`=?, `gender`=?, `b_ed`=?, `admitted_year`=?, `photo`=? WHERE `id`=?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "ssssssi", $n, $r, $g, $b, $ay, $ph, $i);
} else {
    // If no new photo is uploaded, update other fields only
    $query = "UPDATE `$table_name` SET `name`=?, `rollno`=?, `gender`=?, `b_ed`=?, `admitted_year`=? WHERE `id`=?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "sssssi", $n, $r, $g, $b, $ay, $i);
}

// Execute the query and check for errors
if (mysqli_stmt_execute($stmt)) {
    echo "<p>Record updated successfully in $table_name.</p>";
} else {
    echo "<p>Error updating record: " . mysqli_stmt_error($stmt) . "</p>";
}

// Close the statement and database connection
mysqli_stmt_close($stmt);
mysqli_close($conn);
?>