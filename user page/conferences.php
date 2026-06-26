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
    <title>Conferences | UMUCO EVENTS</title>
    <link rel="stylesheet" href="../css/user_choice.css">
    <link rel="stylesheet" href="../css/style_header.css">
</head>
<body>

<div class="main-content">

    <div class="cards-container">
        <div class="event-card">
            <h2>Conferences</h2>
            <ul class="competition-list">
                <li>Tech Innovation Summit</li>
                <li>Healthcare & Wellness Forum</li>
                <li>Entrepreneurship & Startups</li>
                <li>Education Technology Conference</li>
                <li>Environmental Sustainability Summit</li>
                <li>Science innovation conferences</li>
                <li>And many more..</li>
            </ul>
        </div>

        <div class="event-card">
            <img src="https://bantugazette.com/wp-content/uploads/2025/04/Global-AI-Summit-Kigali-Rwanda-Bantu-Gazette-scaled-1.jpeg" alt="Tech Innovation Summit">
            <div class="card-content">
                <h2>Tech Innovation Summit</h2>
                <p>Explore the latest innovations in technology with industry leaders.</p>
                <a href="../viewEvent.php" class="card-btn" >Register</a>
            </div>
        </div>

        <div class="event-card">
            <img src="https://www.ktpress.rw/wp-content/uploads/2023/11/National-Health-Financing-Dialogue-organized-by-the-Rwanda-NGO-Forum-1024x616.jpg" alt="Healthcare & Wellness Forum">
            <div class="card-content">
                <h2>Healthcare & Wellness Forum</h2>
                <p>Discuss breakthroughs in healthcare, wellness, and medical technology.</p>
                <a href="../viewEvent.php" class="card-btn">Register</a>
            </div>
        </div>

        <div class="event-card">
            <img src="https://www.ktpress.rw/wp-content/uploads/2025/08/GRLEy0YWIAA_8FB-1024x606.jpeg" alt="Entrepreneurship & Startups">
            <div class="card-content">
                <h2>Entrepreneurship & Startups</h2>
                <p>Learn how to start and scale a business from successful entrepreneurs.</p>
                <a href="../viewEvent.php" class="card-btn">Register</a>
            </div>
        </div>

        <div class="event-card">
            <img src="https://img2.chinadaily.com.cn/images/202510/23/68f97cf0a310f735017f4cd5.png" alt="Education Technology Conference">
            <div class="card-content">
                <h2>Education Technology Conference</h2>
                <p>Discover the latest in EdTech tools and online learning platforms.</p>
                <a href="../viewEvent.php" class="card-btn">Register</a>
            </div>
        </div>

        <div class="event-card">
            <img src="https://uok.ac.rw/wp-content/uploads/2025/06/IMG_5591-1024x768.jpg" alt="Environmental Sustainability Summit">
            <div class="card-content">
                <h2>Environmental Sustainability Summit</h2>
                <p>Discuss green solutions, climate action, and sustainable development goals.</p>
                <a href="../viewEvent.php" class="card-btn">Register</a>
            </div>
        </div>

    </div>
</div>

<?php include '../utils/footer.php'; ?>

</body>
</html>