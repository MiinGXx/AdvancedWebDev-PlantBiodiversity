<?php
require 'db_connection.php';

// Get plant ID from the URL (e.g., plant_detail.php?id=1)
$plant_id = isset($_GET['id']) ? (int)$_GET['id'] : 1; // Default to plant ID 1 if not set

// Fetch plant details from the database
$stmt = $conn->prepare("SELECT * FROM plant_table WHERE id = ?");
$stmt->bind_param("i", $plant_id);
$stmt->execute();
$result = $stmt->get_result();
$plant = $result->fetch_assoc();

if (!$plant) {
    echo "Plant not found!";
    exit;
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en" class="detail">
<head>
    <title>Plant Biodiversity Portal | Plant Detail</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" type="text/css" href="css/style.css">
</head>

<body>
    <header>
        <h1>Plant Biodiversity Portal</h1>
        <nav>
            <ul>            
                <li><a href="index.php">Home</a></li>          
                <li><a href="main_menu.php">Main Menu</a></li>    
                <li><a href="about.php">About</a></li>         
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>    
    </header>   

    <main>
        <section class="hero">
            <h2>Plant Detail: <?php echo htmlspecialchars($plant['Scientific_Name']); ?></h2>
            <p>View detailed information about the plant species.</p>
        </section>

        <br><br><br>

        <section class="plant-info">
            <img src="<?php echo htmlspecialchars($plant['plants_image']); ?>" alt="Herbarium Specimen Photo">
            <h2><?php echo htmlspecialchars($plant['Scientific_Name']); ?></h2>
            <p><strong>Common Name:</strong> <?php echo htmlspecialchars($plant['Common_Name']); ?></p>
            <p><strong>Family:</strong> <?php echo htmlspecialchars($plant['family']); ?></p>
            <p><strong>Genus:</strong> <?php echo htmlspecialchars($plant['genus']); ?></p>
            <p><strong>Species:</strong> <?php echo htmlspecialchars($plant['species']); ?></p>
            <p><strong>Description:</strong> <a href="<?php echo htmlspecialchars($plant['description']); ?>" download>Download PDF</a></p>
        </section>

        <section class="back-to-list">
            <a href="contribute.php">Back to Contribute Page</a>
        </section>
    </main>

    <footer>
        <p>&copy; 2024 Plant Biodiversity | <a href="profile.php">Isaac Ng Ming Hong</a></p>
    </footer>
</body>
</html>