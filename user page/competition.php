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
    <title>Competition | UMUCO EVENTS</title>
    <link rel="stylesheet" href="../css/user_choice.css">
    <link rel="stylesheet" href="../css/style_header.css">
</head>
<body>

<div class="main-content">
    

   <div class="cards-container">
        <div class="event-card">
            <h2>Competitions We Offer</h2>
            <ul class="competition-list">
                <li>AI Coding Challenge</li>
                <li>Hackathon Rwanda</li>
                <li>Startup Pitch Competition</li>
                <li>Robotics Challenge</li>
                <li>Math Olympiad</li>
                <li>Science Fair</li>
                <li>Debate Tournament</li>
            </ul>
        </div>
    
    <div class="event-card">
        <img src="https://cdn.mos.cms.futurecdn.net/s7iY4nThq9bzqHZdbdhby3-970-80.webp" alt="AI Coding Challenge">
        <div class="card-content">
            <h2>AI Coding Challenge</h2>
            <p>Test your programming and AI skills in this exciting coding competition.</p>
            <a href="../viewEvent.php" class="card-btn">Register</a>
        </div>
    </div>

    <div class="event-card">
        <img src="https://cdn.prod.website-files.com/65841d7defe94c74579f242b/67e16cdbcf0f6986905a177b_resource-news-irembo-hackathon-ws-p-1080.webp" alt="Hackathon Rwanda">
        <div class="card-content">
            <h2>Hackathon Rwanda</h2>
            <p>Collaborate and innovate in a fast-paced hackathon with tech enthusiasts.</p>
            <a href="../viewEvent.php" class="card-btn">Register</a>
        </div>
    </div>

    <div class="event-card">
        <img src="https://www.ktpress.rw/wp-content/uploads/2024/11/53384531809_5008ab6671_k-1000x720.jpg" alt="Startup Pitch Competition">
        <div class="card-content">
            <h2>Startup Pitch Competition</h2>
            <p>Pitch your business idea to investors and get feedback from industry experts.</p>
            <a href="../viewEvent.php" class="card-btn">Register</a>
        </div>
    </div>

    <div class="event-card">
        <img src="https://img.curiositystream.com/cHJvZHVjdGlvbi9zb3VyY2VzL3RpdGxlcy8yMDE1Ni9ob3Jpem9udGFsX2ltYWdlXzE2XzkvZW4tVVMvYTRkZTg4YzYtZjk4ZS00YjI1LWJkMDUtOWFmMTMwMTY3YWJkL3Nlcmllc19ob3Jpem9udGFsX2ltYWdlXzE2XzlfMTU2XzM4NDB4MjE2MF8wMDAxLmpwZw==" alt="Robotics Challenge">
        <div class="card-content">
            <h2>Robotics Challenge</h2>
            <p>Showcase your robotics projects and compete for top honors in engineering.</p>
            <a href="../viewEvent.php" class="card-btn">Register</a>
        </div>
    </div>

    <div class="event-card">
        <img src="https://www.unicusolympiads.com/assets/images/blog/math-olympiad.jpg" alt="Math Olympiad">
        <div class="card-content">
            <h2>Math Olympiad</h2>
            <p>Challenge your analytical and problem-solving skills in this math competition.</p>
            <a href="../viewEvent.php" class="card-btn">Register</a>
        </div>
    </div>

</div>
</div>

<?php include '../utils/footer.php'; ?>

</body>
</html>