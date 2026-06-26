<?php
session_start();
include_once 'classes/connect.php'; // Provides $conn
include_once 'classes/Feedback.php';

// Instantiate Feedback class
$feedbackObj = new Feedback($conn);

$error = "";
$success = "";

// Handle form submission
if (isset($_POST['submit_feedback'])) {
    if ($feedbackObj->addFeedback($_POST)) {
        $success = $feedbackObj->success;
    } else {
        $error = $feedbackObj->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us | UMUCO EVENTS</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style_contact.css">
    <link rel="stylesheet" href="../css/style_header.css">
</head>

<body>

<?php require 'utils/indexHeader.php'; ?>

<main class="contact-section">
    <div class="contact-title">
        <h1>Contact Us</h1>
        <p>We would love to hear from you. Reach out for any event inquiries.</p>
    </div>

    <div class="contact-container">
        <aside class="contact-info">
            <h2>Get In Touch</h2>

            <div class="info-box">
                <h3>Email</h3>
                <p>UmucoEvents@gmail.com</p>
            </div>

            <div class="info-box">
                <h3>Phone</h3>
                <p>+250 780 000 000</p>
            </div>

            <div class="info-box">
                <h3>Address</h3>
                <p>UMUCO event<br>UNILAK Campus</p>
            </div>
        </aside>

        <section class="contact-form">
            <h2>Send a Message</h2>

            <!-- Feedback Message Div -->
            <div class="feedback-message">
                <?php if ($error): ?>
                    <p class="error-msg"><?php echo htmlspecialchars($error); ?></p>
                <?php endif; ?>

                <?php if ($success): ?>
                    <p class="success-msg"><?php echo htmlspecialchars($success); ?></p>
                <?php endif; ?>
            </div>

            <form action="" method="POST">
                <input type="text" name="name" placeholder="Your Name" required>
                <input type="email" name="email" placeholder="Your Email" required>
                <input type="text" name="subject" placeholder="Subject">
                <textarea name="message" placeholder="Your Message" required></textarea>
                
                <button type="submit" name="submit_feedback" class="btn-submit">Send Message</button>
            </form>
        </section>
    </div>
</main>

<?php require 'utils/footer.php'; ?>

</body>
</html>