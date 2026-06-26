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
    <title>Concerts | UMUCO EVENTS</title>
    <link rel="stylesheet" href="../css/user_choice.css">
    <link rel="stylesheet" href="../css/style_header.css">
</head>
<body>

<div class="main-content">

    <!-- Concerts Section -->
    <div class="cards-container">
        <div class="event-card">
            <h2>Concerts</h2>
            <ul class="competition-list">
                <li>Gospel Music Festival</li>
                <li>Jazz & Blues Night</li>
                <li>Rwandan Traditional Music Concert</li>
                <li>Rock & Pop Showcase</li>
                <li>Electronic Dance Music Night</li>
                <li>And many more..</li>
            </ul>
        </div>

        <!-- UMUCO Music Festival -->
        <div class="event-card">
            <img src="https://i0.wp.com/radiotv10.rw/wp-content/uploads/2023/05/FwhAAntWIBsA6LO.jpeg?fit=1000%2C712&ssl=1"
                 alt="UMUCO Music Festival Crowd">
            <div class="card-content">
                <h2>Gospel Music Festival</h2>
                <p>Enjoy a vibrant showcase of local and international music performances.</p>
                <a href="../viewEvent.php" class="card-btn">Register</a>
            </div>
        </div>

        <!-- Jazz & Blues Night -->
        <div class="event-card">
            <img src="https://images.pexels.com/photos/210922/pexels-photo-210922.jpeg"
                 alt="Jazz & Blues Night Performance">
            <div class="card-content">
                <h2>Jazz & Blues Night</h2>
                <p>Relax and enjoy smooth jazz and blues with expressive live musicians.</p>
                <a href="../viewEvent.php" class="card-btn">Register</a>
            </div>
        </div>

        <!-- Traditional Music Concert -->
        <div class="event-card">
            <img src="https://www.ktpress.rw/wp-content/uploads/2016/09/TRAD.png"
                 alt="Traditional Music Concert Crowd">
            <div class="card-content">
                <h2>Rwandan Traditional Music Concert</h2>
                <p>Experience rich cultural heritage through traditional music and dance.</p>
                <a href="../viewEvent.php" class="card-btn">Register</a>
            </div>
        </div>

        <!-- Rock & Pop Showcase -->
        <div class="event-card">
            <img src="https://www.igihe.com/local/cache-vignettes/L600xH419/kiv12-acb7c.jpg?1771745150"
                 alt="Rock & Pop Band on Stage">
            <div class="card-content">
                <h2>Rock & Pop Showcase</h2>
                <p>Catch the hottest rock and pop bands live in energetic performances.</p>
                <a href="../viewEvent.php" class="card-btn">Register</a>
            </div>
        </div>

        <!-- Electronic Dance Music Night -->
        <div class="event-card">
            <img src="https://images.pexels.com/photos/167636/pexels-photo-167636.jpeg"
                 alt="Electronic Dance Music Night DJ">
            <div class="card-content">
                <h2>Electronic Dance Music Night</h2>
                <p>Dance the night away to electrifying beats from top DJs.</p>
                <a href="../viewEvent.php" class="card-btn">Register</a>
            </div>
        </div>

    </div>
</div>

<?php include '../utils/footer.php'; ?>

</body>
</html>