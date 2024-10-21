<?php
$host = 'localhost';
$name = 'root'; // Change to a non-root user
$password = ''; // Use a secure password
$database = 'autokantadb';

// Create connection
$conn = new mysqli($host, $name, $password, $database);

// Check connection
if ($conn->connect_error) {
    error_log("Connection failed: " . $conn->connect_error); // Log the error
    echo "Connection failed. Please try again later."; // User-friendly message
    exit();
}

// Set character set to UTF-8
$conn->set_charset("utf8");

//echo "Connected successfully";
?>