<?php
    // Array of plant data
    $plants = [
        1 => [
            'scientific_name' => 'Rosa chinensis',
            'common_name' => 'China Rose',
            'photo' => 'path/to/china_rose.jpg',
        ],
        2 => [
            'scientific_name' => 'Ficus lyrata',
            'common_name' => 'Fiddle Leaf Fig',
            'photo' => 'path/to/fiddle_leaf_fig.jpg',
        ],
        3 => [
            'scientific_name' => 'Aloe vera',
            'common_name' => 'Aloe Vera',
            'photo' => 'path/to/aloe_vera.jpg',
        ],
        4 => [
            'scientific_name' => 'Monstera deliciosa',
            'common_name' => 'Swiss Cheese Plant',
            'photo' => 'path/to/monstera.jpg',
        ],
        5 => [
            'scientific_name' => 'Lavandula angustifolia',
            'common_name' => 'Lavender',
            'photo' => 'path/to/lavender.jpg',
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
        <p>&copy; 2024 Plant Biodiversity</p>
    </footer>
</body>
</html>
