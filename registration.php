<?php
session_start();
$csrf_token = bin2hex(random_bytes(32));
$_SESSION['csrf_token'] = $csrf_token;
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register hello</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

<div class="main-container">

    <div class="navbar">
        <div class="logo">Event System</div>
        <div class="nav-links">
            <a href="#">Home</a>
            <a href="#">Events</a>
            <a href="#">About</a>
            <a href="#" class="active">Register</a>
        </div>
    </div>

    <div class="content">

        <div class="left-panel"></div>

        <div class="right-panel">
            <div class="form-card">

                <p class="sub-text">JOIN THE COMMUNITY</p>
                <h2 class="title">Create a New Account</h2>

                <form method="POST" action="AuthController.php" onsubmit="return validateForm()">

                    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">

                    <div class="form-group">
                        <label>Full Name</label>
                        <input class="form-control" type="text" name="full_name" required minlength="2">
                    </div>

                    <div class="form-group">
                        <label>Email</label>
                        <input class="form-control" type="email" name="email" required>
                    </div>

                    <div class="form-group">
                        <label>Phone</label>
                        <input class="form-control" type="tel" name="phone" required>
                    </div>

                    <div class="form-group">
                        <label>Role</label>
                        <select class="form-control" name="role" required>
                            <option value="Attendee">Attendee</option>
                            <option value="Organizer">Organizer</option>
                        </select>
                    </div>

                    <div class="password-row">
                        <div class="form-group">
                            <label>Password</label>
                            <input class="form-control" type="password" id="password" name="password" required>
                        </div>

                        <div class="form-group">
                            <label>Confirm</label>
                            <input class="form-control" type="password" id="confirm_password" name="confirm_password" required>
                        </div>
                    </div>

                    <button class="btn-register" name="register">Register</button>

                </form>

                <p class="login-text">
                    Already have an account? <a href="login.php">Login</a>
                </p>

            </div>
        </div>
    </div>

    <div class="footer">
        © 2024 Event System
    </div>

</div>

<script>
function validateForm() {
    let pass = document.getElementById("password").value;
    let confirm = document.getElementById("confirm_password").value;

    if (pass.length < 8) {
        alert("Password must be at least 8 characters");
        return false;
    }

    if (pass !== confirm) {
        alert("Passwords do not match");
        return false;
    }

    return true;
}

</script>

</body>
</html>