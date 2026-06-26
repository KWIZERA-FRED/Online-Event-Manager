<?php
session_start();

// Only allow logged-in users
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Include header
include '../utils/userChoiceHeader.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Workshops | UMUCO EVENTS</title>
    <link rel="stylesheet" href="../css/user_choice.css">
    <link rel="stylesheet" href="../css/style_header.css">
</head>
<body>

<div class="main-content">

    <!-- Workshops Section -->
    <div class="cards-container">
        <div class="event-card">
            <h2>Workshops</h2>
            <ul class="competition-list">
                <li>Web Development Bootcamp</li>
                <li>Creative Writing Workshop</li>
                <li>Photography Masterclass</li>
                <li>Public Speaking Skills</li>
                <li>Entrepreneurship & Startup Workshop</li>
                <li>Design Thinking & Innovation</li>
                <li>More workshops coming soon...</li>
            </ul>
        </div>

        <!-- Web Development Bootcamp -->
        <div class="event-card">
            <img src="https://images.pexels.com/photos/3183150/pexels-photo-3183150.jpeg"
                 alt="People collaborating in a workshop setting">
            <div class="card-content">
                <h2>Web Development Bootcamp</h2>
                <p>Hands‑on coding skills and real project experience to build your first website app.</p>
                <a href="../viewEvent.php" class="card-btn">Register</a>
            </div>
        </div>

        <!-- Creative Writing Workshop -->
        <div class="event-card">
            <img src="https://images.pexels.com/photos/261909/pexels-photo-261909.jpeg"
                 alt="Creative writing session with participants">
            <div class="card-content">
                <h2>Creative Writing Workshop</h2>
                <p>Enhance your writing craft through prompts, feedback, and creativity exercises.</p>
                <a href="../viewEvent.php" class="card-btn">Register</a>
            </div>
        </div>

        <!-- Photography Masterclass -->
        <div class="event-card">
            <img src="https://images.pexels.com/photos/414612/pexels-photo-414612.jpeg"
                 alt="Photography workshop teaching camera skills">
            <div class="card-content">
                <h2>Photography Masterclass</h2>
                <p>Master camera techniques and creative composition with expert photographers.</p>
                <a href="../viewEvent.php" class="card-btn">Register</a>
            </div>
        </div>

        <!-- Public Speaking Skills -->
        <div class="event-card">
            <img src="https://images.pexels.com/photos/3184325/pexels-photo-3184325.jpeg"
                 alt="Public speaking workshop with audience">
            <div class="card-content">
                <h2>Public Speaking Skills</h2>
                <p>Boost confidence and communication skills with practical speaking exercises.</p>
                <a href="../viewEvent.php" class="card-btn">Register</a>
            </div>
        </div>

        <!-- Entrepreneurship Workshop -->
        <div class="event-card">
            <img src="https://images.unsplash.com/photo-1521737604893-d14cc237f11d"
                 alt="Business workshop brainstorming session">
            <div class="card-content">
                <h2>Entrepreneurship & Startup Workshop</h2>
                <p>Learn business fundamentals and how to pitch your idea to investors.</p>
                <a href="../viewEvent.php" class="card-btn">Register</a>
            </div>
        </div>

        <!-- Design Thinking & Innovation -->
        <div class="event-card">
            <img src="https://images.pexels.com/photos/3184298/pexels-photo-3184298.jpeg"
                 alt="Design thinking and collaboration session">
            <div class="card-content">
                <h2>Design Thinking & Innovation</h2>
                <p>Engage in creative problem solving and innovative workshop activities.</p>
                <a href="../viewEvent.php" class="card-btn">Register</a>
            </div>
        </div>

    </div>
</div>

<?php include '../utils/footer.php'; ?>

</body>
</html>