<?php
session_start();
require 'db.php'; // Include the database connection

// Initialize variables
$error_message = '';
$success_message = '';
$car_info = [];

// Handle car information submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Adding car info
    if (isset($_POST['add_car'])) {
        $car_number = trim($_POST['register']);

        // Basic validation
        if (empty($car_number)) {
            $error_message = "Car number is required.";
        } else {
            // Prepare the SQL statement
            $stmt = $conn->prepare("INSERT INTO car_info (register) VALUES (?)");
            $stmt->bind_param("s", $car_number);

            if ($stmt->execute()) {
                $success_message = "Car number added successfully.";
            } else {
                $error_message = "Error: " . $stmt->error;
            }

            $stmt->close();
        }
    }

    // Searching for car info
    if (isset($_POST['search_car'])) {
        $search_register = trim($_POST['search_register']);

        // Prepare the SQL statement for searching
        $stmt = $conn->prepare("SELECT register, color, year_model, kilometers FROM car WHERE register = ?");
        $stmt->bind_param("s", $search_register);
        $stmt->execute();
        $result = $stmt->get_result();

        // Fetch the car information
        if ($result->num_rows > 0) {
            $car_info = $result->fetch_assoc();
        } else {
            $error_message = "No car found with that registration number.";
        }

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
    <title>Traffic Website</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .overlay {
            background: rgba(0, 0, 0, 0.5);
            height: 100%;
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            z-index: 1;
        }

        .card-img-top {
            width: 100%;
            /* Ensure it takes full width */
            height: auto;
            /* Maintain aspect ratio */
            display: block;
            /* Block-level element to allow centering */
            margin: 0 auto;
            /* Center the image */
        }

        /* Optional: Customize button colors */
        .btn-primary {
            background-color: #007bff;
            /* Change to your preferred color */
            border: none;
        }

        .btn-secondary {
            background-color: #6c757d;
            /* Change to your preferred color */
            border: none;
        }
    </style>
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href="#">Traffic Info</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" href="#">About</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Traffic Updates</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Contact</a>
                </li>
            </ul>
        </div>
    </nav>
    <!-- Hero Section with Carousel -->
    <div id="heroCarousel" class="carousel slide" data-ride="carousel" data-interval="5000"
        style="height: 50vh; position: relative;">
        <!-- Carousel Indicators (optional) -->
        <ol class="carousel-indicators">
            <li data-target="#heroCarousel" data-slide-to="0" class="active"></li>
            <li data-target="#heroCarousel" data-slide-to="1"></li>
            <li data-target="#heroCarousel" data-slide-to="2"></li>
        </ol>

        <!-- Carousel Inner -->
        <div class="carousel-inner" style="height: 100%;">
            <div class="carousel-item active"
                style="background: url('./image/finland-in-pictures-beautiful-places-to-photograph-turku.jpg') no-repeat center center; background-size: cover; height: 100%;">
                <div class="overlay"></div>
                <div class="container d-flex align-items-center justify-content-center text-center"
                    style="height: 100%; z-index: 2; position: relative;">
                    <div>
                        <h1 class="text-white">Stay Updated on Traffic Conditions</h1>
                        <p class="text-white">Your guide to safer and faster travel.</p>
                        <a href="register_person.php" class="btn btn-primary btn-lg">Register</a>
                        <a href="login.php" class="btn btn-secondary btn-lg">Login</a>
                    </div>
                </div>
            </div>
            <div class="carousel-item"
                style="background: url('./image/tapio-haaja-I9SWvZ9sO2U-unsplash.jpg') no-repeat center center; background-size: cover; height: 100%;">
                <div class="overlay"></div>
                <div class="container d-flex align-items-center justify-content-center text-center"
                    style="height: 100%; z-index: 2; position: relative;">
                    <div>
                        <h1 class="text-white">Get Real-Time Traffic Updates</h1>
                        <p class="text-white">Never miss any critical updates while traveling.</p>
                        <a href="register_person.php" class="btn btn-primary btn-lg">Register</a>
                        <a href="login.php" class="btn btn-secondary btn-lg">Login</a>
                    </div>
                </div>
            </div>
            <div class="carousel-item"
                style="background: url('./image/kristaps-grundsteins-phv0kzqMJyk-unsplash.jpg') no-repeat center center; background-size: cover; height: 100%;">
                <div class="overlay"></div>
                <div class="container d-flex align-items-center justify-content-center text-center"
                    style="height: 100%; z-index: 2; position: relative;">
                    <div>
                        <h1 class="text-white">Plan Your Routes with Ease</h1>
                        <p class="text-white">Stay ahead and plan safer routes.</p>
                        <a href="register_person.php" class="btn btn-primary btn-lg">Register</a>
                        <a href="login.php" class="btn btn-secondary btn-lg">Login</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Carousel Controls (optional) -->
        <a class="carousel-control-prev" href="#heroCarousel" role="button" data-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="sr-only">Previous</span>
        </a>
        <a class="carousel-control-next" href="#heroCarousel" role="button" data-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="sr-only">Next</span>
        </a>
    </div>
    <div>
        <!-- Search Car Information Section -->
        <div class="container mt-5">
            <h2 class="text-center mb-4">Search Car Information</h2>
            <form action="" method="POST" class="mb-4">
                <div class="mb-3">
                    <label for="search_register" class="form-label">Car Registration Number</label>
                    <input type="text" class="form-control" id="search_register" name="search_register" required>
                </div>
                <button type="submit" name="search_car" class="btn btn-primary btn-lg">Search</button>
            </form>

            <?php if (!empty($car_info)): ?>
                <div class="card mt-4">
                    <div class="card-header">
                        <h4 class="mb-0">Car Information:</h4>
                    </div>
                    <div class="card-body">
                        <p class="card-text"><strong>Registration Number:</strong>
                            <?php echo htmlspecialchars($car_info['register']); ?></p>
                        <p class="card-text"><strong>Color:</strong> <?php echo htmlspecialchars($car_info['color']); ?></p>
                        <p class="card-text"><strong>Year Model:</strong>
                            <?php echo htmlspecialchars($car_info['year_model']); ?></p>
                        <p class="card-text"><strong>Kilometers:</strong>
                            <?php echo htmlspecialchars($car_info['kilometers']); ?></p>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (!empty($error_message) && empty($car_info)): ?>
                <div class="alert alert-danger mt-3" role="alert">
                    <?php echo htmlspecialchars($error_message); ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <!-- Features Section -->
    <div class="container my-5">
        <h2 class="text-center">Our Features</h2>
        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <img src="./image/website_steps_-01.jpg" class="card-img-top" alt="Traffic Updates">
                    <div class="card-body">
                        <h5 class="card-title">Real-Time Updates</h5>
                        <p class="card-text" id="trafficUpdates">Loading updates...</p>
                        <button class="btn btn-primary" onclick="fetchTrafficUpdates()">Check Updates</button>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <img src="./image/website_steps_-03.jpg" class="card-img-top" alt="Route Planner">
                    <div class="card-body">
                        <h5 class="card-title">Route Planner</h5>
                        <p class="card-text" id="routeInfo">Loading route info...</p>
                        <button class="btn btn-primary" onclick="fetchRoute()">Get Route</button>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <img src="./image/website_steps_-02__1_.jpg" class="card-img-top" alt="Accident Alerts">
                    <div class="card-body">
                        <h5 class="card-title">Accident Alerts</h5>
                        <p class="card-text" id="accidentAlerts">Loading alerts...</p>
                        <button class="btn btn-primary" onclick="fetchAccidentAlerts()">Check Alerts</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-light text-center py-4">
        <p>&copy; 2024 Traffic Info. All rights reserved.</p>
    </footer>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>

</html>