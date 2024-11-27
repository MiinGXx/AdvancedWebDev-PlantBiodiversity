<?php
session_start();

if (!isset($_SESSION['email']) || $_SESSION['type'] !== 'user') {
    header("Location: login.php");
    exit();
}

require 'vendor/autoload.php';
require 'db_connection.php';

$message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['plantImage'])) {
    $image = $_FILES['plantImage'];
    
    if ($image['error'] === UPLOAD_ERR_OK) {
        // Set API URL and Key
        $apiKey = '2b10Md2ofy3RmvDBMxEdIiA7O'; // Or retrieve from config file
        $apiUrl = "https://my-api.plantnet.org/v2/identify/all?api-key=$apiKey&include-related-images=true&lang=en";

        // Send the image to the API
        $client = new GuzzleHttp\Client();
        try {
            $apiRequest = $client->request('POST', $apiUrl, [
                'multipart' => [
                    [
                        'name'     => 'images',
                        'contents' => fopen($image['tmp_name'], 'r'),
                        'filename' => $image['name']
                    ],
                    [
                        'name'     => 'organs',
                        'contents' => 'leaf', 'flower', 'fruit' // You can change this to other organs if needed
                    ]
                ]
            ]);

            // Decode the response
            $response = json_decode($apiRequest->getBody(), true);

            // Display the results
            if (isset($response['results']) && count($response['results']) > 0) {
                $message = "<p class='success'>Plant identified successfully!</p>";
                for ($i = 0; $i < min(3, count($response['results'])); $i++) {
                    $scientificName = $response['results'][$i]['species']['scientificNameWithoutAuthor'];
                    // Check if the scientific name exists in the database
                    $stmt = $conn->prepare("SELECT * FROM plant_table WHERE Scientific_Name = ?");
                    $stmt->bind_param("s", $scientificName);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $plant = $result->fetch_assoc();

                    if ($plant) {
                        $message .= "<img src='" . htmlspecialchars($plant['plants_image']) . "' alt='Herbarium Specimen Photo'>";
                        $message .= "<h2>" . htmlspecialchars($plant['Scientific_Name']) . "</h2>";
                        $message .= "<p><strong>Common Name:</strong> " . htmlspecialchars($plant['Common_Name']) . "</p>";
                        $message .= "<p><strong>Family:</strong> " . htmlspecialchars($plant['family']) . "</p>";
                        $message .= "<p><strong>Genus:</strong> " . htmlspecialchars($plant['genus']) . "</p>";
                        $message .= "<p><strong>Species:</strong> " . htmlspecialchars($plant['species']) . "</p>";
                        $message .= "<p><strong>Description:</strong> <a href='" . htmlspecialchars($plant['description']) . "' download>Download PDF</a></p>";
                    }

                    $stmt->close();
                }
                $message .= "</ul>";
            } else {
                $message = "<p class='error'>Oops! Plant data can't be found!</p>";
            }
        } catch (GuzzleHttp\Exception\ClientException $e) { // Catch API errors
            if ($e->getResponse()->getStatusCode() == 404) {
                $message = "<p class='error'>Oops! Plant data can't be found!</p>";
            } else {
                $message = "<p class='error'>An error occurred: " . htmlspecialchars($e->getMessage()) . "</p>";
            }
        }
    } else { // Handle file upload errors
        $message = "<p>Error uploading the file.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en" class="identify">
<head>
    <title>Plant Biodiversity Portal | Identify</title>
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
            <h2>Plant Identifier</h2>
            <p>Upload an image of a plant leaf to identify the plant species.</p>
        </section>
        
        <section class="plant-identification">
            <h2>Plant Identification</h2>
            <form method="post" enctype="multipart/form-data">
                <label for="plantImage">Upload Plant Image:</label>
                <input type="file" name="plantImage" id="plantImage" accept="image/*" required>
                <br>
                <button type="submit">Identify Plant</button>
            </form>
        </section>
            
        <?php if (!empty($message)): ?>
            <section class="plant-info">
                <?php echo $message; ?>
            </section>
        <?php endif; ?>
    </main>

    <footer>
        <p>&copy; 2024 Plant Biodiversity Portal | <a href="profile.php">Isaac Ng Ming Hong</a></p>
    </footer>

</body>
</html>