<?php
$plants = [
    1 => [
        'scientific_name' => 'Endiandra sieberi',
        'common_name' => 'Corkwood',
        'family' => 'Lauraceae',
        'genus' => 'Endiandra',
        'species' => 'E. sieberi',
        'photo' => 'images/plant_images/Lauraceae_Endiandra_Sieberi.jpg',
        'description_pdf' => 'pdf/corkwood_description.pdf',
    ],
    2 => [
        'scientific_name' => 'Ficus lyrata',
        'common_name' => 'Fiddle Leaf Fig',
        'family' => 'Moraceae',
        'genus' => 'Ficus',
        'species' => 'F. lyrata',
        'photo' => 'images/plant_images/Moraceae_Ficus_Lyrata.jpg',
        'description_pdf' => 'pdf/fiddle_leaf_fig_description.pdf',
    ],
    3 => [
        'scientific_name' => 'Aloe vera',
        'common_name' => 'Aloe Vera',
        'family' => 'Asphodelaceae',
        'genus' => 'Aloe',
        'species' => 'A. vera',
        'photo' => 'images/plant_images/Asphodelaceae_Aloe_Vera.jpg',
        'description_pdf' => 'pdf/aloe_vera_description.pdf',
    ],
    4 => [
        'scientific_name' => 'Monstera deliciosa',
        'common_name' => 'Swiss Cheese Plant',
        'family' => 'Araceae',
        'genus' => 'Monstera',
        'species' => 'M. deliciosa',
        'photo' => 'images/plant_images/Araceae_Monsterra_Deliciosa.jpg',
        'description_pdf' => 'pdf/monstera_description.pdf',
    ],
    5 => [
        'scientific_name' => 'Lavandula angustifolia',
        'common_name' => 'Lavender',
        'family' => 'Lamiaceae',
        'genus' => 'Lavandula',
        'species' => 'L. angustifolia',
        'photo' => 'images/plant_images/Lamiaceae_Lavandula_Angustifolia.jpg',
        'description_pdf' => 'pdf/lavender_description.pdf',
    ],
];

// Get plant ID from the URL (e.g., plant_detail.php?id=1)
$plant_id = isset($_GET['id']) ? (int)$_GET['id'] : 1; // Default to plant ID 1 if not set

// Check if the plant exists in the array
if (!array_key_exists($plant_id, $plants)) {
    echo "Plant not found!";
    exit;
}

// Get the plant details
$plant = $plants[$plant_id];
?>


<!DOCTYPE html>
<html lang="en" class="detail">
<head>
    <title>Plant Biodiversity Portal | Plant Detail</title>
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
            <h2>Plant Detail: <?php echo $plant['scientific_name']; ?></h2>
            <p>View detailed information about the plant species.</p>
        </section>

        <br><br><br>

        <section class="plant-info">
            <img src="<?php echo $plant['photo']; ?>" alt="Herbarium Specimen Photo">
            <h2><?php echo $plant['scientific_name']; ?></h2>
            <p><strong>Common Name:</strong> <?php echo $plant['common_name']; ?></p>
            <p><strong>Family:</strong> <?php echo $plant['family']; ?></p>
            <p><strong>Genus:</strong> <?php echo $plant['genus']; ?></p>
            <p><strong>Species:</strong> <?php echo $plant['species']; ?></p>
            <p><strong>Description:</strong> <a href="<?php echo $plant['description_pdf']; ?>" download>Download PDF</a></p>
        </section>

        <section class="back-to-list">
            <a href="contribute.php">Back to Contribute Page</a>
        </section>
    </main>

    <footer>
        <p>&copy; 2024 Plant Biodiversity | <a href="profile.php">Isaac Ng Ming Hong</a></p></p>
    </footer>
</body>
</html>
