<?php
function create_user($conn, $ssn, $name, $password, $address, $phone_number)
{
    // Check if the username already exists  
    $query = $conn->prepare("SELECT * FROM person WHERE name = ? LIMIT 1");
    $query->bind_param("s", $name);
    $query->execute();
    $result = $query->get_result();

    if ($result && $result->num_rows > 0) {
        // Username already exists  
        return false;
    }

    // Hash the password before storing it  
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Prepare an SQL statement to insert the new user  
    $insert_query = $conn->prepare("INSERT INTO person (ssn, name, password, address, phone_number, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
    $insert_query->bind_param("sssss", $ssn, $name, $hashed_password, $address, $phone_number);

    // Execute and check if the user was created successfully  
    if ($insert_query->execute()) {
        return true; // User created successfully  
    } else {
        return false; // Failed to create user  
    }
}


function login_user($conn, $name, $password)
{
    $query = $conn->prepare("SELECT * FROM person WHERE name = ? LIMIT 1");
    if (!$query) {
        die("Query Preparation Failed: " . $conn->error);
    }

    $query->bind_param("s", $name);

    if (!$query->execute()) {
        die("Query Execution Failed: " . $query->error);
    }

    $result = $query->get_result();

    if ($result && $result->num_rows > 0) {
        $name = $result->fetch_assoc();
        if (password_verify($password, $name['password'])) {
            return $name; // Return user data if login is successful
        }
    }

    return false; // Return false if login failed
}

function check_login($conn)
{
    // Ensure the session is started
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    // Check if the session variable for name is set
    if (isset($_SESSION['name'])) {
        $name = $_SESSION['name'];

        // Use prepared statements to prevent SQL injection
        $query = $conn->prepare("SELECT * FROM person WHERE name = ? LIMIT 1");
        if (!$query) {
            die("Query Preparation Failed: " . $conn->error);
        }

        // Bind parameters
        $query->bind_param("s", $name);

        // Execute the statement
        if (!$query->execute()) {
            die("Query Execution Failed: " . $query->error);
        }

        $result = $query->get_result();
        if ($result && $result->num_rows > 0) {
            return $result->fetch_assoc(); // Return the user data
        }
    }

    // Redirect to login if user is not found or not logged in
    header("Location: login.php");
    die;
}

?>