<?php
session_start();
include_once 'classes/connect.php';
include_once 'classes/User.php';

// Only allow admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$userClass = new User($conn);

// ================= GET USER DATA =================
if (!isset($_GET['id'])) {
    header("Location: adminPage.php");
    exit();
}

$user_id = intval($_GET['id']);
$user = $userClass->getUserById($user_id);
if (!$user) {
    $_SESSION['error'] = "User not found.";
    header("Location: adminPage.php");
    exit();
}

// ================= HANDLE FORM SUBMISSION =================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $updateData = [
        'username' => $_POST['username'] ?? null,
        'email' => $_POST['email'] ?? null,
        'phone' => $_POST['phone'] ?? null,
        'role' => $_POST['role'] ?? null,
        'password' => $_POST['password'] ?? null
    ];

    if ($userClass->updateUser($user_id, $updateData)) {
        // Success: redirect back to admin page
        $_SESSION['success'] = $userClass->success;
        header("Location: adminPage.php");
        exit();
    } else {
        $error = $userClass->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Edit User</title>
<style>
body {
    font-family: Arial, sans-serif;
    background: #f4f4f4;
    display: flex;
    justify-content: center;
    align-items: flex-start;
    padding: 50px 0;
}
.container {
    background: #fff;
    padding: 25px 30px;
    border-radius: 10px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    width: 400px;
}
h2 {
    text-align: center;
    margin-bottom: 20px;
}
form label {
    display: block;
    margin: 10px 0 5px;
}
form input, form select {
    width: 100%;
    padding: 8px 10px;
    margin-bottom: 15px;
    border-radius: 5px;
    border: 1px solid #ccc;
    box-sizing: border-box;
}
form button {
    width: 100%;
    padding: 10px;
    background: #28a745;
    color: #fff;
    border: none;
    border-radius: 5px;
    font-size: 16px;
    cursor: pointer;
}
form button:hover {
    background: #218838;
}
.error {
    color: #dc3545;
    margin-bottom: 15px;
    text-align: center;
}
.success {
    color: #28a745;
    margin-bottom: 15px;
    text-align: center;
}
a.back-link {
    display: block;
    margin-top: 15px;
    text-align: center;
    color: #007bff;
    text-decoration: none;
}
a.back-link:hover {
    text-decoration: underline;
}
</style>
</head>
<body>

<div class="container">
<h2>Edit User</h2>

<?php if (!empty($error)): ?>
    <div class="error"><?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>

<form method="POST">
    <label for="username">Username</label>
    <input type="text" name="username" id="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>

    <label for="email">Email</label>
    <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>

    <label for="phone">Phone</label>
    <input type="text" name="phone" id="phone" value="<?php echo htmlspecialchars($user['phone']); ?>">

    <label for="role">Role</label>
    <select name="role" id="role" required>
        <option value="user" <?php if($user['role']=='user') echo 'selected'; ?>>User</option>
        <option value="admin" <?php if($user['role']=='admin') echo 'selected'; ?>>Admin</option>
    </select>

    <label for="password">Password <small>(leave blank to keep current)</small></label>
    <input type="password" name="password" id="password" placeholder="New password">

    <button type="submit">Update User</button>
</form>

<a class="back-link" href="adminPage.php">Back to Admin Dashboard</a>
</div>

</body>
</html>