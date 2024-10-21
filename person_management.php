<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Edit Person Information</title>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarTogglerDemo03"
                aria-controls="navbarTogglerDemo03" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <a class="navbar-brand" href="#">Navbar</a>
            <div class="collapse navbar-collapse" id="navbarTogglerDemo03">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="#">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Link</a>
                    </li>
                </ul>
                <form class="d-flex">
                    <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search">
                    <button class="btn btn-outline-success" type="submit">Search</button>
                </form>
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