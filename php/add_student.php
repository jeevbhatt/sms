<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    exit('Unauthorized');
}

// Include database connection
require_once('db_connect.php');

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $name = $_POST['name'];
    $semester = $_POST['semester'];
    $rollno = $_POST['rollno'];
    $gender = $_POST['gender'];
    $b_ed = $_POST['b_ed'];
    $admitted_year = $_POST['admitted_year'];
    $created_at = date('Y-m-d H:i:s');

    // Handle file upload
    $photo = null;
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png'];
        $filename = $_FILES['photo']['name'];
        $filetype = pathinfo($filename, PATHINFO_EXTENSION);

        // Check if file type is allowed
        if (in_array(strtolower($filetype), $allowed)) {
            // Create unique filename
            $newname = uniqid() . '.' . $filetype;
            $upload_dir = '../uploads/';

            // Create directory if it doesn't exist
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            // Move uploaded file
            if (move_uploaded_file($_FILES['photo']['tmp_name'], $upload_dir . $newname)) {
                $photo = $newname;
            }
        }
    }

    // Prepare SQL statement to prevent SQL injection
    $stmt = $conn->prepare("INSERT INTO students (name, semester, rollno, gender, b_ed, admitted_year, photo, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sissssss", $name, $semester, $rollno, $gender, $b_ed, $admitted_year, $photo, $created_at);

    // Execute the statement
    if ($stmt->execute()) {
        // Redirect back to the form with success message
        header("Location: ../index.html#services?success=1");
        exit();
    } else {
        // Redirect back to the form with error message
        header("Location: ../index.html#services?error=1");
        exit();
    }

    // Close statement
    $stmt->close();

    // Close connection
    $conn->close();
}
?>
