<?php
// Start the session
session_start();

// Include the database connection file
require 'db.php';

// Initialize an error message variable
$error_message = '';

// Generate a new CSRF token if it doesn't exist
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validate CSRF token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $error_message = 'CSRF token validation failed.';
    } else {
        // Start the transaction
        $conn->begin_transaction();

        try {
            // Get form data
            $name = trim($_POST['name']);
            $ssn = trim($_POST['ssn']);
            $address = trim($_POST['address']);
            $phone_number = trim($_POST['phone_number']);
            $password = password_hash(trim($_POST['password']), PASSWORD_DEFAULT);
            $confirm_password = trim($_POST['confirm_password']);

            // Validate passwords match
            if ($_POST['password'] !== $confirm_password) {
                throw new Exception('Passwords do not match.');
            }

            // Insert into the person table
            $stmt = $conn->prepare("INSERT INTO person (ssn, name, address, phone_number, password) VALUES (?, ?, ?, ?, ?)");
            // Check if prepare() failed
            if ($stmt === false) {
                throw new Exception('Prepare failed: ' . $conn->error);
            }
            $stmt->bind_param("sssss", $ssn, $name, $address, $phone_number, $password);
            if (!$stmt->execute()) {
                throw new Exception('Execute failed: ' . $stmt->error);
            }

            // Commit the transaction
            $conn->commit();

            // Redirect to index page after successful registration
            header("Location: login.php");
            exit(); // Always exit after a redirect

        } catch (Exception $e) {
            // Roll back the transaction if an error occurred
            $conn->rollback();
            $error_message = "Failed to add person: " . $e->getMessage();
        }

        // Close the statement
        $stmt->close();
    }
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Person Registration</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>

<body>
    <div class="container mt-5">
        <h2 class="text-center">Person Registration Form</h2>
        <?php if (!empty($error_message)): ?>
            <div class="alert alert-danger">
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>
        <form action="register_person.php" method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">

            <div class="form-group">
                <label for="name">Name:</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <div class="form-group">
                <label for="ssn">SSN:</label>
                <input type="text" class="form-control" id="ssn" name="ssn" required>
            </div>
            <div class="form-group">
                <label for="address">Address:</label>
                <input type="text" class="form-control" id="address" name="address" required>
            </div>
            <div class="form-group">
                <label for="phone_number">Phone Number:</label>
                <input type="text" class="form-control" id="phone_number" name="phone_number" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <div class="form-group">
                <label for="confirm_password">Confirm Password:</label>
                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
            </div>

            <button type="submit" class="btn btn-primary btn-block">Register</button>
            <a href="login.php" class="btn btn-secondary btn-block">Login</a>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>