<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Campus Event FCRIT</title>
    <link rel="stylesheet" href="css/style_header.css">
</head>

<body>

<header class="main-header">

    <div class="header-container">

        <div class="logo">
            <h2>UMUCO EVENTS</h2>
        </div>

        <nav class="nav-menu">
            <ul>
                <li>
                    <a class="Register-btn" href="logout.php">Log Out</a>
                </li>
                <li>Logged in as: <strong><?php echo $_SESSION['email']; ?></strong></li>

            </ul>
        </nav>

    </div>

</header>

</body>
</html>