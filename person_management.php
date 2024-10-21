<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Edit Person Information</title>
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
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="home.php">Autokanta</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarTogglerDemo03"
                aria-controls="navbarTogglerDemo03" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarTogglerDemo03" style="justify-content: flex-end;">
                <a href="dashboard.php" class="btn btn-danger">Dashboard</a>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <h2>Edit Person Information</h2>
        <form>
            <div class="mb-3">
                <label for="ssn" class="form-label">SSN</label>
                <input type="text" class="form-control" id="ssn" maxlength="11" required readonly>
            </div>
            <div class="mb-3">
                <div class="input-group">
                    <input type="text" class="form-control" id="name" maxlength="50" required>
                    <button type="button" class="btn btn-secondary">Edit</button>
                </div>
                <label for="name" class="form-label mt-2">Name</label>
            </div>
            <div class="mb-3">
                <div class="input-group">
                    <input type="text" class="form-control" id="phone_number" maxlength="20" required>
                    <button type="button" class="btn btn-secondary">Edit</button>
                </div>
                <label for="phone_number" class="form-label mt-2">Phone Number</label>
            </div>
            <div class="mb-3">
                <div class="input-group">
                    <input type="text" class="form-control" id="address" maxlength="100" required>
                    <button type="button" class="btn btn-secondary">Edit</button>
                </div>
                <label for="address" class="form-label mt-2">Address</label>
            </div>
            <button type="submit" class="btn btn-primary">Update Information</button>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>