<?php
require_once '../classes/connect.php'; // Your database connection file
require_once '../classes/User.php';          // The file containing the class you shared

$user = new User($conn);

// Parameters: $username, $email, $password, $phone, $role
if ($user->register('kwizera', 'KwizeraAdmin@gmail.com', '12345', '07933109595', 'admin')) {
    echo "Admin created successfully!";
} else {
    echo "Error: " . $user->error;
}
?>