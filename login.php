<?php
// Start session at the very top
session_start();

// Include database connection
include_once 'classes/connect.php';
include_once 'classes/User.php'; // Include User class

// Initialize error message
$error = "";

// Handle login form submission
if (isset($_POST['login'])) {

    // Sanitize input
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    // Create User object
    $userObj = new User($conn);

    // Attempt login using User class
    $user = $userObj->login($email, $password);

    if ($user) {
        // Store session variables correctly
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['username'] = $user['username']; // Correct key
        $_SESSION['role'] = $user['role'];
   
        // Redirect based on role
        if ($user['role'] === 'admin') {
            header("Location: adminPage.php"); // Path to your admin page
        } else {
            header("Location: userPage.php"); // Path to your regular user page
        }
        exit();
    } else {
        $error = $userObj->error; // Get error from User class
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UMUCO EVENTS | Login</title>
    <link rel="stylesheet" href="css/style_register.css">
    <style>
            /* Buttons */
        .form-container .button {
            width: 100%;
            padding: 14px 0;
            background: #4caf78;
            border: none;
            border-radius: 6px;
            color: #fff;
            font-size: 1.1em;
            font-weight: bold;
            cursor: pointer;
            transition: 0.3s;
        }

        .form-container button:hover {
            background: #7a8a35;
            transform: translateY(-2px);
        }
    </style>
</head>
<body>

<?php require 'utils/indexHeader.php'; ?>

<div class="page-content">
    <div class="form-wrapper">
        <div class="form-container">
            <h1>Login to Your Account</h1>

            <?php if (!empty($error)) : ?>
                <p style="color:tomato;"class="error-msg"><?php echo htmlspecialchars($error); ?></p>
            <?php endif; ?>

            <form method="POST">
                <input type="email" name="email" placeholder="Email Address" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit" name="login" class="button">Login</button>
                <p>Don't have an account? <a href="register.php">Register here</a></p>
            </form>
        </div>
    </div>
</div>

<?php require 'utils/footer.php'; ?>

</body>
</html>