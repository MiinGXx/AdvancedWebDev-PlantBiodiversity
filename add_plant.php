<?php
session_start();
if ($_SESSION['type'] != 'admin') {
    header("Location: login.php");
    exit;
}

require 'db_connection.php';

$errors = [];
$postData = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect plant details
    $scientific_name = $_POST['scientific_name'];
    $common_name = $_POST['common_name'];
    $family = $_POST['family'];
    $genus = $_POST['genus'];
    $species = $_POST['species'];
    $status = 'pending';

    // Validate inputs
    if (empty($scientific_name)) $errors['scientific_name'] = "Scientific name is required.";
    if (empty($common_name)) $errors['common_name'] = "Common name is required.";
    if (empty($family)) $errors['family'] = "Family is required.";
    if (empty($genus)) $errors['genus'] = "Genus is required.";
    if (empty($species)) $errors['species'] = "Species is required.";

    // Handle PDF upload for the description
    if (isset($_FILES['description']) && $_FILES['description']['error'] == 0) {
        $pdf_name = $_FILES['description']['name'];
        $pdf_tmp_name = $_FILES['description']['tmp_name'];
        $pdf_ext = strtolower(pathinfo($pdf_name, PATHINFO_EXTENSION));
        
        // Ensure only PDF files are allowed
        if ($pdf_ext === 'pdf') {
            $new_pdf_name = uniqid("plant_desc_", true) . '.' . $pdf_ext;
            $pdf_upload_path = 'pdf/' . $new_pdf_name;

            // Move uploaded PDF to the 'pdf/' directory
            if (move_uploaded_file($pdf_tmp_name, $pdf_upload_path)) {
                // PDF uploaded successfully
            } else {
                $errors['description'] = "Failed to upload the PDF file.";
            }
        } else {
            $errors['description'] = "Only PDF files are allowed for the description.";
        }
    } else {
        $errors['description'] = "A description PDF is required.";
    }

    // Handle image upload for plant
    if (isset($_FILES['plant_image']) && $_FILES['plant_image']['error'] == 0) {
        $image_name = $_FILES['plant_image']['name'];
        $image_tmp_name = $_FILES['plant_image']['tmp_name'];
        $image_ext = strtolower(pathinfo($image_name, PATHINFO_EXTENSION));
        $allowed_ext = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($image_ext, $allowed_ext)) {
            $new_image_name = uniqid("plant_", true) . '.' . $image_ext;
            $image_upload_path = 'images/plant_images/' . $new_image_name;
            move_uploaded_file($image_tmp_name, $image_upload_path);
        } else {
            $errors['plant_image'] = "Invalid image format. Allowed formats: jpg, jpeg, png, gif.";
        }
    } else {
        $errors['plant_image'] = "Plant image is required.";
    }

    // If no errors, insert data into the database
    if (empty($errors)) {
        $stmt = $conn->prepare("INSERT INTO plant_table (Scientific_Name, Common_Name, Family, Genus, Species, plants_image, description, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssss", $scientific_name, $common_name, $family, $genus, $species, $image_upload_path, $pdf_upload_path, $status);

        if ($stmt->execute()) {
            header("Location: manage_plants.php?added=1");
            exit;
        } else {
            $errors['general'] = "Error adding plant. Please try again.";
        }
        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en" class="add-plant">
<head>
    <title>Plant Biodiversity Portal | Add New Plant</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" type="text/css" href="style/style.css">
</head>
<body>
    <main class="container">
        <h2>Add New Plant</h2>
        <form method="post" enctype="multipart/form-data">
            <div class="form-row">
                <div class="form-group">
                    <label for="scientific_name">Scientific Name:</label>
                    <input type="text" id="scientific_name" name="scientific_name">
                    <div class="error"><?php echo isset($errors['scientific_name']) ? $errors['scientific_name'] : ''; ?></div>
                </div>
                
                <div class="form-group">
                    <label for="common_name">Common Name:</label>
                    <input type="text" id="common_name" name="common_name">
                    <div class="error"><?php echo isset($errors['common_name']) ? $errors['common_name'] : ''; ?></div>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="family">Family:</label>
                    <input type="text" id="family" name="family">
                    <div class="error"><?php echo isset($errors['family']) ? $errors['family'] : ''; ?></div>
                </div>

                <div class="form-group">
                    <label for="genus">Genus:</label>
                    <input type="text" id="genus" name="genus">
                    <div class="error"><?php echo isset($errors['genus']) ? $errors['genus'] : ''; ?></div>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="species">Species:</label>
                    <input type="text" id="species" name="species">
                    <div class="error"><?php echo isset($errors['species']) ? $errors['species'] : ''; ?></div>
                </div>

                <div class="form-group">
                    <label for="description">Description (PDF):</label>
                    <input type="file" id="description" name="description" accept=".pdf">
                    <div class="error"><?php echo isset($errors['description']) ? $errors['description'] : ''; ?></div>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="plant_image">Plant Image:</label>
                    <input type="file" id="plant_image" name="plant_image" accept=".jpg, .jpeg, .png, .gif">
                    <div class="error"><?php echo isset($errors['plant_image']) ? $errors['plant_image'] : ''; ?></div>
                </div>
            </div>
            
            <div class="button-group">
                <button type="submit" class="submit-button">Add Plant</button>
                <button type="button" onclick="window.location.href='manage_plants.php'" class="cancel-button">Cancel</button>
            </div>

            <?php if (isset($errors['general'])) : ?>
                <div class="error"><?php echo $errors['general']; ?></div>
            <?php endif; ?>
        </form>
    </main>
</body>
</html>
