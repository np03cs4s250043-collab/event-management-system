<?php
session_start();
require_once "config/db.php";

if (isset($_POST['register'])) {

    // CSRF CHECK
    if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Invalid CSRF token");
    }

    $name  = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $role  = $_POST['role'];
    $pass  = $_POST['password'];
    $confirm = $_POST['confirm_password'];

    // VALIDATION
    if (strlen($name) < 2) {
        die("Name too short");
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Invalid email");
    }

    if ($pass !== $confirm) {
        die("Passwords do not match");
    }

    if (strlen($pass) < 8) {
        die("Password must be at least 8 characters");
    }

    // CHECK EMAIL
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);

    if ($stmt->rowCount() > 0) {
        die("Email already exists");
    }

    // HASH PASSWORD
    $hashed_password = password_hash($pass, PASSWORD_BCRYPT);

    // INSERT
    $stmt = $conn->prepare("INSERT INTO users (full_name, email, phone, role, password) VALUES (?, ?, ?, ?, ?)");

    if ($stmt->execute([$name, $email, $phone, $role, $hashed_password])) {
        header("Location: login.php");
    } else {
        echo "Error occurred";
    }
}