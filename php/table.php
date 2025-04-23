<?php
$conn = mysqli_connect('localhost', 'root', '', 'sms');

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
$sql = "SELECT `name`, `semester`, `rollno`,`b_ed`, `admitted_year`, `photo` FROM `st` WHERE `id` = $i";
$result = mysqli_query($conn, $sql);
if (mysqli_num_rows($result) > 0) {
    // Output data in a table
    echo "<table border='1' cellpadding='10'>";
    echo "<tr><th>ID</th><th>Name</th><th>Semester</th><th>Roll No</th><th>B.Ed</th><th>Admitted Year</th><th>Photo</th></tr>";
    
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>" .
             "<td>" . $row["id"] . "</td>" .
             "<td>" . $row["name"] . "</td>" .
             "<td>" . $row["semester"] . "</td>" .
             "<td>" . $row["rollno"] . "</td>" .
             "<td>" . $row["b_ed"] . "</td>" .
             "<td>" . $row["admitted_year"] . "</td>" .
             "<td><img src='" . $row["photo"] . "' width='100' height='100' alt='Photo'></td>" .
             "</tr>";
    }

    echo "</table>";
} else {
    echo "0 results";
}

// Close connection
mysqli_close($conn);
?>