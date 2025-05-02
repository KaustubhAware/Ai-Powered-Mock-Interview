<?php
$host = "localhost";
$user = "root"; // Change if your MySQL has a different username
$password = ""; // Set password if applicable
$database = "mock_interview"; // Database name

$conn = new mysqli($host, $user, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
