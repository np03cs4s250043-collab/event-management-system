<?php
if ($_SERVER["REQUEST_METHOD"] != "POST") {
    echo "Please login properly!";
    exit();
}
$conn = new mysqli("localhost", "root", "", "event_management_system");

$username = $_POST['username'];
$password = $_POST['password'];

$sql = "SELECT * FROM users WHERE username='$username'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();


    if (password_verify($password, $user['password'])) {
        echo "Login Successful!";
    } else {
        echo "Wrong Password!";
    }
} else {
    echo "User not found!";
}
$conn->close();
?>
