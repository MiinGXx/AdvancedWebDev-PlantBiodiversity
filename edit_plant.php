<?php
session_start();
if ($_SESSION['type'] != 'admin') {
    header("Location: login.php");
    exit;
}

require 'db_connection.php';

// Retrieve plant details based on the plant ID
$plantId = $_GET['id'];
$stmt = $conn->prepare("SELECT * FROM plant_table WHERE id = ?");
$stmt->bind_param("i", $plantId);
$stmt->execute();
$result = $stmt->get_result();
$plant = $result->fetch_assoc();
$stmt->close();

$error = '';

// Check if the form has been submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $scientific_name = $_POST['scientific_name'];
    $common_name = $_POST['common_name'];
    $family = $_POST['family'];
    $genus = $_POST['genus'];
    $species = $_POST['species'];
    $description = $plant['description'];
    $plants_image = $plant['plants_image']; // Keep existing image path

    // Handle image upload if a new one is uploaded
    if (isset($_FILES['plants_image']) && $_FILES['plants_image']['error'] == 0) {
        $image_name = $_FILES['plants_image']['name'];
        $image_tmp_name = $_FILES['plants_image']['tmp_name'];
        $image_ext = strtolower(pathinfo($image_name, PATHINFO_EXTENSION));

        if (in_array($image_ext, ['jpg', 'jpeg', 'png'])) {
            $new_image_name = uniqid("plant_", true) . '.' . $image_ext;
            $image_upload_path = 'images/plant_images/' . $new_image_name;

            // Delete the old image file if it exists and is different from the new file
            if ($plants_image && file_exists($plants_image) && $plants_image != $image_upload_path) {
                unlink($plants_image);
            }

            // Upload the new image
            move_uploaded_file($image_tmp_name, $image_upload_path);
            $plants_image = $image_upload_path; // Update the path to the new image
        } else {
            $error = "Only JPG, JPEG, and PNG files are allowed for the image.";
        }
    }

    // Update plant details in the database
    $updateStmt = $conn->prepare("UPDATE plant_table SET Scientific_Name = ?, Common_Name = ?, Family = ?, Genus = ?, Species = ?, description = ?, plants_image = ? WHERE id = ?");
    $updateStmt->bind_param("sssssssi", $scientific_name, $common_name, $family, $genus, $species, $description, $plants_image, $plantId);
    if ($updateStmt->execute()) {
        header("Location: manage_plants.php?message=Plant updated successfully");
        exit;
    } else {
        $error = "Failed to update plant details.";
    }
    $updateStmt->close();
}

$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <title>Edit Plant</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="style/style.css">
</head>
<body class="edit-plant">
    <header>
        <h1>Edit Plant Details</h1>
    </header>
    <main>
        <?php if (!empty($error)) : ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="post" enctype="multipart/form-data">
            <div class="form-row">
                <div class="form-group">
                    <label for="scientific_name">Scientific Name:</label>
                    <input type="text" id="scientific_name" name="scientific_name" value="<?php echo htmlspecialchars($plant['Scientific_Name']); ?>">
                </div>
                <div class="form-group">
                    <label for="common_name">Common Name:</label>
                    <input type="text" id="common_name" name="common_name" value="<?php echo htmlspecialchars($plant['Common_Name']); ?>">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="family">Family:</label>
                    <input type="text" id="family" name="family" value="<?php echo htmlspecialchars($plant['family']); ?>">
                </div>
                <div class="form-group">
                    <label for="genus">Genus:</label>
                    <input type="text" id="genus" name="genus" value="<?php echo htmlspecialchars($plant['genus']); ?>">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="species">Species:</label>
                    <input type="text" id="species" name="species" value="<?php echo htmlspecialchars($plant['species']); ?>">
                </div>
                <div class="form-group">
                    <label for="description">Description (PDF):</label>
                    <input type="file" id="description" name="description" accept=".pdf">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Current Image:</label>
                    <img src="<?php echo htmlspecialchars($plant['plants_image']); ?>" alt="Plant Image" class="current-image">
                    
                </div>

                <div class="form-group">
                    <label for="plants_image">Change Image (JPG, JPEG, PNG):</label>
                    <input type="file" id="plants_image" name="plants_image" accept=".jpg, .jpeg, .png">

                    <p>Current Description: <a href="<?php echo htmlspecialchars($plant['description']); ?>" download>Download PDF</a></p>
                </div>
            </div>

            <div class="button-group">
                <button type="submit" class="submit-button">Save Changes</button>
                <button type="button" onclick="window.location.href='manage_plants.php'" class="cancel-button">Cancel</button>
            </div>
        </form>
    </main>
</body>
</html>
