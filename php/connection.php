<?php
$conn = mysqli_connect('localhost', 'root', '', 'sms');
if ($conn) {
    // Connection is successful
    echo "Connection OK";
    // Meta tag to refresh the page after 10 seconds
    echo "<head><meta http-equiv=\"refresh\" content=\"45;url=../sms.html\">";
    echo "<p>You will be redirected to the homepage in 45 seconds...</p>";
} 
else {
    echo "Not Connected";
}
?>