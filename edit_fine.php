<?php
session_start();
require 'db.php'; // Include your database connection file

// Handle fine update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_fine'])) {
    $fine_id = trim($_POST['fine_id']);
    $date = $_POST['date'];
    $amount = $_POST['amount'];
    $reason = trim($_POST['reason']);

    // Basic validation
    if (empty($fine_id) || empty($date) || empty($amount) || empty($reason)) {
        die("All fields are required.");
    } else {
        // Prepare the update statement
        $stmt = $conn->prepare("UPDATE fine SET date = ?, amount = ?, reason = ? WHERE id = ?");
        if ($stmt === false) {
            die("Prepare failed: " . $conn->error);
        }

        $stmt->bind_param("sdsi", $date, $amount, $reason, $fine_id);

        // Execute the statement
        if ($stmt->execute()) {
            header("Location: view_fines.php?car=" . urlencode($_POST['car'])); // Redirect back to fines view
            exit;
        } else {
            die("Error updating fine details: " . $stmt->error);
        }

        // Close the statement
        $stmt->close();
    }
}

// Close the database connection
$conn->close();
?>