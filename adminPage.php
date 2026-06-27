<?php
session_start();
include_once 'classes/connect.php';
include_once 'classes/User.php';
include_once 'classes/Events.php';
include_once 'classes/Registration.php';
include_once 'classes/Ticket.php';
include_once 'classes/Feedback.php';

// Restrict access to admin only
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Initialize classes
$userClass = new User($conn);
$eventsClass = new Events($conn);
$registrationClass = new Registration($conn);
$ticketClass = new Ticket($conn);
$feedbackClass = new Feedback($conn);

// ================= UPDATE HANDLERS =================

// Update Event
if (isset($_POST['update_event'])) {
    $eventsClass->updateEvent((int) $_POST['event_id'], $_POST);
}

// Update Registration (status only)
if (isset($_POST['update_registration'])) {
    $registrationClass->updateStatus((int) $_POST['registration_id'], $_POST['status']);
}

// ================= HELPER FUNCTIONS =================

/**
 * Generic SELECT helper using PDO. $table must come from a fixed
 * internal whitelist — never from user input — since table names
 * can't be parameterized in a prepared statement.
 */
function safeQuery(PDO $conn, string $sql): array {
    try {
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("adminPage safeQuery failed: " . $e->getMessage());
        return [];
    }
}

function countTable(PDO $conn, string $table): int {
    // Whitelist guards against ever interpolating arbitrary table names
    $allowed = ['events', 'users', 'registrations', 'tickets', 'feedback'];
    if (!in_array($table, $allowed, true)) {
        return 0;
    }
    try {
        $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM `$table`");
        $stmt->execute();
        return (int) $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    } catch (PDOException $e) {
        error_log("adminPage countTable failed: " . $e->getMessage());
        return 0;
    }
}

// ================= ACTIONS =================

// DELETE
if (isset($_GET['delete'], $_GET['type'])) {
    $type = $_GET['type'];
    $id = intval($_GET['delete']);

    switch ($type) {
        case "event":
            $eventsClass->deleteEvent($id);
            break;
        case "user":
            $userClass->deleteUser($id);
            break;
        case "registration":
            $registrationClass->deleteRegistration($id);
            break;
        case "ticket":
            $ticketClass->deleteTicket($id);
            break;
        case "feedback":
            $feedbackClass->deleteFeedback($id);
            break;
    }
    header("Location: adminPage.php");
    exit();
}

// APPROVE / REJECT REGISTRATION
if (isset($_GET['action'], $_GET['reg_id'])) {
    $action = $_GET['action'];
    $id = intval($_GET['reg_id']);

    $validActions = ['approve' => 'approved', 'reject' => 'rejected'];

    if (array_key_exists($action, $validActions)) {
        $status = $validActions[$action];
        $registrationClass->updateStatus($id, $status);
    }
    header("Location: adminPage.php");
    exit();
}

