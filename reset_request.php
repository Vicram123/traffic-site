<?php
// reset_request.php

// Start the session
session_start();

// Include the database connection file
include 'db.php'; // Adjust the actual filename

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $ssn = $_POST['ssn'];

    // Check if SSN exists in the database
    $sqlCheckSSN = "SELECT COUNT(*) FROM person WHERE ssn = ?";
    $stmtCheckSSN = $conn->prepare($sqlCheckSSN);
    $stmtCheckSSN->bind_param("s", $ssn);
    $stmtCheckSSN->execute();
    $stmtCheckSSN->bind_result($count);
    $stmtCheckSSN->fetch();

    if ($count > 0) {
        // Generate a unique token for password reset
        $token = bin2hex(random_bytes(50));

        // Store the token in the database
        $sqlToken = "INSERT INTO password_resets (ssn, token) VALUES (?, ?)";
        $stmtToken = $conn->prepare($sqlToken);
        $stmtToken->bind_param("ss", $ssn, $token);
        $stmtToken->execute();

        // Send an email to the user with the reset link
        $resetLink = "http://yourdomain.com/reset_password.php?token=" . $token; // Adjust URL accordingly
        // You would need to implement the email sending here (using mail() or PHPMailer)

        echo "Reset link has been sent to your email.";
    } else {
        echo "SSN does not exist.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Password Reset Request</title>
</head>

<body>
    <h2>Password Reset Request</h2>
    <form action="reset_request.php" method="POST">
        <label for="ssn">Enter your SSN:</label>
        <input type="text" id="ssn" name="ssn" required>
        <button type="submit">Request Password Reset</button>
    </form>
</body>

</html>