<?php
// update_profile.php

session_start();
include 'db.php'; // Include your database connection file

// Check if the user is logged in
if (!isset($_SESSION['ssn'])) {
    echo "User is not logged in.";
    exit();
}

// Get the submitted data
$ssn = $_POST['ssn'];
$name = $_POST['name'];
$address = $_POST['address'];
$phone_number = $_POST['phone_number'];

// Update the user's profile in the database
$sqlUpdate = "UPDATE person SET name = ?, address = ?, phone_number = ? WHERE ssn = ?";
$stmtUpdate = $conn->prepare($sqlUpdate);

if ($stmtUpdate === false) {
    die("Prepare failed: " . $conn->error);
}

$stmtUpdate->bind_param("ssss", $name, $address, $phone_number, $ssn);
if ($stmtUpdate->execute()) {
    // Redirect back to the index page or show success message
    header("Location: index.php?update=success");
    exit();
} else {
    echo "Error updating profile: " . $stmtUpdate->error;
}

$stmtUpdate->close();
$conn->close();
