<?php
session_start();
require 'db.php'; // Include your database connection file

// Initialize variables
$error_message = "";
$success_message = "";
$cars = []; // To hold added car details

// Check if the user is logged in
if (!isset($_SESSION['ssn'])) {
    header("Location: login.php");
    exit();
}

// Handle car addition
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_car'])) {
    $registration_number = trim($_POST['registration_number']);
    $color = trim($_POST['color']);
    $year_model = intval($_POST['year_model']);
    $kilometers = intval($_POST['kilometers']);
    $owner = trim($_POST['owner']);

    // Validate input
    if (empty($registration_number) || empty($color) || $year_model <= 1885 || $kilometers < 0 || empty($owner)) {
        $error_message = "All fields are required and must be valid.";
    } else {
        // Prepare the insert statement
        $stmt = $conn->prepare("INSERT INTO car (register, color, year_model, kilometers, owner) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssdis", $registration_number, $color, $year_model, $kilometers, $owner);

        // Execute the statement
        if ($stmt->execute()) {
            $success_message = "Car added successfully.";
        } else {
            $error_message = "Error: " . $stmt->error;
        }

        // Close the statement
        $stmt->close();
    }
}

// Fetch added cars for the logged-in user
$ssn = $_SESSION['ssn']; // Get the logged-in user's SSN
$result = $conn->prepare("SELECT * FROM car WHERE owner = ? ORDER BY id DESC"); // Ensure the column name matches

if ($result === false) {
    die("Prepare failed: " . htmlspecialchars($conn->error)); // Output error message
}

$result->bind_param("s", $ssn);
$result->execute();
$result_set = $result->get_result();

if ($result_set->num_rows > 0) {
    while ($row = $result_set->fetch_assoc()) {
        $cars[] = $row; // Add car details to array
    }
}

// Close the statement
$result->close();

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Car Management</title>
</head>

<body>

    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Car Management</a>
            <form action="logout.php" method="POST">
                <button class="btn btn-outline-danger" type="submit">Logout</button>
            </form>
        </div>
    </nav>

    <div class="container mt-5">
        <h2>Add a Car</h2>

        <!-- Display error or success messages -->
        <?php if ($error_message): ?>
            <div class="alert alert-danger" role="alert">
                <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php elseif ($success_message): ?>
            <div class="alert alert-success" role="alert">
                <?php echo htmlspecialchars($success_message); ?>
            </div>
        <?php endif; ?>

        <form action="add_car.php" method="POST">
            <div class="mb-3">
                <label for="registration_number" class="form-label">Car Registration Number</label>
                <input type="text" class="form-control" id="registration_number" name="registration_number" required>
            </div>
            <div class="mb-3">
                <label for="color" class="form-label">Color</label>
                <input type="text" class="form-control" id="color" name="color" required>
            </div>
            <div class="mb-3">
                <label for="year_model" class="form-label">Year Model</label>
                <input type="number" class="form-control" id="year_model" name="year_model" min="1886" required>
            </div>
            <div class="mb-3">
                <label for="kilometers" class="form-label">Kilometers</label>
                <input type="number" class="form-control" id="kilometers" name="kilometers" min="0" required>
            </div>
            <div class="mb-3">
                <label for="owner" class="form-label">Owner</label>
                <input type="text" class="form-control" id="owner" name="owner" required>
            </div>
            <button type="submit" name="add_car" class="btn btn-primary">Add Car</button>
            <a href="index.php" class="btn btn-secondary">Profile</a>
        </form>

        <h3 class="mt-4">Added Cars:</h3>
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Registration Number</th>
                    <th>Color</th>
                    <th>Year Model</th>
                    <th>Kilometers</th>
                    <th>Owner</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($cars as $car): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($car['id']); ?></td>
                        <td><?php echo htmlspecialchars($car['register']); ?></td>
                        <td><?php echo htmlspecialchars($car['color']); ?></td>
                        <td><?php echo htmlspecialchars($car['year_model']); ?></td>
                        <td><?php echo htmlspecialchars($car['kilometers']); ?></td>
                        <td><?php echo htmlspecialchars($car['owner']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>