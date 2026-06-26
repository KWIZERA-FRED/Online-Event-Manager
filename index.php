<?php
session_start(); // Check if user is logged in
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>UMUCO EVENTS | Landing Page</title>
<link rel="stylesheet" href="css/style_user_index.css">
</head>
<body>

<?php require 'utils/indexHeader.php'; ?>
<div class="main-container">
<div class="hero-section">
    <div class="hero-text">
        <h1>Welcome to <span style="color: #27335c;">UMUCO EVENTS</span></h1>
        <p>Discover, explore, and participate in unforgettable events – from conferences and workshops to social gatherings, competitions, and live concerts.</p>
        <?php if(isset($_SESSION['user_id'])): ?>
            <a href="viewEvent.php" class="hero-btn">Explore Events</a>
        <?php else: ?>
            <a href="register.php" class="hero-btn">Register to Explore Events</a>
        <?php endif; ?>
    </div>
</div>

<div class="cards-container">

    <div class="event-card">
        <img src="https://images.unsplash.com/photo-1582192903020-8a5e59dcdcf2?q=80&w=870&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D" alt="Conference">
        <div class="card-content">
            <h2>Conferences</h2>
            <p>Engage with thought leaders, expand your knowledge, and network with professionals at our carefully curated conferences. Every conference is an opportunity to grow, learn, and connect.</p>
            <?php if(isset($_SESSION['user_id'])): ?>
                <a href="viewEvent.php?id=1" class="card-btn">View Events</a>
            <?php else: ?>
                <a href="register.php" class="card-btn">Register to Participate</a>
            <?php endif; ?>
        </div>
    </div>

    <div class="event-card">
        <img src="https://images.unsplash.com/photo-1560831340-b9679dc9e9f0?q=80&w=774&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D" alt="Workshop">
        <div class="card-content">
            <h2>Workshops & Training</h2>
            <p>Hands-on workshops and immersive training sessions designed to equip you with the skills and expertise you need to excel. Unlock practical knowledge, one session at a time.</p>
            <?php if(isset($_SESSION['user_id'])): ?>
                <a href="viewEvent.php?id=2" class="card-btn">View Events</a>
            <?php else: ?>
                <a href="register.php" class="card-btn">Register to Participate</a>
            <?php endif; ?>
        </div>
    </div>

    <div class="event-card">
        <img src="https://images.unsplash.com/photo-1543148981-18199087786c?q=80&w=870&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D" alt="Social Event">
        <div class="card-content">
            <h2>Social Events</h2>
            <p>From lively campus gatherings to themed parties and cultural celebrations, our social events are crafted for enjoyment, networking, and creating memories that last a lifetime.</p>
            <?php if(isset($_SESSION['user_id'])): ?>
                <a href="viewEvent.php?id=3" class="card-btn">View Events</a>
            <?php else: ?>
                <a href="register.php" class="card-btn">Register to Participate</a>
            <?php endif; ?>
        </div>
    </div>

    <div class="event-card">
        <img src="https://images.unsplash.com/flagged/photo-1550413231-202a9d53a331?q=80&w=870&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D" alt="Competition">
        <div class="card-content">
            <h2>Competitions & Contests</h2>
            <p>Test your skills, challenge your limits, and compete with the best! Our competitions and contests provide excitement, recognition, and the thrill of victory.</p>
            <?php if(isset($_SESSION['user_id'])): ?>
                <a href="viewEvent.php?id=4" class="card-btn">View Events</a>
            <?php else: ?>
                <a href="register.php" class="card-btn">Register to Participate</a>
            <?php endif; ?>
        </div>
    </div>

    <div class="event-card">
        <img src="https://images.unsplash.com/photo-1514525253161-7a46d19cd819?q=80&w=774&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D" alt="Concert">
        <div class="card-content">
            <h2>Concerts & Entertainment</h2>
            <p>Immerse yourself in live music, cultural performances, and spectacular entertainment. Our concerts offer unforgettable experiences that resonate long after the final note.</p>
            <?php if(isset($_SESSION['user_id'])): ?>
                <a href="viewEvent.php?id=5" class="card-btn">View Events</a>
            <?php else: ?>
                <a href="register.php" class="card-btn">Register to Participate</a>
            <?php endif; ?>
        </div>
    </div>

</div>
</div>
<?php require 'utils/footer.php'; ?>
</body>
</html>