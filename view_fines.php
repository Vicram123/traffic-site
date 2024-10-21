<?php
// view_fines.php

// Start or resume the session
session_start();

// Include the database connection file (db.php)
include 'db.php'; // Adjust the actual filename

// Check if 'ssn' is set in the session
if (!isset($_SESSION['ssn'])) {
    echo "Session SSN is not set.";
    exit();
}

// Get the car_id from the URL
$car_id = isset($_GET['car_id']) ? $_GET['car_id'] : '';

// Validate car_id
if (empty($car_id)) {
    echo "Car ID is not specified.";
    exit();
}

// Fetch fines associated with the car
$sqlFines = "SELECT * FROM fine WHERE car = ?";
$stmtFines = $conn->prepare($sqlFines);

// Check if prepare() failed
if ($stmtFines === false) {
    die("Prepare failed: " . $conn->error);
}

$stmtFines->bind_param("s", $car_id);
$stmtFines->execute();
$finesResult = $stmtFines->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>View Fines</title>
</head>

<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light fixed-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Traffic Info</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Contact</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="home.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5" style="margin-top: 70px;"> <!-- Adjust margin to avoid overlap with navbar -->
        <h2>Fines for Car ID: <?php echo htmlspecialchars($car_id); ?></h2>

        <?php if ($finesResult->num_rows > 0): ?>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Fine ID</th>
                        <th>Amount</th>
                        <th>Date Issued</th>
                        <th>Description</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($fine = $finesResult->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($fine['id']); ?></td> <!-- Assuming 'id' is the primary key -->
                            <td><?php echo htmlspecialchars($fine['amount']); ?></td>
                            <td><?php echo htmlspecialchars($fine['date']); ?></td>
                            <td><?php echo htmlspecialchars($fine['reason']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            <a href="index.php" class="btn btn-primary">Back</a>
        <?php else: ?>
            <p>No fines found for this car.</p>
            <a href="index.php" class="btn btn-primary">Back</a> <!-- Back button for better user experience -->
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>