<?php
session_start();
if (!isset($_SESSION['email'])) {
    header('Location: login.php');
    exit;
}

?>

<!DOCTYPE html>
<html lang="en" class="profile">
<head>
    <title>Plant Biodiversity Portal | Profile</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" type="text/css" href="css/style.css">
</head>

<body>
    <main>
        <section class="profile-content">
            <img src="images/student_profile.jpg" alt="Profile Image">
            <h1>Isaac Ng Ming Hong</h1>
            <p>Student ID: 102779797</p>
            <p>Email: 102779797@students.swinburne.edu.my</p>
            <h2>Statement</h2>
            <p>
                I declare that this assignment is my individual work. I have not
                worked collaboratively nor have I copied from any other student's work or from any
                other source. I have not engaged another party to complete this assignment. I
                am aware of the University's policy with regards to plagiarism. I have not
                allowed, and will not allow, anyone to copy my work with the intention of passing
                it off as his or her own work.
            </p>
        </section>

        <section class="links">
            <h2>Links</h2>
            <ul class="button-list">
                <li><a href="index.php" class="button">Back to homepage</a></li>
                <li><a href="about.php" class="button">About Page</a></li>
            </ul>
        </section>
    </main>
    
    
</body>
</html>
