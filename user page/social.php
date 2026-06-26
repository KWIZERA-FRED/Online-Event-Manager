<?php
session_start();

// Only allow logged‑in users
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
    <title>Social Events | UMUCO EVENTS</title>
    <link rel="stylesheet" href="../css/user_choice.css">
    <link rel="stylesheet" href="../css/style_header.css">
</head>
<body>

<div class="main-content">

    <!-- Social Events Section -->
    <div class="cards-container">
        <div class="event-card">
            <h2>Social Events</h2>
            <ul class="competition-list">
                <li>Networking Mixer</li>
                <li>Community Picnic</li>
                <li>Holiday Party Celebration</li>
                <li>Movie Night Under the Stars</li>
                <li>Cultural Dance & Festivities</li>
                <li>Friends & Meet‑Ups</li>
                <li>More fun social events…</li>
            </ul>
        </div>

        <!-- Networking Mixer -->
        <div class="event-card">
            <img src="https://images.pexels.com/photos/3184298/pexels-photo-3184298.jpeg"
                 alt="Networking Mixer Social Event">
            <div class="card-content">
                <h2>Networking Mixer</h2>
                <p>Connect with new people, build relationships, and enjoy great conversations.</p>
                <a href="../viewEvent.php" class="card-btn">Register</a>
            </div>
        </div>

        <!-- Community Picnic -->
        <div class="event-card">
            <img src="https://images.pexels.com/photos/2619967/pexels-photo-2619967.jpeg"
                 alt="Community Picnic Outdoors">
            <div class="card-content">
                <h2>Community Picnic</h2>
                <p>Relax outdoors, enjoy delicious bites, and meet friendly faces in your community.</p>
                <a href="../viewEvent.php" class="card-btn">Register</a>
            </div>
        </div>

        <!-- Holiday Party Celebration -->
        <div class="event-card">
            <img src="https://images.pexels.com/photos/3182765/pexels-photo-3182765.jpeg"
                 alt="Holiday Party Celebration">
            <div class="card-content">
                <h2>Holiday Party Celebration</h2>
                <p>Join us for festive music, food, and holiday cheer with all your friends.</p>
                <a href="../viewEvent.php" class="card-btn">Register</a>
            </div>
        </div>

        <!-- Movie Night Under the Stars -->
        <div class="event-card">
            <img src="https://miro.medium.com/v2/resize:fit:1100/format:webp/1*w-Lj3d7V5Eyg8KExan8WVw.jpeg"
                 alt="Outdoor Movie Night Event">
            <div class="card-content">
                <h2>Movie Night Under the Stars</h2>
                <p>Bring your blankets and popcorn for a cozy outdoor movie experience.</p>
                <a href="../viewEvent.php" class="card-btn">Register</a>
            </div>
        </div>

        <!-- Cultural Dance & Festivities -->
        <div class="event-card">
            <img src="https://images.pexels.com/photos/694587/pexels-photo-694587.jpeg"
                 alt="Cultural Dance & Festivities">
            <div class="card-content">
                <h2>Cultural Dance & Festivities</h2>
                <p>Experience music, dance, and traditions from diverse cultures.</p>
                <a href="../viewEvent.php" class="card-btn">Register</a>
            </div>
        </div>

        <!-- Friends & Meet‑Ups -->
        <div class="event-card">
            <img src="https://images.pexels.com/photos/1055691/pexels-photo-1055691.jpeg"
                 alt="Friends & Meet‑Ups Gathering">
            <div class="card-content">
                <h2>Friends & Meet‑Ups</h2>
                <p>Casual gatherings to hang out, play games, or chat with new and old friends.</p>
                <a href="../viewEvent.php" class="card-btn">Register</a>
            </div>
        </div>

    </div>
</div>

<?php include '../utils/footer.php'; ?>

</body>
</html>