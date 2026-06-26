<?php
session_start();

// Redirect to login page if user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Include database connection
include_once 'classes/connect.php';
include_once 'classes/events.php';


// Safe display name
$userDisplayName = htmlspecialchars($_SESSION['username']) ?? 'user';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Campus Event</title>
    <link rel="stylesheet" href="css/style_index.css">
</head>
<body>

<?php require 'utils/header.php'; ?>

<div class="main-content">
    <div class="page-title">
        <h1>Welcome <?php echo $userDisplayName; ?>! Explore Our Diverse Event Services</h1>
    </div>

    <hr class="divider">
    <div class="event-section">
        <div class="event-container">
            <div class="event-image">
                <img src="images/conference_1.jpg" alt="Conference">
            </div>
            <div class="event-details">
                <h2>Conferences</h2>
                <p>Enhance your technical knowledge by participating in our professional conferences and academic events.</p>
                <a class="event-btn" href="user page/conferences.php">View Technical Events</a>
            </div>
        </div>
    </div>

    <hr class="divider">
    <div class="event-section">
        <div class="event-container">
            <div class="event-image">
                <img src="images/workshop_1.jpg" alt="Workshop">
            </div>
            <div class="event-details">
                <h2>Workshops & Training</h2>
                <p>Develop practical skills through our interactive workshops and training sessions.</p>
                <a class="event-btn" href="user page/workshop.php">View Workshops</a>
            </div>
        </div>
    </div>

    <hr class="divider">
    <div class="event-section">
        <div class="event-container">
            <div class="event-image">
                <img src="images/social_event_parties.jpg" alt="Social Event">
            </div>
            <div class="event-details">
                <h2>Social Events</h2>
                <p>Celebrate memorable moments through social gatherings, parties, weddings, and campus celebrations.</p>
                <a class="event-btn" href="user page/social.php">View Social Events</a>
            </div>
        </div>
    </div>

    <hr class="divider">
    <div class="event-section">
        <div class="event-container">
            <div class="event-image">
                <img src="images/competitions_volleyball.jpg" alt="Competition">
            </div>
            <div class="event-details">
                <h2>Competitions & Contests</h2>
                <p>Challenge yourself by participating in exciting competitions and contests.</p>
                <a class="event-btn" href="user page/competition.php">View Competitions</a>
            </div>
        </div>
    </div>

    <hr class="divider">
    <div class="event-section">
        <div class="event-container">
            <div class="event-image">
                <img src="images/concerts_rwanda_culture.jpg" alt="Concert">
            </div>
            <div class="event-details">
                <h2>Concerts & Entertainment</h2>
                <p>Experience music, culture and entertainment through our concerts and live shows.</p>
                <a class="event-btn" href="user page/concerts.php">View Entertainment Events</a>
            </div>
        </div>
    </div>
</div>

<?php require 'utils/footer.php'; ?>

</body>
</html>