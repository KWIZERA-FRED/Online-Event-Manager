<?php
session_start();
include_once 'classes/connect.php';
include_once 'classes/events.php';
include_once 'classes/Ticket.php';
include_once 'classes/Registration.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$event_id = intval($_POST['event_id']); // sent from event page
$registrationObj = new Registration($conn);
$ticketObj = new Ticket($conn);

// Check if user already registered
$existing = $registrationObj->getUserRegistrations($user_id);
foreach ($existing as $reg) {
    if ($reg['event_id'] == $event_id) {
        die("You are already registered for this event.");
    }
}

// Register the user and get ticket code
$ticket_code = $registrationObj->registerUser($user_id, $event_id, 'approved');

if ($ticket_code) {
    // Fetch the registration_id
    $reg = $registrationObj->getRegistrationByTicket($ticket_code);
    $registration_id = $reg['registration_id'];

    // Issue QR ticket
    $qr_code = $ticketObj->issueTicket($registration_id);

    echo "Registration successful! Your QR ticket code is: <strong>$qr_code</strong>";
} else {
    echo "Registration failed: " . $registrationObj->error;
}
?>