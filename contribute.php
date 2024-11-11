<?php
session_start();
if (!isset($_SESSION['email'])) {
    header('Location: login.php');
    exit;
}

require 'db_connection.php';

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $scientific_name = $_POST['scientific_name'];
    $common_name = $_POST['common_name'];
    $family = $_POST['family'];
    $genus = $_POST['genus'];
    $species = $_POST['species'];
    $description = '';
    $photo = '';

    // Handle photo upload
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
        $photo_name = $_FILES['photo']['name'];
        $photo_tmp_name = $_FILES['photo']['tmp_name'];
        $photo_ext = strtolower(pathinfo($photo_name, PATHINFO_EXTENSION));

        if (in_array($photo_ext, ['jpg', 'jpeg', 'png'])) {
            $new_photo_name = str_replace(' ', '_', $scientific_name) . '.' . $photo_ext;
            $photo_upload_path = 'images/plant_images/' . $new_photo_name;

            // Check if the plant_images folder exists, if not, create it
            if (!is_dir('images/plant_images')) {
                mkdir('images/plant_images', 0777, true);
            }

            // Upload the new photo
            if (move_uploaded_file($photo_tmp_name, $photo_upload_path)) {
                $photo = $photo_upload_path;
            } else {
                $errors['photo'] = "There was an error uploading the photo.";
            }
        } else {
            $errors['photo'] = "Only JPG, JPEG, and PNG files are allowed.";
        }
    }

    // Handle description upload
    if (isset($_FILES['description']) && $_FILES['description']['error'] == 0) {
        if ($_FILES['description']['size'] <= 7 * 1024 * 1024) { // Limit file size to 7MB
            $description_name = $_FILES['description']['name'];
            $description_tmp_name = $_FILES['description']['tmp_name'];
            $description_ext = strtolower(pathinfo($description_name, PATHINFO_EXTENSION));

            if ($description_ext == 'pdf') {
                $new_description_name = str_replace(' ', '_', $scientific_name) . '.' . $description_ext;
                $description_upload_path = 'pdf/' . $new_description_name;

                // Check if the pdf folder exists, if not, create it
                if (!is_dir('pdf')) {
                    mkdir('pdf', 0777, true);
                }

                // Upload the new description
                if (move_uploaded_file($description_tmp_name, $description_upload_path)) {
                    $description = $description_upload_path;
                } else {
                    $errors['description'] = "There was an error uploading the description.";
                }
            } else {
                $errors['description'] = "Only PDF files are allowed.";
            }
        } else {
            $errors['description'] = "The file size should not exceed 7MB.";
        }
    }

    // Insert plant details into the database
    if (empty($errors)) {
        $stmt = $conn->prepare("INSERT INTO plant_table (Scientific_Name, Common_Name, family, genus, species, plants_image, description, status) VALUES (?, ?, ?, ?, ?, ?, ?, 'pending')");
        $stmt->bind_param("sssssss", $scientific_name, $common_name, $family, $genus, $species, $photo, $description);
        $stmt->execute();
        $stmt->close();

        // Set success message
        $success_message = "Plant details have been uploaded for review.";
    }
}

// Fetch approved plants
$result = $conn->query("SELECT * FROM plant_table WHERE status = 'approved'");
$plants = [];
while ($row = $result->fetch_assoc()) {
    $plants[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en" class="contribute">
<head>
    <title>Plant Biodiversity Portal | Contribute</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" type="text/css" href="style/style.css">
</head>
<body>
    <header>
        <h1>Plant Biodiversity Portal</h1>
        <nav>
            <ul>
                <li><a href="main_menu.php">Main Menu</a></li>
                <li><a href="about.php">About</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <section class="hero">
            <h2>Contribute</h2>
            <p>Upload photos of fresh leaves and herbarium specimens with information to contribute to the dataset. Your contributions will be used to identify plant species in the Identify section.</p>
        </section>

        <h1>Upload Plant Photos</h1>
        <section class="form-container">
            <div class="form-wrapper">
                <form action="contribute.php" method="post" enctype="multipart/form-data">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="scientific_name">Scientific Name:</label>
                            <input type="text" id="scientific_name" name="scientific_name" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="common_name">Common Name:</label>
                            <input type="text" id="common_name" name="common_name" required>
                        </div>
                        <div class="form-group">
                            <label for="family">Family:</label>
                            <input type="text" id="family" name="family" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="genus">Genus:</label>
                            <input type="text" id="genus" name="genus" required>
                        </div>
                        <div class="form-group">
                            <label for="species">Species:</label>
                            <input type="text" id="species" name="species" required>
                        </div>
                        <div class="form-group">
                            <label for="description">Description:</label>
                            <input type="file" id="description" name="description" required>
                            <div class="error"><?php echo $errors['description'] ?? ''; ?></div>
                        </div>
                        <div class="form-group">
                            <label for="photo">Photo:</label>
                            <input type="file" id="photo" name="photo" required>
                            <div class="error"><?php echo $errors['photo'] ?? ''; ?></div>
                        </div>
                    </div>

                    <div class="success"><?php echo $success_message ?? ''; ?></div>
                    <input type="submit" value="Upload" onclick="return confirm('Are you sure you want to upload this plant information?');">
                </form>
            </div>
        </section>

        <section class="contributed-plants">
            <h1>Contributed Plants</h1>
            <div class="plant-list">
            <?php
            // Loop through each plant and display its card
            foreach ($plants as $plant) {
                echo '
                <div class="plant-card">
                    <a href="plant_detail.php?id=' . $plant['id'] . '">
                        <img src="' . $plant['plants_image'] . '" alt="' . $plant['Common_Name'] . '">
                        <h2>' . $plant['Scientific_Name'] . '</h2>
                        <p>' . $plant['Common_Name'] . '</p>
                    </a>
                </div>';
            }
            ?>
            </div>
        </section>
    </main>

    <footer>
        <p>&copy; 2024 Plant Biodiversity | <a href="profile.php">Isaac Ng Ming Hong</a></p>
    </footer>
</body>
</html>

<?php
$conn->close();
?>