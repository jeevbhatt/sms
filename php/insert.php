<?php 
include "connection.php";
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
</head>
<body>';
// Retrieve form data
$n = $_POST["name"];
$s = $_POST["semester"];
$r = $_POST['rollno'];
$g = $_POST['gender']; // Gender
$b = $_POST['b_ed'];
$ay = $_POST['admitted_year'];

// Handle the uploaded photo file
$a = $_FILES['photo']['tmp_name'];
$ph = addslashes(file_get_contents($a));

// Debugging output
echo "<br>Name: $n<br>Semester: $s<br>Roll No: $r<br>Gender: $g<br>Program: $b<br>Admitted Year: $ay<br>";

// Determine the table name based on semester
$table_name = "semester_" . $s;

// Check if the semester value is valid
if (!is_numeric($s) || $s < 1 || $s > 8) {
    die("<p>Invalid semester value. Please enter a number between 1 and 8.</p>");
}

// Check if the table exists
$check_table = "SHOW TABLES LIKE '$table_name'";
$result = mysqli_query($conn, $check_table);
if (mysqli_num_rows($result) === 0) {
    die("<p>Table for semester $s does not exist. Please create the table first.</p>");
}

// Insert query for the respective semester table
$query = "INSERT INTO `$table_name` (`name`, `semester`, `rollno`, `gender`, `b_ed`, `admitted_year`, `photo`)  
          VALUES ('$n', '$s', '$r', '$g', '$b', '$ay', '$ph')";

if (mysqli_query($conn, $query)) {
    echo "<p>Record inserted successfully into $table_name</p>";
} else {
    echo "<p>Error inserting record: " . mysqli_error($conn) . "</p>";
}

// Close the database connection
mysqli_close($conn);
?>