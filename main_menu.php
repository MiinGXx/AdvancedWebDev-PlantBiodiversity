<?php
session_start();
if (!isset($_SESSION['email']) || $_SESSION['type'] != 'user') {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en" class="menu">
<head>
    <title>Plant Biodiversity Portal | Main Menu</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" type="text/css" href="style/style.css">
</head>

<body>          
    <header>
        <h1>Plant Biodiversity Portal</h1>
        <nav>
            <ul>            
                <li><a href="index.php">Home</a></li>       
                <li><a href="main_menu.php">Main Menu</a></li>                   
                <li><a href="about.php">About</a></li>
                <li><a href="logout.php">Logout</a></li> <!-- Consider creating a separate logout page -->
                <li><a href="update_profile.php">View Profile</a></li>
            </ul>
        </nav>    
    </header>

    <main>
        <section class="hero">
            <h2>Main Menu</h2>
            <p>Welcome to the Plant Biodiversity Portal. Explore the sections below to learn more about plant classification, identification, and preservation.</p>
        </section>
        
        <section>
            <h2>Select a Section</h2>
            <div class="menu-options">
                <div class="card">
                    <h3>Plants Classification</h3>
                    <p>Learn about Plant family, Genus, and Species.</p>
                    <a class="menu-btn" href="classify.php">Go to Plants Classification</a>
                </div>
                <div class="card">
                    <h3>Tutorial</h3>
                    <p>Learn how to transfer a fresh leaf into herbarium specimens, tools to use, and how to preserve the herbarium.</p>
                    <a class="menu-btn" href="tutorial.php">Go to Tutorial</a>
                </div>
                <div class="card">
                    <h3>Identify</h3>
                    <p>Identify the plant type based on the photo uploaded. Get the scientific plant name, common name, and photos of herbarium specimens. Download description in PDF.</p>
                    <a class="menu-btn" href="identify.php">Go to Identify</a>
                </div>
                <div class="card">
                    <h3>Contribution</h3>
                    <p>Contribute data to the dataset by uploading photos of fresh leaves and herbarium specimens with information. Data will be stored in the database and used for the Identify page.</p>
                    <a class="menu-btn" href="contribute.php">Go to Contribution</a>
                </div>
            </div>
        </section>
    </main>

    <footer>
        <p>&copy; 2024 Plant Biodiversity Portal | <a href="profile.php">Isaac Ng Ming Hong</a></p>
    </footer>
</body>
</html>
