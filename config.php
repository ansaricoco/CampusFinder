<?php
$conn = new mysqli("localhost", "root", "", "cf_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
