<?php 
include "connection.php";

// Ensure that 'id' is present in the POST request
if (isset($_POST['id'])) {
    $i = intval($_POST['id']); // Sanitize input to prevent SQL injection

    // Debugging line to display ID
    echo "<br>ID: " . $i . "<br>";

    // SQL query to select the required fields
    $query = "SELECT `name`, `semester`, `rollno`,`b_ed`, `admitted_year`, `photo` FROM `st` WHERE `id` = $i";

    // Execute the query
    $result = mysqli_query($conn, $query);

    // Check if the query was successful
    if ($result) {
        // Check if there are any rows returned
        if (mysqli_num_rows($result) > 0) {
            // Fetch and display data
            while ($row = mysqli_fetch_assoc($result)) {
        echo "<br>"."<br>"."Name: " . htmlspecialchars($row['name']) . "<br>";
        echo "Semester: " . htmlspecialchars($row['semester']) . "<br>";
        echo "Roll No.: " . htmlspecialchars($row['rollno']) . "<br>";
        echo "B.Ed.: " . htmlspecialchars($row['b_ed']) . "<br>";
        echo "Admitted Year: " . htmlspecialchars($row['admitted_year']) . "<br>";
        echo "Photo: " . htmlspecialchars($row['photo']) . "<br>";
            }
        }
    }
}
?>