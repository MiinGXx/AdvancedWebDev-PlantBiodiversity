<?php
session_start();
if ($_SESSION['type'] != 'admin') {
    header("Location: login.php");
    exit;
}

require 'db_connection.php';

// Initialize variables
$errors = [];
$email = isset($_GET['email']) ? $_GET['email'] : '';

if ($email) {
    $stmt = $conn->prepare("SELECT user_table.*, account_table.type FROM user_table JOIN account_table ON user_table.email = account_table.email WHERE user_table.email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $userData = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

// Handle form submission for updating the account
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $dob = $_POST['dob'];
    $gender = $_POST['gender'];
    $hometown = $_POST['hometown'];
    $contact_number = $_POST['contact_number'];
    $type = $_POST['type'];

    // Default profile image based on gender
    $profile_image = ($gender == "Male") ? "profile_images/boys.jpg" : "profile_images/girl.png";

    // Handle profile image upload if provided
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == 0) {
        $image_name = $_FILES['profile_image']['name'];
        $image_tmp_name = $_FILES['profile_image']['tmp_name'];
        $image_ext = strtolower(pathinfo($image_name, PATHINFO_EXTENSION));

        if (in_array($image_ext, ['jpg', 'jpeg', 'png'])) {
            $new_image_name = uniqid("profile_", true) . '.' . $image_ext;
            $image_upload_path = 'profile_images/' . $new_image_name;

            // Delete the old image if exists and is different from the default
            if ($userData['profile_image'] && file_exists($userData['profile_image']) && strpos($userData['profile_image'], 'default') === false) {
                unlink($userData['profile_image']);
            }

            // Upload the new image
            move_uploaded_file($image_tmp_name, $image_upload_path);
            $profile_image = $image_upload_path;
        } else {
            $errors['profile_image'] = "Only JPG, JPEG, and PNG files are allowed.";
        }
    }

    // Update user details in `user_table`
    if (empty($errors)) {
        $updateUser = $conn->prepare("UPDATE user_table SET first_name = ?, last_name = ?, dob = ?, gender = ?, hometown = ?, contact_number = ?, profile_image = ? WHERE email = ?");
        $updateUser->bind_param("ssssssss", $first_name, $last_name, $dob, $gender, $hometown, $contact_number, $profile_image, $email);
        $updateUser->execute();
        $updateUser->close();

        // Update account type in `account_table`
        $updateAccount = $conn->prepare("UPDATE account_table SET type = ? WHERE email = ?");
        $updateAccount->bind_param("ss", $type, $email);
        $updateAccount->execute();
        $updateAccount->close();

        header("Location: manage_accounts.php?updated=1");
        exit;
    }
}

// Check if dob should be disabled
$disableDob = ($userData['first_name'] === 'Admin' && $userData['last_name'] === 'User');
// Check if the contact number should be disabled
$disableContactNumber = ($userData['first_name'] === 'Admin' && $userData['last_name'] === 'User');

$conn->close();
?>

<!DOCTYPE html>
<html lang="en" class="edit-account">
<head>
    <title>Plant Biodiversity Portal | Edit Account</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" type="text/css" href="css/style.css">
</head>
<body>
    <main class="container">
        <h2>Edit Account</h2>
        
        <form method="post" enctype="multipart/form-data">
            <div class="form-row">
                <div class="form-group">
                    <label for="first_name">First Name:</label>
                    <input type="text" id="first_name" name="first_name" required value="<?php echo htmlspecialchars($userData['first_name']); ?>">
                </div>
                <div class="form-group">
                    <label for="last_name">Last Name:</label>
                    <input type="text" id="last_name" name="last_name" required value="<?php echo htmlspecialchars($userData['last_name']); ?>">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="dob">Date of Birth:</label>
                    <input type="date" id="dob" name="dob" required value="<?php echo htmlspecialchars($userData['dob']); ?>" <?php echo $disableDob ? 'disabled' : ''; ?>>
                </div>
                <div class="form-group">
                    <label>Gender:</label>
                    <div class="gender-group">
                        <input type="radio" id="male" name="gender" value="Male" <?php echo ($userData['gender'] === 'Male') ? 'checked' : ''; ?>>
                        <label for="male">Male</label>
                        <input type="radio" id="female" name="gender" value="Female" <?php echo ($userData['gender'] === 'Female') ? 'checked' : ''; ?>>
                        <label for="female">Female</label>
                    </div>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required value="<?php echo htmlspecialchars($userData['email']); ?>" readonly disabled>
                </div>
                <div class="form-group">
                    <label for="hometown">Hometown:</label>
                    <input type="text" id="hometown" name="hometown" value="<?php echo htmlspecialchars($userData['hometown']); ?>">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="contact_number">Contact Number:</label>
                    <input type="text" id="contact_number" name="contact_number" 
                        value="<?php echo htmlspecialchars($userData['contact_number']); ?>"
                        <?php echo $disableContactNumber ? 'disabled' : ''; ?>>
                </div>

                <div class="form-group">
                    <label for="type">Account Type:</label>
                    <select id="type" name="type" required>
                        <option value="user" <?php echo ($userData['type'] == 'user') ? 'selected' : ''; ?>>User</option>
                        <option value="admin" <?php echo ($userData['type'] == 'admin') ? 'selected' : ''; ?>>Admin</option>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Current Profile Image:</label>
                    <?php if (!empty($userData['profile_image'])): ?>
                        <img src="<?php echo htmlspecialchars($userData['profile_image']); ?>" alt="Profile Image" class="current-image">
                    <?php else: ?>
                        <p>No profile image available.</p>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="profile_image">Change Profile Image (JPG, JPEG, PNG):</label>
                    <input type="file" id="profile_image" name="profile_image" accept=".jpg, .jpeg, .png">
                </div>
            </div>

            <div class="button-group">
                <button type="submit" class="submit-button">Update Account</button>
                <button type="button" onclick="window.location.href='manage_accounts.php'" class="cancel-button">Cancel</button>
            </div>
            
            <?php if (!empty($errors)) : ?>
                <div class="error">
                    <?php foreach ($errors as $error) : ?>
                        <p><?php echo $error; ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </form>
    </main>
</body>
</html>
