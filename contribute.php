<?php
    // Array of plant data
    $plants = [
        1 => [
            'scientific_name' => 'Endiandra sieberi',
            'common_name' => 'Corkwood',
            'photo' => 'images/plant_images/Lauraceae_Endiandra_Sieberi.jpg',
        ],
        2 => [
            'scientific_name' => 'Ficus lyrata',
            'common_name' => 'Fiddle Leaf Fig',
            'photo' => 'images/plant_images/Moraceae_Ficus_Lyrata.jpg',
        ],
        3 => [
            'scientific_name' => 'Aloe vera',
            'common_name' => 'Aloe Vera',
            'photo' => 'images/plant_images/Asphodelaceae_Aloe_Vera.jpg',
        ],
        4 => [
            'scientific_name' => 'Monstera deliciosa',
            'common_name' => 'Swiss Cheese Plant',
            'photo' => 'images/plant_images/Araceae_Monsterra_Deliciosa.jpg',
        ],
        5 => [
            'scientific_name' => 'Lavandula angustifolia',
            'common_name' => 'Lavender',
            'photo' => 'images/plant_images/Lamiaceae_Lavandula_Angustifolia.jpg',
        ],
    ];
?>  

<!DOCTYPE html>
<html lang="en" class="contribute">
<head>
    <title>Plant Biodiversity Portal | Contribute</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>

<body>
    <header>
        <h1>Plant Biodiversity Portal</h1>
        <nav>
            <ul>            
                <li><a href="index.php">Home</a></li>          
                <li><a href="main_menu.php">Main Menu</a></li>             
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>    
    </header> 

    <main>
        <section class="hero">
            <h2>Contribute</h2>
            <p>Upload photos of fresh leaves and herbarium specimens with information to contribute to the dataset. Your contributions will be used to identify plant species in the Identify section.</p>
        </section>
        
        <section class="contributed-plants">
            <h1>Contributed Plants</h1>
            <div class="plant-list">
            <?php
            // Loop through each plant and display its card
            foreach ($plants as $id => $plant) {
                echo '
                <div class="plant-card">
                    <a href="plant_detail.php?id=' . $id . '">
                        <img src="' . $plant['photo'] . '" alt="' . $plant['common_name'] . '">
                        <h2>' . $plant['scientific_name'] . '</h2>
                        <p>' . $plant['common_name'] . '</p>
                    </a>
                </div>';
            }
            ?>
            </div>
        </section>
    </main>

    <footer>
        <p>&copy; 2024 Plant Biodiversity | <a href="profile.php">Isaac Ng Ming Hong</a></p></p>
    </footer>
</body>
</html>
