<?php
session_start();
require 'db.php'; // Include your database connection file

// Role management logic
if (isset($_SESSION['user_ssn'])) {
    $user_ssn = $_SESSION['user_ssn'];
    $role_stmt = $conn->prepare("SELECT role FROM person WHERE ssn = ?");
    $role_stmt->bind_param("s", $user_ssn);
    $role_stmt->execute();
    $role_result = $role_stmt->get_result();

    if ($role_row = $role_result->fetch_assoc()) {
        $user_role = $role_row['role'];

        // Redirect based on role
        if ($user_role === 'admin') {
            header("Location: dashboard.php");
            exit();
        } elseif ($user_role === 'manager') {
            header("Location: register_fine.php");
            exit();
        } else {
            header("Location: index.php");
            exit();
        }
    }
    $role_stmt->close();
}

// Initialize variables
$error_message = "";
$success_message = "";
$fines = [];
$cars = [];

// Fetch fines from the database
$stmt = $conn->prepare("SELECT fine.id, fine.car, fine.date, fine.amount, fine.reason, fine.paid, person.name AS owner 
FROM fine JOIN person ON fine.person = person.ssn");

if ($stmt) {
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $fines[] = $row; // Add fine details to the array
    }
    $stmt->close();
} else {
    $error_message = "Query preparation failed: " . $conn->error;
}

// Fetch cars from the database
$car_stmt = $conn->prepare("
   SELECT 
        car.register, 
        car.color, 
        car.year_model, 
        car.kilometers, 
        fine.amount AS fine_amount, 
        fine.reason AS fine_reason, 
        fine.paid, 
        fine.due_date,
        car.owner AS owner_ssn  -- Make sure the correct field is used here
    FROM 
        car
    LEFT JOIN 
        fine ON car.register = fine.car
");


if ($car_stmt) {
    $car_stmt->execute();
    $car_result = $car_stmt->get_result();

    while ($row = $car_result->fetch_assoc()) {
        $cars[] = $row; // Add car details to the array
    }
    $car_stmt->close();
} else {
    $error_message = "Query preparation failed: " . $conn->error;
}

// Handle fine updates
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['update_fine'])) {
    $fine_id = trim($_POST['fine_id']);
    $amount = trim($_POST['amount']);
    $reason = trim($_POST['reason']);

    // Update the fine in the database
    $update_stmt = $conn->prepare("UPDATE fine SET amount = ?, reason = ? WHERE id = ?");
    if ($update_stmt) {
        $update_stmt->bind_param("ssi", $amount, $reason, $fine_id);
        if ($update_stmt->execute()) {
            $success_message = "Fine updated successfully!";
        } else {
            $error_message = "Error updating fine: " . $update_stmt->error;
        }
        $update_stmt->close();
    } else {
        $error_message = "Query preparation failed: " . $conn->error;
    }
}

