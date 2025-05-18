<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    exit('Unauthorized');
}

// Include database connection
require_once '../db_connect.php';

// Set header to return JSON
header('Content-Type: application/json');

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $address = $_POST['address'] ?? '';
    $class = $_POST['class'] ?? '';
    $roll = $_POST['roll'] ?? '';
    $dob = $_POST['dob'] ?? '';
    $gender = $_POST['gender'] ?? '';

    // Validate required fields
    if (empty($name) || empty($email) || empty($phone) || empty($class) || empty($roll)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Please fill all required fields'
        ]);
        exit;
    }

    // Handle file upload
    $photo = '';
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../../uploads/';
        $fileName = time() . '_' . basename($_FILES['photo']['name']);
        $targetFile = $uploadDir . $fileName;

        // Check if file is an actual image
        $check = getimagesize($_FILES['photo']['tmp_name']);
        if ($check === false) {
            echo json_encode([
                'status' => 'error',
                'message' => 'File is not an image'
            ]);
            exit;
        }

        // Check file size (limit to 5MB)
        if ($_FILES['photo']['size'] > 5000000) {
            echo json_encode([
                'status' => 'error',
                'message' => 'File is too large (max 5MB)'
            ]);
            exit;
        }

        // Allow only certain file formats
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
        $fileExtension = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
        if (!in_array($fileExtension, $allowedExtensions)) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Only JPG, JPEG, PNG & GIF files are allowed'
            ]);
            exit;
        }

        // Upload file
        if (move_uploaded_file($_FILES['photo']['tmp_name'], $targetFile)) {
            $photo = $fileName;
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Failed to upload file'
            ]);
            exit;
        }
    }

    try {
        // Check if email already exists
        $checkSql = "SELECT id FROM students WHERE email = :email";
        $checkStmt = $conn->prepare($checkSql);
        $checkStmt->bindParam(':email', $email, PDO::PARAM_STR);
        $checkStmt->execute();

        if ($checkStmt->rowCount() > 0) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Email already exists'
            ]);
            exit;
        }

        // Prepare SQL query
        $sql = "INSERT INTO students (name, email, phone, address, class, roll, dob, gender, photo)
                VALUES (:name, :email, :phone, :address, :class, :roll, :dob, :gender, :photo)";
        $stmt = $conn->prepare($sql);

        // Bind parameters
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->bindParam(':phone', $phone, PDO::PARAM_STR);
        $stmt->bindParam(':address', $address, PDO::PARAM_STR);
        $stmt->bindParam(':class', $class, PDO::PARAM_STR);
        $stmt->bindParam(':roll', $roll, PDO::PARAM_STR);
        $stmt->bindParam(':dob', $dob, PDO::PARAM_STR);
        $stmt->bindParam(':gender', $gender, PDO::PARAM_STR);
        $stmt->bindParam(':photo', $photo, PDO::PARAM_STR);

        // Execute the query
        $stmt->execute();

        // Return success response
        echo json_encode([
            'status' => 'success',
            'message' => 'Student added successfully'
        ]);
    } catch (PDOException $e) {
        // Return error response
        echo json_encode([
            'status' => 'error',
            'message' => 'Database error: ' . $e->getMessage()
        ]);
    }
} else {
    // Return error for invalid request method
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid request method'
    ]);
}
?>