// ================= CSV EXPORT =================
if (isset($_GET['export'])) {
    $type = $_GET['export'];
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="' . $type . '_data.csv"');
    $output = fopen("php://output", "w");

    switch ($type) {
        case "events":
            fputcsv($output, ["ID", "Title", "Category", "Date", "Price", "Total Capacity"]);
            $rows = safeQuery($conn, "SELECT * FROM events");
            foreach ($rows as $row) {
                $eventsClass->max_capacity = (int) $row['max_capacity'];
                $eventsClass->price = (float) $row['price'];
                $totalCost = $eventsClass->calculateTotal();
                fputcsv($output, [
                    $row['event_id'],
                    $row['event_title'],
                    $row['category'],
                    $row['event_date'],
                    $row['price'],
                    $totalCost
                ]);
            }
            break;

        case "users":
            fputcsv($output, ["ID", "Username", "Email", "Role"]);
            $rows = safeQuery($conn, "SELECT * FROM users");
            foreach ($rows as $row) {
                fputcsv($output, [$row['user_id'], $row['username'], $row['email'], $row['role']]);
            }
            break;

        case "registrations":
            fputcsv($output, ["ID", "User", "Event", "Ticket Code", "Status"]);
            $rows = safeQuery($conn, "
                SELECT r.*, u.username, e.event_title 
                FROM registrations r
                JOIN users u ON r.user_id = u.user_id
                JOIN events e ON r.event_id = e.event_id
            ");
            foreach ($rows as $row) {
                fputcsv($output, [$row['registration_id'], $row['username'], $row['event_title'], $row['ticket_code'], $row['status']]);
            }
            break;

        case "tickets":
            fputcsv($output, ["ID", "Ticket Code", "QR Code"]);
            $rows = safeQuery($conn, "
                SELECT t.*, r.ticket_code 
                FROM tickets t 
                JOIN registrations r ON t.registration_id = r.registration_id
            ");
            foreach ($rows as $row) {
                fputcsv($output, [$row['ticket_id'], $row['ticket_code'], $row['qr_code']]);
            }
            break;

        case "feedback":
            fputcsv($output, ["Name", "Email", "Message"]);
            $rows = safeQuery($conn, "SELECT * FROM feedback");
            foreach ($rows as $row) {
                fputcsv($output, [$row['name'], $row['email'], $row['message']]);
            }
            break;
    }

    fclose($output);
    exit();
}

// ================= FETCH DATA =================
$events = safeQuery($conn, "SELECT * FROM events");
$users = safeQuery($conn, "SELECT * FROM users");
$registrations = safeQuery($conn, "
    SELECT r.*, u.username, e.event_title 
    FROM registrations r
    JOIN users u ON r.user_id = u.user_id
    JOIN events e ON r.event_id = e.event_id
");
$tickets = safeQuery($conn, "
    SELECT t.*, r.ticket_code 
    FROM tickets t 
    JOIN registrations r ON t.registration_id = r.registration_id
");
$feedbacks = safeQuery($conn, "SELECT * FROM feedback");

// ================= COUNTS =================
$totalEvents = countTable($conn, "events");
$totalUsers = countTable($conn, "users");
$totalRegistrations = countTable($conn, "registrations");
$totalTickets = countTable($conn, "tickets");
$totalFeedback = countTable($conn, "feedback");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Dashboard</title>
<link rel="stylesheet" href="css/admin.css">
<script src="script.js"></script>
</head>
<body>
<?php require 'utils/adminHeader.php'; ?>

<main class="admin-dashboard">
<h1>Admin Dashboard</h1>

<!-- STATS -->
<div class="stats-container">
    <div class="card">Events<span><?php echo $totalEvents; ?></span></div>
    <div class="card">Users<span><?php echo $totalUsers; ?></span></div>
    <div class="card">Registrations<span><?php echo $totalRegistrations; ?></span></div>
    <div class="card">Tickets<span><?php echo $totalTickets; ?></span></div>
    <div class="card">Feedback<span><?php echo $totalFeedback; ?></span></div>
</div>

<!-- TABLE SELECTOR -->
<div class="table-selector">
<select onchange="showTable(this.value)">
    <option value="eventsTable">Events</option>
    <option value="usersTable">Users</option>
    <option value="registrationsTable">Registrations</option>
    <option value="ticketsTable">Tickets</option>
    <option value="feedbackTable">Feedback</option>
</select>
</div>

<!-- EVENTS TABLE -->
<section id="eventsTable" class="table-section">
<h2>Events <a href="?export=events" class="btn export">Export CSV</a></h2>
<input type="text" placeholder="Search..." onkeyup="searchTable('eSearch','eventsTable')" id="eSearch">
<table>
<thead>
<tr>
<th>ID</th><th>Title</th><th>Category</th><th>Date</th><th>Price</th><th>Total Price</th><th>Actions</th>
</tr>
</thead>
<tbody>
<?php foreach ($events as $row) { 
    $eventsClass->max_capacity = (int) $row['max_capacity'];
    $eventsClass->price = (float) $row['price'];
    $totalCost = $eventsClass->calculateTotal();
?>
<tr>
<td><?php echo $row['event_id']; ?></td>
<td><?php echo htmlspecialchars($row['event_title']); ?></td>
<td><?php echo htmlspecialchars($row['category']); ?></td>
<td><?php echo $row['event_date']; ?></td>
<td>Rwf <?php echo $row['price']; ?></td>
<td>Rwf <?php echo $totalCost; ?></td>
<td>
<!-- <a href="edit_event.php?id=<?php echo $row['event_id']; ?>">Edit</a> | -->
<a href="?type=event&delete=<?php echo $row['event_id']; ?>" onclick="return confirm('Delete event?')">Delete</a>
</td>
</tr>
<?php } ?>
</tbody>
</table>
</section>

<!-- USERS TABLE -->
<section id="usersTable" class="table-section" style="display:none;">
<h2>Users <a href="?export=users" class="btn export">Export CSV</a></h2>
<input type="text" placeholder="Search..." onkeyup="searchTable('uSearch','usersTable')" id="uSearch">
<table>
<thead>
<tr><th>ID</th><th>Name</th><th>Email</th><th>Role</th><th>Actions</th></tr>
</thead>
<tbody>
<?php foreach ($users as $row) { ?>
<tr>
<td><?php echo $row['user_id']; ?></td>
<td><?php echo htmlspecialchars($row['username']); ?></td>
<td><?php echo htmlspecialchars($row['email']); ?></td>
<td><?php echo htmlspecialchars($row['role']); ?></td>
<td>
<a href="edit_user.php?id=<?php echo $row['user_id']; ?>">Edit</a> |
<a href="?type=user&delete=<?php echo $row['user_id']; ?>" onclick="return confirm('Delete user?')">Delete</a>
</td>
</tr> 
<?php } ?>           
</tbody>
</table>
</section>

<!-- REGISTRATIONS TABLE -->
<section id="registrationsTable" class="table-section" style="display:none;">
<h2>Registrations <a href="?export=registrations" class="btn export">Export CSV</a></h2>
<input type="text" placeholder="Search..." onkeyup="searchTable('rSearch','registrationsTable')" id="rSearch">
<table>
<thead>
<tr>
<th>ID</th><th>User</th><th>Event</th><th>Code</th><th>Status</th><th>Actions</th>
</tr>
</thead>
<tbody>
<?php foreach ($registrations as $row) { ?>
<tr>
<td><?php echo $row['registration_id']; ?></td>
<td><?php echo htmlspecialchars($row['username']); ?></td>
<td><?php echo htmlspecialchars($row['event_title']); ?></td>
<td><?php echo htmlspecialchars($row['ticket_code']); ?></td>
<td><?php echo htmlspecialchars($row['status']); ?></td>
<td>
<a href="ticket_registration_edit.php?id=<?php echo $row['registration_id']; ?>">Edit Status</a> |
<a href="?type=registration&delete=<?php echo $row['registration_id']; ?>" onclick="return confirm('Delete registration?')">Delete</a>
</td>
</tr>
<?php } ?>
</tbody>
</table>
</section>

<!-- TICKETS TABLE -->
<section id="ticketsTable" class="table-section" style="display:none;">
<h2>Tickets <a href="?export=tickets" class="btn export">Export CSV</a></h2>
<input type="text" placeholder="Search..." onkeyup="searchTable('tSearch','ticketsTable')" id="tSearch">
<table>
<thead>
<tr><th>ID</th><th>Ticket Code</th><th>QR</th><th>Actions</th></tr>
</thead>
<tbody>
<?php foreach ($tickets as $row) { ?>
<tr>
<td><?php echo $row['ticket_id']; ?></td>
<td><?php echo htmlspecialchars($row['ticket_code']); ?></td>
<td><?php echo htmlspecialchars($row['qr_code']); ?></td>
<td>
<a href="?type=ticket&delete=<?php echo $row['ticket_id']; ?>" onclick="return confirm('Delete ticket?')">Delete</a>
</td>
</tr>
<?php } ?>
</tbody>
</table>
</section>

<!-- FEEDBACK TABLE -->
<section id="feedbackTable" class="table-section" style="display:none;">
<h2>Feedback <a href="?export=feedback" class="btn export">Export CSV</a></h2>
<input type="text" placeholder="Search..." onkeyup="searchTable('fSearch','feedbackTable')" id="fSearch">
<table>
<thead>
<tr><th>Name</th><th>Email</th><th>Message</th><th>Actions</th></tr>
</thead>
<tbody>
<?php foreach ($feedbacks as $row) { ?>
<tr>
<td><?php echo htmlspecialchars($row['name']); ?></td>
<td><?php echo htmlspecialchars($row['email']); ?></td>
<td><?php echo htmlspecialchars($row['message']); ?></td>
<td>
<a href="?type=feedback&delete=<?php echo $row['id']; ?>" onclick="return confirm('Delete feedback?')">Delete</a>
</td>
</tr>
<?php } ?>
</tbody>
</table>
</section>

</main>
<?php require 'utils/footer.php'; ?>
</body>
</html>