<?php
session_start();
require 'db.php'; // Include your database connection file

// Initialize variables
$error_message = "";
$success_message = "";
$cars = []; // To hold search results

// Check if the user is logged in and is an admin
if (!isset($_SESSION['ssn'])) {
    header("Location: login.php"); // Redirect to login if not logged in
    exit();
}

// Check if the user is an admin or manager
if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'manager')) {
    // Redirect if the user is neither an admin nor a manager
    header('Location: access_denied.php'); // Redirect if user does not have access
    exit();
}


// Handle search for registered cars
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['search_car'])) {
    $car_search = trim($_POST['car_search']);

    // Validate input
    if (empty($car_search)) {
        $error_message = "Please enter a car registration number to search.";
    } else {
        // Search for registered cars and associated fines
        $stmt = $conn->prepare("
            SELECT c.register, c.owner, f.amount, f.date, f.due_date
            FROM car c
            LEFT JOIN fine f ON c.register = f.car
            WHERE c.register LIKE ?
        ");
        $search_term = "%" . $car_search . "%"; // Wildcard search
        $stmt->bind_param("s", $search_term);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $cars[] = $row; // Add car details to array
            }
        } else {
            $error_message = "No cars found matching your search.";
        }
        $stmt->close();
    }
}

// Handle fine registration
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register_fine'])) {
    // Get form data and sanitize it
    $car = trim($_POST['car']);
    $person = trim($_POST['person']);
    $date = $_POST['date'];
    $amount = $_POST['amount'];
    $reason = trim($_POST['reason']);
    $due_date = $_POST['due_date']; // Get due date from the form

    // Basic validation
    if (empty($car) || empty($person) || empty($date) || empty($amount) || empty($reason) || empty($due_date)) {
        $error_message = "All fields are required.";
    } elseif (!is_numeric($amount) || $amount <= 0) {
        $error_message = "Please enter a valid amount.";
    } else {
        // Prepare the insert statement (add the due_date column)
        $stmt = $conn->prepare("INSERT INTO fine (car, person, date, amount, reason, due_date) VALUES (?, ?, ?, ?, ?, ?)");
        if ($stmt === false) {
            die("Prepare failed: " . $conn->error);
        }

        // Bind parameters, including due date
        $stmt->bind_param("ssddss", $car, $person, $date, $amount, $reason, $due_date);

        // Execute the statement
        if ($stmt->execute()) {
            $success_message = "Fine registered successfully. Due date: " . htmlspecialchars($due_date);
        } else {
            $error_message = "Error: " . $stmt->error;
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Fine Registration Form</title>
</head>

<body>

    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarTogglerDemo03"
                aria-controls="navbarTogglerDemo03" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <a class="navbar-brand nav-link active " href="home.php">Autokanta</a>
            <div class="collapse navbar-collapse" id="navbarTogglerDemo03">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="#"></a>
                    </li>

                </ul>

                <form action="logout.php" method="POST">
                    <a href="index.php" class="btn btn-secondary">Profile</a>
                    <a href="dashboard.php" class="btn btn-primary">Dashboard</a>
                    <button class="btn btn-outline-success" type="submit">Logout</button>
                </form>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <h2>Search for Registered Cars</h2>

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

        <form action="register_fine.php" method="POST">
            <div class="mb-3">
                <label for="car_search" class="form-label">Car Registration Number</label>
                <input type="text" class="form-control" id="car_search" name="car_search" required>
            </div>
            <button type="submit" name="search_car" class="btn btn-primary">Search Car</button>
        </form>

        <!-- Display found cars -->
        <?php if (!empty($cars)): ?>
            <h3 class="mt-4">Search Results:</h3>
            <table class="table">
                <thead>
                    <tr>
                        <th>Car Registration</th>
                        <th>Owner</th>
                        <th>Fine Amount</th>
                        <th>Issue Date</th>
                        <th>Due Date</th> <!-- New column for due date -->
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cars as $car): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($car['register']); ?></td>
                            <td><?php echo htmlspecialchars($car['owner']); ?></td>
                            <td><?php echo htmlspecialchars($car['amount'] !== null ? $car['amount'] : 'No fines'); ?></td>
                            <td><?php echo htmlspecialchars($car['date'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($car['due_date'] ?? 'N/A'); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <!-- Fine Registration Form -->
            <h3 class="mt-4">Register Fine</h3>
            <form action="register_fine.php" method="POST">
                <input type="hidden" name="car" value="<?php echo htmlspecialchars($cars[0]['register']); ?>">
                <input type="hidden" name="person" value="<?php echo htmlspecialchars($cars[0]['owner']); ?>">
                <div class="mb-3">
                    <label for="date" class="form-label">Date</label>
                    <input type="date" class="form-control" id="date" name="date" required>
                </div>
                <div class="mb-3">
                    <label for="amount" class="form-label">Fine Amount</label>
                    <input type="number" step="0.01" class="form-control" id="amount" name="amount" required>
                </div>
                <div class="mb-3">
                    <label for="due_date" class="form-label">Due Date</label>
                    <input type="date" class="form-control" id="due_date" name="due_date" required>
                </div>
                <div class="mb-3">
                    <label for="reason" class="form-label">Reason</label>
                    <input type="text" class="form-control" id="reason" name="reason" maxlength="200" required>
                </div>
                <button type="submit" name="register_fine" class="btn btn-primary">Register Fine</button>

            </form>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>