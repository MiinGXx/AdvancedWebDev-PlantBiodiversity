<!DOCTYPE html>
<html lang="en">
<head>
    <title>Plant Biodiversity Portal</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>

<body>          
    <h1>Plant Biodiversity Portal</h1>
    <nav>
        <ul>            
            <li><a href="index.php">Home</a></li>                       
            <li><a href="about.php">About</a></li>
            <li><a href="login.php">Login</a></li>
            <li><a href="register.php">Register</a></li>
        </ul>
    </nav>    
    <main>
        <h2>Welcome to Plant Biodiversity</h2>
        
        <!-- Callout Container for Introduction -->
        <div class="callout">
        <p>Our portal is dedicated to the rich and diverse world of plants, offering a gateway to explore and understand global plant biodiversity. Over centuries, botanists, taxonomists, and naturalists have painstakingly collected herbarium specimens from all corners of the earth. These specimens, preserved and cataloged with essential details like scientific names, origins, and collection dates, provide an invaluable resource for studying the intricate relationships between plant species.</p>
            
        <p>Through this portal, users can delve into the world of plant identification, gaining insights into how taxonomists compare new specimens to historical collections in order to confirm species identities. Whether you’re a researcher, student, or plant enthusiast, this platform serves as a hub for learning about plant diversity, taxonomy, and the preservation of botanical heritage.</p>

        <p>Join us in exploring the incredible variety of plant life and the vital role that herbarium collections play in the scientific study and conservation of plant species worldwide.</p>

        </div>

        <br><br><br>

        <!-- Herbarium Specimen Photos Section -->
        <h3>Random Herbarium Specimen Photos</h3>
        <div class="specimen-gallery">
            <?php
                // Folder containing the herbarium specimen images
                $imageDir = 'images/';  // Make sure you have a folder named 'images' with your specimen photos

                // Get all image files from the folder
                $images = glob($imageDir . "*.jpg");  // You can adjust the file extension

                // Shuffle the images array to display them in random order
                shuffle($images);

                // Loop through and display up to 4 images
                $count = 0;
                foreach ($images as $image) {
                    if ($count < 4) {
                        echo '<img src="' . $image . '" alt="Herbarium Specimen">';
                        $count++;
                    } else {
                        break;
                    }
                }
            ?>
        </div>

        <br><br>

        <h2>Check Out Our Main Menu For More Information!</h2>
        <div class="menu-button">
            <a href="main_menu.php" class="menu-btn">Go to Main Menu</a>
        </div>
        
    </main>
    <footer>
        <p>&copy; 2024 Plant Biodiversity</p>
    </footer>
</body>
</html>