<?php
session_start();
include_once 'classes/connect.php';

// Prepare a message to show success/failure
$message = "";
$messageClass = "";

if(isset($_POST['register'])){
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if($password !== $confirm_password){
        $message = "Passwords do not match.";
        $messageClass = "error-message";
    } else {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        $check = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");
        if(mysqli_num_rows($check) > 0){
            $message = "Email already registered.";
            $messageClass = "error-message";
        } else {
            $insert = mysqli_query($conn, "INSERT INTO users(username,email,phone,password) 
                                           VALUES('$username','$email','$phone','$password_hash')");
            if($insert){
                $message = "Registration successful! Please login.";
                $messageClass = "success-message"; // you can style green
            } else {
                $message = "Registration failed. Try again.";
                $messageClass = "error-message";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>UMUCO EVENTS | Register</title>
    <link rel="stylesheet" href="css/style_register.css">
    <style>
        .error-message { color: red; font-size: 0.9em; margin-bottom: 10px; }
        .success-message { color: green; font-size: 0.9em; margin-bottom: 10px; }
        .input-error { border: 1px solid red; }
    </style>
</head>
<body>

<?php require 'utils/indexHeader.php'; ?>

<div class="page-content">
    <div class="form-wrapper">
        <div class="form-container">
            <h1>Create Your Account</h1>

            <!-- Message div for PHP feedback -->
            <?php if(!empty($message)): ?>
                <div class="<?php echo $messageClass; ?>"><?php echo $message; ?></div>
            <?php endif; ?>

            <form method="POST" id="registerForm">
                <input type="text" name="username" placeholder="Full Name" required>
                <div class="error-message" id="usernameError"></div>

                <input type="email" name="email" placeholder="Email Address" required>
                <div class="error-message" id="emailError"></div>

                <input type="tel" name="phone" placeholder="Phone Number">
                <div class="error-message" id="phoneError"></div>

                <input type="password" name="password" placeholder="Password" required>
                <div class="error-message" id="passwordError"></div>

                <input type="password" name="confirm_password" placeholder="Confirm Password" required>
                <div class="error-message" id="confirmPasswordError"></div>

                <button type="submit" name="register">Register</button>

                <p>Already have an account? <a href="login.php">Login here</a></p>

                <!-- General form error -->
                <div class="error-message" id="formError"></div>
            </form>
        </div>
    </div>
</div>

<?php require 'utils/footer.php'; ?>

<!-- External JS validation -->
<script src="validateRegister.js"></script>

</body>
</html>