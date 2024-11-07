<?php
require 'db_connection.php'; // Ensure this file contains the DB connection setup

// Create database if it doesn't exist
$conn->query("CREATE DATABASE IF NOT EXISTS $dbname");
$conn->select_db($dbname);

// Create tables
$conn->query("CREATE TABLE IF NOT EXISTS user_table (
    email VARCHAR(50) NOT NULL PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    dob DATE NULL,
    gender VARCHAR(6) NOT NULL,
    contact_number VARCHAR(15) NULL,
    hometown VARCHAR(50) NOT NULL,
    profile_image VARCHAR(100) NULL
)");

$conn->query("CREATE TABLE IF NOT EXISTS account_table (
    email VARCHAR(50) NOT NULL,
    password VARCHAR(255) NOT NULL,
    type VARCHAR(5) NOT NULL,
    FOREIGN KEY (email) REFERENCES user_table(email) ON DELETE CASCADE ON UPDATE CASCADE
)");

$conn->query("CREATE TABLE IF NOT EXISTS plant_table (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    Scientific_Name VARCHAR(50) NOT NULL,
    Common_Name VARCHAR(50) NOT NULL,
    family VARCHAR(100) NOT NULL,
    genus VARCHAR(100) NOT NULL,
    species VARCHAR(100) NOT NULL,
    plants_image VARCHAR(100) NULL,
    description VARCHAR(100) NULL
)");

// Check if the admin user already exists
$result = $conn->query("SELECT email FROM user_table WHERE email = 'admin@swin.edu.my'");
if ($result->num_rows == 0) {
    // Insert dummy data if it doesn't exist
    $conn->query("INSERT INTO user_table (email, first_name, last_name, gender, hometown) VALUES
        ('admin@swin.edu.my', 'Admin', 'User', 'Male', 'City')");
    $conn->query("INSERT INTO account_table (email, password, type) VALUES
        ('admin@swin.edu.my', '" . password_hash('admin', PASSWORD_BCRYPT) . "', 'admin')");
}
$conn->close();
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
    <link rel="stylesheet" type="text/css" href="style/style.css">
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
