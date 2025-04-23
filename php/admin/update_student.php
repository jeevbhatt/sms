<?php
session_start();
// Check if user is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

// Include database connection
require_once('../db_connect.php');

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $id = $_POST['id'];
    $name = $_POST['name'];
    $semester = $_POST['semester'];
    $rollno = $_POST['rollno'];
    $gender = $_POST['gender'];
    $b_ed = $_POST['b_ed'];
    $admitted_year = $_POST['admitted_year'];
    
    // Handle file upload
    $photo_update = "";
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png'];
        $filename = $_FILES['photo']['name'];
        $filetype = pathinfo($filename, PATHINFO_EXTENSION);
        
        // Check if file type is allowed
        if (in_array(strtolower($filetype), $allowed)) {
            // Create unique filename
            $newname = uniqid() . '.' . $filetype;
            $upload_dir = '../../uploads/';
            
            // Create directory if it doesn't exist
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            // Move uploaded file
            if (move_uploaded_file($_FILES['photo']['tmp_name'], $upload_dir . $newname)) {
                // Get current photo to delete
                $stmt = $conn->prepare("SELECT photo FROM students WHERE id = ?");
                $stmt->bind_param("i", $id);
                $stmt->execute();
                $result = $stmt->get_result();
                
                if ($row = $result->fetch_assoc()) {
                    $old_photo = $row['photo'];
                    // Delete old photo if exists
                    if ($old_photo && file_exists($upload_dir . $old_photo)) {
                        unlink($upload_dir . $old_photo);
                    }
                }
                
                $photo_update = ", photo = ?";
            }
        }
    }
    
    // Prepare SQL statement to prevent SQL injection
    if ($photo_update) {
        $sql = "UPDATE students SET name = ?, semester = ?, rollno = ?, gender = ?, b_ed = ?, admitted_year = ?, photo = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sissssssi", $name, $semester, $rollno, $gender, $b_ed, $admitted_year, $newname, $id);
    } else {
        $sql = "UPDATE students SET name = ?, semester = ?, rollno = ?, gender = ?, b_ed = ?, admitted_year = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sissssi", $name, $semester, $rollno, $gender, $b_ed, $admitted_year, $id);
    }
    
    // Execute the statement
    if ($stmt->execute()) {
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'message' => 'Student updated successfully']);
    } else {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Error updating student: ' . $stmt->error]);
    }
    
    // Close statement
    $stmt->close();
    
    // Close connection
    $conn->close();
}
?>