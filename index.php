<!DOCTYPE html>
<html lang="en" class="index">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Plant Biodiversity Portal</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>

<body>
    <header>
        <h1>Plant Biodiversity Portal</h1>
        <nav>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="main-menu.php">Main Menu</a></li>
                <li><a href="login.php">Login</a></li>
                <li><a href="register.php">Register</a></li>
            </ul>
        </nav>
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
            <h3>Random Herbarium Specimen Photos</h3>
            <div class="specimen-gallery">
                <?php
                    // Folder containing the herbarium specimen images
                    $imageDir = 'images/';  // Ensure you have a folder named 'images' with your specimen photos

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
        <p>&copy; 2024 Plant Biodiversity Portal</p>
    </footer>
</body>
</html>
