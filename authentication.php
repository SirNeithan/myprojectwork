<?php
session_start();
include 'db/db_connect.php';

function redirectWithMessage($location, $message, $type = 'error') {
    $_SESSION['flash'] = $message;
    $_SESSION['flash_type'] = $type;
    header("Location: $location");
    exit();
}

if (isset($_POST['signUp'])) {
    $username = $conn->real_escape_string($_POST['username']);
    $firstName = $conn->real_escape_string($_POST['firstName']);
    $lastName = $conn->real_escape_string($_POST['lastName']);
    $phone = $conn->real_escape_string($_POST['phone']);
    $email = $conn->real_escape_string($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        redirectWithMessage("index.php", "Email already exists.");
    } else {
        $stmt = $conn->prepare("INSERT INTO users (username, firstName, lastName, phone, email, password) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $username, $firstName, $lastName, $phone, $email, $password);
        if ($stmt->execute()) {
            redirectWithMessage("index.php", "Registration successful. Please log in.", "success");
        } else {
            redirectWithMessage("index.php", "Error: " . $stmt->error);
        }
    }
}

if (isset($_POST['signIn'])) {
    $email = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {
            $_SESSION['userID'] = $user['userID'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['email'] = $user['email'];

            header("Location: dashboard.php");
            exit();
        } else {
            redirectWithMessage("index.php", "Invalid email or password.");
        }
    } else {
        redirectWithMessage("index.php", "Invalid email or password.");
    }
    $stmt->close();
}

$conn->close();
