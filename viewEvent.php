<?php
session_start();

// Only allow logged-in users
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Include required files
include_once 'classes/connect.php';
include_once 'classes/events.php';
include_once 'classes/Registration.php';
include_once 'classes/Ticket.php';

// Instantiate objects
$eventObj = new Events($conn);
$registrationObj = new Registration($conn);
$ticketObj = new Ticket($conn);

// Fetch user tickets
$userTickets = $registrationObj->getUserTicketsAndStatus($_SESSION['user_id']);

// Safe username
$userDisplayName = htmlspecialchars($_SESSION['username'] ?? 'User');

$error = "";
$success = "";
$qr_image = "";

// Handle form submission
if (isset($_POST['add_event'])) {

    if (empty($_POST['event_title']) || empty($_POST['category']) || empty($_POST['event_date'])) {
        $error = "Please fill in all required fields.";
    } else {

        $event_id = $eventObj->addEvent($_POST);

        if ($event_id) {

            $ticket_code = $registrationObj->registerUser($_SESSION['user_id'], $event_id);

            if ($ticket_code) {

                $registration = $registrationObj->getRegistrationByTicket($ticket_code);

                if ($registration) {

                    $qr_code = $ticketObj->issueTicket($registration['registration_id']);

                    if ($qr_code) {

                        $success = "Event added successfully!<br>
                                    Ticket Code: <strong>" . htmlspecialchars($ticket_code) . "</strong>";

                        $qr_image = "tickets_qr/" . $qr_code . ".png";

                        if (!is_dir('tickets_qr')) {
                            mkdir('tickets_qr', 0755, true);
                        }

                        if (!file_exists($qr_image)) {
                            require_once 'phpqrcode/qrlib.php';
                            QRcode::png($qr_code, $qr_image, QR_ECLEVEL_H, 6);
                        }

                    } else {
                        $error = "Ticket generation failed: " . $ticketObj->error;
                    }

                } else {
                    $error = "Registration not found.";
                }

            } else {
                $error = "Registration failed: " . $registrationObj->error;
            }

        } else {
            $error = "Event creation failed: " . $eventObj->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Event | UMUCO EVENTS</title>
    <link rel="stylesheet" href="css/style_events.css">
    <script src="js/validate.js"></script>
</head>
<body>

<?php require 'utils/header.php'; ?>

<div class="main-content">

    <h2>Welcome <?php echo $userDisplayName; ?> 👋</h2>

    <!-- 🎟️ TICKETS SECTION -->
    <div class="open-tickets">
        <h3>Your Tickets</h3>

        <?php if (!empty($userTickets)): ?>
            <?php foreach ($userTickets as $ticket): ?>

                <?php 
                    // Dynamic status class (for styling later)
                    $statusClass = strtolower($ticket['status']);
                ?>

                <div class="ticket-box <?php echo $statusClass; ?>">
                    <p><strong>Event ID:</strong> <?php echo htmlspecialchars($ticket['event_id']); ?></p>
                    <p><strong>Ticket Code:</strong> <?php echo htmlspecialchars($ticket['ticket_code']); ?></p>
                    <p><strong>Status:</strong> <?php echo htmlspecialchars($ticket['status']); ?></p>
                </div>

            <?php endforeach; ?>
        <?php else: ?>
            <p>No tickets found.</p>
        <?php endif; ?>
    </div>

    <!-- Messages -->
    <?php if ($error): ?>
        <p class="error-msg"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>

    <?php if ($success): ?>
        <p class="success-msg"><?php echo $success; ?></p>

        <?php if ($qr_image && file_exists($qr_image)): ?>
            <div class="qr-code">
                <img src="<?php echo htmlspecialchars($qr_image); ?>" alt="QR Code">
                <p>Scan this QR code for your ticket.</p>
            </div>
        <?php endif; ?>
    <?php endif; ?>

    <!-- 📝 ADD EVENT FORM -->
    <h2>Add a New Event</h2>

    <form method="POST" onsubmit="return validateEventForm()">

        <label>Event Title:</label>
        <input type="text" name="event_title" required>

        <label>Category:</label>
        <select name="category" required>
            <option value="">--Select--</option>
            <option value="Conference">Conference</option>
            <option value="Workshop">Workshop</option>
            <option value="Social">Social</option>
            <option value="Competition">Competition</option>
            <option value="Concert">Concert</option>
            <option value="Others">Others</option>
        </select>

        <label>Description:</label>
        <textarea name="description" rows="4" required></textarea>

        <label>Event Date:</label>
        <input type="date" name="event_date" required>

        <label>Event Time:</label>
        <input type="time" name="event_time" required>

        <label>Venue:</label>
        <input type="text" name="venue" required>

        <label>Maximum Capacity:</label>
        <input type="number" name="max_capacity" min="1" value="100" required>

        <button type="submit" name="add_event">Add Event</button>
    </form>

</div>

<?php require 'utils/footer.php'; ?>

</body>
</html>