<?php
// Include database connection
require_once('db_connect.php');

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $message = $_POST['message'];
    $created_at = date('Y-m-d H:i:s');
    
    // Validate input
    if (empty($fullname) || empty($email) || empty($message)) {
        echo "Please fill in all fields.";
        exit;
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "Invalid email format.";
        exit;
    }
    
    // Prepare SQL statement to prevent SQL injection
    $stmt = $conn->prepare("INSERT INTO contact_messages (fullname, email, message, created_at) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $fullname, $email, $message, $created_at);
    
    // Execute the statement
    if ($stmt->execute()) {
        echo "Thank you for your message! We will get back to you soon.";
    } else {
        echo "Error: " . $stmt->error;
    }
    
    // Close statement
    $stmt->close();
    
    // Close connection
    $conn->close();
}
?>