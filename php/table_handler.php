<?php
$id = 'id';

$conn = new mysqli("localhost", "root", "", "sms");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT `name`, `semester`, `rollno`, `b.ed`, `admitted_year`, `photo` FROM `st` WHERE `id` = ?";

$stmt = $conn->prepare($sql);

$stmt->bind_param("i", $id);

// Execute the statement
$stmt->execute();

// Get the result
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "Name: " . $row["name"] . "<br>";
        echo "Semester: " . $row["semester"] . "<br>";
        echo "Roll Number: " . $row["rollno"] . "<br>";
        echo "B.Ed: " . $row["b_ed"] . "<br>";
        echo "Admitted Year: " . $row["admitted_year"] . "<br>";
        echo "<img src='" . $row["photo"] . "' alt='Photo'><br>";
    }
} else {
    echo "No record found.";
}

$conn->close();
?>