// Handle fine deletion
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['delete_fine'])) {
    $fine_id = trim($_POST['fine_id']);

    // Delete the fine from the database
    $delete_stmt = $conn->prepare("DELETE FROM fine WHERE id = ?");
    if ($delete_stmt) {
        $delete_stmt->bind_param("i", $fine_id);
        if ($delete_stmt->execute()) {
            header("Location: dashboard.php");
            exit; // Terminate the script after the redirect
        } else {
            $error_message = "Error deleting fine: " . $delete_stmt->error;
        }
        $delete_stmt->close();
    } else {
        $error_message = "Query preparation failed: " . $conn->error;
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Fine Management Dashboard</title>
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
                <a class="navbar-brand" href="home.php">Autokanta</a>
                <div class="collapse navbar-collapse" id="navbarTogglerDemo03">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        <li class="nav-item">
                            <a href="person_management.php" class="btn btn-secondary me-2">Update User infos</a>
                        </li>
                    </ul>
                    <div>
                        <a href="index.php" class="btn btn-danger me-2">Profile</a>
                        <a href="logout.php" class="btn btn-danger me-2">Logout</a>
                    </div>
                </div>
            </div>
        </nav>


        <div class="container mt-5">
            <h2>Fine Management Dashboard</h2>

            <!-- Display success or error message -->
            <?php if ($success_message): ?>
                <div class="alert alert-success" role="alert">
                    <?php echo htmlspecialchars($success_message); ?>
                </div>
            <?php elseif ($error_message): ?>
                <div class="alert alert-danger" role="alert">
                    <?php echo htmlspecialchars($error_message); ?>
                </div>
            <?php endif; ?>

            <!-- Display fines -->
            <?php if (!empty($fines)): ?>
                <h3>Fines</h3>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Car Registration</th>
                            <th>Owner Name</th>
                            <th>Date</th>
                            <th>Amount</th>
                            <th>Reason</th>
                            <th>Paid</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($fines as $fine): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($fine['car']); ?></td>
                                <td><?php echo htmlspecialchars($fine['owner']); ?></td>
                                <td><?php echo htmlspecialchars($fine['date']); ?></td>
                                <td><?php echo htmlspecialchars($fine['amount']); ?></td>
                                <td><?php echo htmlspecialchars($fine['reason']); ?></td>
                                <td><?php echo $fine['paid'] ? 'Yes' : 'No'; ?></td>
                                <td>
                                    <!-- Button to open the edit modal -->
                                    <button class="btn btn-primary" data-bs-toggle="modal"
                                        data-bs-target="#editFineModal<?php echo htmlspecialchars($fine['id']); ?>">
                                        Edit
                                    </button>
                                    <!-- Edit Fine Modal -->
                                    <div class="modal fade" id="editFineModal<?php echo htmlspecialchars($fine['id']); ?>"
                                        tabindex="-1" aria-labelledby="editFineModalLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="editFineModalLabel">Edit Fine</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <form action="dashboard.php" method="POST">
                                                        <input type="hidden" name="fine_id"
                                                            value="<?php echo htmlspecialchars($fine['id']); ?>">
                                                        <div class="mb-3">
                                                            <label for="amount" class="form-label">Amount</label>
                                                            <input type="number" step="0.01" class="form-control" id="amount"
                                                                name="amount"
                                                                value="<?php echo htmlspecialchars($fine['amount']); ?>"
                                                                required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="reason" class="form-label">Reason</label>
                                                            <input type="text" class="form-control" id="reason" name="reason"
                                                                value="<?php echo htmlspecialchars($fine['reason']); ?>"
                                                                maxlength="200" required>
                                                        </div>
                                                        <button type="submit" name="update_fine" class="btn btn-primary">Update
                                                            Fine</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Delete Fine Form -->
                                    <form action="dashboard.php" method="POST" style="display:inline;">
                                        <input type="hidden" name="fine_id"
                                            value="<?php echo htmlspecialchars($fine['id']); ?>">
                                        <button type="submit" name="delete_fine" class="btn btn-danger"
                                            onclick="return confirm('Are you sure you want to delete this fine?');">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <div><a href="register_fine.php" class="btn btn-primary p">Register Fine</a></div>
            <?php else: ?>
                <p>No fines found.</p>
            <?php endif; ?>
            <h3 class="mt-5">Registered Cars</h3>
            <?php if (!empty($cars)): ?>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Car Registration</th>
                            <th>Owner SSN</th> <!-- Changed to Owner SSN -->
                            <th>Color</th>
                            <th>Year Model</th>
                            <th>Kilometers</th>
                            <th>Fine Amount</th>
                            <th>Fine Reason</th>
                            <th>Paid</th>
                            <th>Due Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($cars as $car): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($car['register']); ?></td>
                                <td><?php echo htmlspecialchars($car['owner_ssn']); ?></td> <!-- Changed to display SSN -->
                                <td><?php echo htmlspecialchars($car['color']); ?></td>
                                <td><?php echo htmlspecialchars($car['year_model']); ?></td>
                                <td><?php echo htmlspecialchars($car['kilometers']); ?></td>
                                <td><?php echo htmlspecialchars($car['fine_amount']) ?: 'No fine'; ?></td>
                                <td><?php echo htmlspecialchars($car['fine_reason']) ?: 'No fine'; ?></td>
                                <td><?php echo $car['paid'] ? 'Yes' : 'No'; ?></td>
                                <td><?php echo htmlspecialchars($car['due_date']) ?: 'N/A'; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No registered cars found.</p>
            <?php endif; ?>
        </div>
    </div>
    <!-- Footer -->
    <footer class="bg-light text-center py-4">
        <p>&copy; 2024 Autokanta Info. All rights reserved.</p>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>