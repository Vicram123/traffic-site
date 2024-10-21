<?php
session_start();
require 'db.php'; // Include the database connection file

$error_message = "";
$success_message = "";

// Handle password reset request
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['reset_password'])) {
    $ssn = trim($_POST['ssn']);
    $phone = trim($_POST['phone']);
    $new_password = trim($_POST['new_password']);

    // Validate inputs
    if (empty($ssn) || empty($phone) || empty($new_password)) {
        $error_message = "All fields are required.";
    } else {
        // Fetch user by SSN and phone number
        $stmt = $conn->prepare("SELECT * FROM person WHERE ssn = ? AND phone_number = ?");
        $stmt->bind_param("ss", $ssn, $phone);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Update the password
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $update_stmt = $conn->prepare("UPDATE person SET password = ? WHERE ssn = ?");
            $update_stmt->bind_param("ss", $hashed_password, $ssn);

            if ($update_stmt->execute()) {
                $success_message = "Password reset successfully. You can now log in.";
            } else {
                $error_message = "Failed to reset password. Please try again.";
            }
        } else {
            $error_message = "SSN or phone number is incorrect.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Reset Password</title>
</head>

<body>
    <div class="container mt-5">
        <h2>Reset Password</h2>
        <?php if ($error_message): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error_message); ?></div>
        <?php endif; ?>
        <?php if ($success_message): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success_message); ?></div>
        <?php endif; ?>
        <form action="reset_password.php" method="POST">
            <div class="mb-3">
                <label for="ssn" class="form-label">SSN</label>
                <input type="text" class="form-control" id="ssn" name="ssn" required>
            </div>
            <div class="mb-3">
                <label for="phone" class="form-label">Phone Number</label>
                <input type="text" class="form-control" id="phone" name="phone" required>
            </div>
            <div class="mb-3">
                <label for="new_password" class="form-label">New Password</label>
                <input type="password" class="form-control" id="new_password" name="new_password" required>
            </div>
            <button type="submit" class="btn btn-primary" name="reset_password">Reset Password</button>
            <a href="index.php" class="btn btn-primary">Back</a>
        </form>
    </div>
</body>

</html>