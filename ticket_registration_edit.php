<?php
session_start();
include_once 'classes/connect.php';
include_once 'classes/Registration.php';

// Only allow admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$registrationClass = new Registration($conn);

// ================= GET REGISTRATION DATA =================
if (!isset($_GET['id'])) {
    header("Location: adminPage.php");
    exit();
}

$reg_id = intval($_GET['id']);
$registration = $registrationClass->getRegistrationById($reg_id);
if (!$registration) {
    $_SESSION['error'] = "Registration not found.";
    header("Location: adminPage.php");
    exit();
}

// ================= HANDLE FORM SUBMISSION =================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $status = $_POST['status'] ?? null;
    if ($registrationClass->updateStatus($reg_id, $status)) {
        $_SESSION['success'] = "Registration status updated successfully!";
        header("Location: adminPage.php");
        exit();
    } else {
        $error = $registrationClass->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Update Registration</title>
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
    width: 350px;
}
h2 {
    text-align: center;
    margin-bottom: 20px;
}
form label {
    display: block;
    margin: 10px 0 5px;
}
form select {
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
    background: #007bff;
    color: #fff;
    border: none;
    border-radius: 5px;
    font-size: 16px;
    cursor: pointer;
}
form button:hover {
    background: #0069d9;
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
<h2>Update Registration Status</h2>

<?php if (!empty($error)): ?>
    <div class="error"><?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>

<form method="POST">
    <label for="status">Status</label>
    <select name="status" id="status" required>
        <option value="pending" <?php if($registration['status']=='pending') echo 'selected'; ?>>Pending</option>
        <option value="approved" <?php if($registration['status']=='approved') echo 'selected'; ?>>Approved</option>
        <option value="rejected" <?php if($registration['status']=='rejected') echo 'selected'; ?>>Rejected</option>
    </select>

    <button type="submit">Update Status</button>
</form>

<a class="back-link" href="adminPage.php">Back to Admin Dashboard</a>
</div>

</body>
</html>