<?php
require 'db_connection.php'; // Ensure this file contains the DB connection setup
?>

<?php
// Start the session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user'])) {
    // Change the navigation to include the login and registration links
    $nav = '<li><a href="index.php">Home</a></li>';
    $nav .= '<li><a href="main_menu.php">Main Menu</a></li>';
    $nav .= '<li><a href="about.php">About</a></li>';
    $nav .= '<li><a href="login.php">Login</a></li>';
    $nav .= '<li><a href="registration.php">Register</a></li>';
} else {
    // Change the navigation to include the main menu and logout links
    $nav = '<li><a href="index.php">Home</a></li>';
    $nav .= '<li><a href="main_menu.php">Main Menu</a></li>';
    $nav .= '<li><a href="about.php">About</a></li>';
    $nav .= '<li><a href="logout.php">Logout</a></li>';
}
?>


<!DOCTYPE html>
<html lang="en" class="index">
<head>
    <title>Plant Biodiversity Portal | Home</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" type="text/css" href="css/style.css">
</head>

<body>
    <header>
        <h1>Plant Biodiversity Portal</h1>
        <nav>
            <ul>
                <?php echo $nav; ?>
            </ul>
    </header>
    
    <main>
        <section class="hero">
            <h2>Empowering Plant Biodiversity Exploration</h2>
            <p>Join us in understanding and conserving the rich diversity of plant life.</p>
        </section>

        <section class="callout">
            <h3>About Our Portal</h3>
            <p>Our portal is dedicated to the rich and diverse world of plants, offering a gateway to explore and understand global plant biodiversity. Over centuries, botanists, taxonomists, and naturalists have painstakingly collected herbarium specimens from all corners of the earth.</p>
            <p>Through this portal, users can delve into plant identification and gain insights into taxonomic studies. Whether you're a researcher, student, or plant enthusiast, this platform serves as a hub for learning about plant diversity, taxonomy, and the preservation of botanical heritage.</p>
        </section>

        <section class="gallery">
            <h2>Herbarium Specimen Photos</h2>
            <div class="specimen-gallery">
                <?php
                    // Folder containing the herbarium specimen images
                    $imageDir = 'images/herbarium_images/';  // Ensure you have a folder named 'images' with your specimen photos

                    // Get all image files from the folder
                    $images = glob($imageDir . "*.jpg");  // Adjust the file extension as needed

                    // Shuffle the images array to display them randomly
                    shuffle($images);

                    // Loop through and display up to 4 images
                    $count = 0;
                    foreach ($images as $image) {
                        if ($count < 4) {
                            echo '<img src="' . $image . '" alt="Herbarium Specimen" class="specimen-image">';
                            $count++;
                        } else {
                            break;
                        }
                    }
                ?>
            </div>
            <br>
        </section>

        <section class="main-menu">
            <h2>Explore More</h2>
            <div class="menu-button">
                <a href="main_menu.php" class="menu-btn">Go to Main Menu</a>
            </div>
        </section>
        <br><br><br>
    </main>
    
    <footer>
        <p>&copy; 2024 Plant Biodiversity Portal | <a href="profile.php">Isaac Ng Ming Hong</a></p>
    </footer>
</body>
</html>
