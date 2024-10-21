<?php
// index.php

// Start or resume the session
session_start();

// Include the database connection file (db.php)
include 'db.php'; // Adjust the actual filename

// Check if 'ssn' and 'role' are set in the session
if (!isset($_SESSION['ssn']) || !isset($_SESSION['role'])) {
    echo "Session SSN or role is not set.";
    exit();
}

$ssn = $_SESSION['ssn'];
$role = $_SESSION['role']; // Fetch the role from the session

// Fetch owner's details
$sqlOwner = "SELECT name, address, phone_number FROM person WHERE ssn = ?";
$stmtOwner = $conn->prepare($sqlOwner);

// Check if prepare() failed
if ($stmtOwner === false) {
    die("Prepare failed: " . $conn->error);
}

$stmtOwner->bind_param("s", $ssn);
$stmtOwner->execute();
$ownerResult = $stmtOwner->get_result();
$ownerDetails = $ownerResult->fetch_assoc();

// Fetch cars added by the owner
$sqlCars = "SELECT car.* FROM car WHERE car.owner = ?";
$stmtCars = $conn->prepare($sqlCars);

// Check if prepare() failed
if ($stmtCars === false) {
    die("Prepare failed: " . $conn->error);
}

$stmtCars->bind_param("s", $ssn);
$stmtCars->execute();
$carsResult = $stmtCars->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Car List</title>
    <style>
        html,
        body {
            height: 100%;
            margin: 0;
        }

        .wrapper {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .content {
            flex: 1;
            /* Allow content to take available space */
        }

        .dropdown-menu {
            min-width: 200px;
            /* Adjust width of the dropdown */
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <div class="container-fluid">
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarTogglerDemo03" aria-controls="navbarTogglerDemo03" aria-expanded="false"
                    aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <a class="navbar-brand nav-link active" href="home.php">Autokanta</a>
                <div class="collapse navbar-collapse" id="navbarTogglerDemo03">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        <li class="nav-item">
                            <a class="nav-link active" aria-current="page" href="#"></a>
                        </li>
                    </ul>
                    <a href="home.php" class="btn btn-danger">Logout</a>
                </div>
            </div>
        </nav>

        <div class="container mt-5 content">
            <h2>Your Profile</h2>
            <?php if ($ownerDetails): ?>
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title"><strong>Name:</strong> <?php echo htmlspecialchars($ownerDetails['name']); ?>
                        </h5>
                        <p class="card-text"><strong>Address:</strong>
                            <?php echo htmlspecialchars($ownerDetails['address']); ?></p>
                        <p class="card-text"><strong>Phone:</strong>
                            <?php echo htmlspecialchars($ownerDetails['phone_number']); ?></p>
                        <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#editProfileModal">Edit
                            Profile</button>
                    </div>
                </div>
            <?php else: ?>
                <p>No owner details found.</p>
            <?php endif; ?>

            <h2>Issue Fine To Car</h2>
            <a href="register_fine.php" class="btn btn-primary mb-3">Issue Fine</a>

            <?php if ($carsResult->num_rows > 0): ?>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Owner Name</th>
                            <th>Owner SSN</th>
                            <th>Registration Number</th>
                            <th>Color</th>
                            <th>Year Model</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($car = $carsResult->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($ownerDetails['name']); ?></td>
                                <td><?php echo htmlspecialchars($car['owner']); ?></td>
                                <td><?php echo htmlspecialchars($car['register']); ?></td>
                                <td><?php echo htmlspecialchars($car['color']); ?></td>
                                <td><?php echo htmlspecialchars($car['year_model']); ?></td>
                                <td>
                                    <!-- Dropdown for Viewing Fines and Adding Car -->
                                    <div class="dropdown">
                                        <button class="btn btn-secondary dropdown-toggle" type="button"
                                            id="dropdownMenuButton<?php echo $car['register']; ?>" data-bs-toggle="dropdown"
                                            aria-expanded="false">
                                            Actions
                                        </button>
                                        <ul class="dropdown-menu"
                                            aria-labelledby="dropdownMenuButton<?php echo $car['register']; ?>">
                                            <li>
                                                <a class="dropdown-item"
                                                    href="view_fines.php?car_id=<?php echo urlencode($car['register']); ?>">View
                                                    Fines</a>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No cars found. Please add a car.</p>
            <?php endif; ?>

            <?php if ($role === 'admin'): ?>
                <h2>Admin Panel</h2>
                <p>Admin-specific functionality goes here.</p>
                <!-- Example Admin Actions -->
                <a href="manage_users.php" class="btn btn-warning mb-3">Manage Users</a>
            <?php endif; ?>
        </div>

        <!-- Edit Profile Modal -->
        <div class="modal fade" id="editProfileModal" tabindex="-1" aria-labelledby="editProfileModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editProfileModalLabel">Edit Profile</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form action="update_profile.php" method="POST">
                            <input type="hidden" name="ssn" value="<?php echo htmlspecialchars($ssn); ?>">
                            <div class="mb-3">
                                <label for="name" class="form-label">Name</label>
                                <input type="text" class="form-control" id="name" name="name"
                                    value="<?php echo htmlspecialchars($ownerDetails['name']); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="address" class="form-label">Address</label>
                                <input type="text" class="form-control" id="address" name="address"
                                    value="<?php echo htmlspecialchars($ownerDetails['address']); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="phone_number" class="form-label">Phone</label>
                                <input type="text" class="form-control" id="phone_number" name="phone_number"
                                    value="<?php echo htmlspecialchars($ownerDetails['phone_number']); ?>" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Update Profile</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <footer class="bg-light text-center py-4">
            <p>&copy; 2024 Autokanta Info. All rights reserved.</p>
        </footer>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>