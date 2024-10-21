<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Start the session
session_start();
require 'db.php'; // Include your database connection file

// Enable MySQLi error reporting
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$error_message = "";

// Handle login form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $ssn = trim($_POST['ssn']);
    $password = trim($_POST['password']);

    // Basic validation
    if (empty($ssn) || empty($password)) {
        $error_message = "SSN and password are required.";
    } else {
        // Prepare the SQL statement
        $stmt = $conn->prepare("SELECT user_id, password, role FROM person WHERE ssn = ?");

        if ($stmt === false) {
            error_log("Prepare failed: " . htmlspecialchars($conn->error));
            $error_message = "An error occurred while preparing the query.";
        } else {
            // Bind parameters and execute
            $stmt->bind_param("s", $ssn);
            if (!$stmt->execute()) {
                error_log("Execution failed: " . htmlspecialchars($stmt->error));
                $error_message = "An error occurred while executing the query.";
            } else {
                $stmt->store_result();

                // Check if the user exists
                if ($stmt->num_rows === 0) {
                    $error_message = "No user found with this SSN.";
                } else {
                    // Bind the result variables
                    $stmt->bind_result($user_id, $hashed_password, $role);
                    $stmt->fetch(); // Fetch the result

                    // Debug output
                    echo "Fetched user_id: " . htmlspecialchars($user_id) . "<br>";
                    echo "Fetched role: " . htmlspecialchars($role) . "<br>";

                    // Verify the password
                    if (password_verify($password, $hashed_password)) {
                        session_regenerate_id(true); // Regenerate session ID
                        $_SESSION['ssn'] = $ssn;
                        $_SESSION['role'] = $role;
                        $_SESSION['user_id'] = $user_id; // Set user_id in the session

                        // Redirect based on user role
                        switch ($role) {
                            case 'admin':
                                header("Location: dashboard.php");
                                break;
                            case 'manager':
                                header("Location: register_fine.php");
                                break;
                            default:
                                header("Location: index.php");
                                break;
                        }
                        exit; // Always call exit after a redirect
                    } else {
                        $error_message = "Invalid password.";
                    }
                }
            }
            $stmt->close(); // Close the statement
        }
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
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <h2>Login</h2>
        <?php if (!empty($error_message)): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error_message); ?></div>
        <?php endif; ?>
        <form action="" method="POST">
            <div class="mb-3">
                <label for="ssn" class="form-label">SSN</label>
                <input type="text" class="form-control" id="ssn" name="ssn" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary">Login</button>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>