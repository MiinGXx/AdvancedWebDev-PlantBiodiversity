<?php
// Path to the text file containing user information
$userFile = 'user_info.txt';

// Function to get user information from the text file
function getUserInfo($file) {
    if (file_exists($file)) {
        $data = file_get_contents($file);
        return json_decode($data, true);
    }
    return null;
}

// Function to save user information to the text file
function saveUserInfo($file, $userInfo) {
    $data = json_encode($userInfo, JSON_PRETTY_PRINT);
    file_put_contents($file, $data);
}

// Get user information
$userInfo = getUserInfo($userFile);

// Default profile image
$defaultImage = 'images/default.jpg';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update'])) {
        // Update user information
        $userInfo['name'] = $_POST['name'];
        $userInfo['email'] = $_POST['email'];
        $userInfo['gender'] = $_POST['gender'];
        saveUserInfo($userFile, $userInfo);
    } elseif (isset($_POST['cancel'])) {
        // Redirect to main menu
        header('Location: main_menu.php');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="style.css">
    <title>Plant Biodiversity | Update Profile</title>
</head>
<body>
    <main>
        <h1>Update Profile</h1>
        <?php if ($userInfo): ?>
            <div class="profile-card">
                <img src="<?php echo $defaultImage; ?>" alt="Profile Image">
                <form method="post">
                    <div>
                        <label for="name">Username:</label>
                        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($userInfo['name']); ?>" required>
                    </div>
                    <div>
                        <label for="email">Email:</label>
                        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($userInfo['email']); ?>" required>
                    </div>
                    <div>
                        <label for="gender">Gender:</label>
                        <select id="gender" name="gender" required>
                            <option value="male" <?php echo $userInfo['gender'] === 'male' ? 'selected' : ''; ?>>Male</option>
                            <option value="female" <?php echo $userInfo['gender'] === 'female' ? 'selected' : ''; ?>>Female</option>
                            <option value="other" <?php echo $userInfo['gender'] === 'other' ? 'selected' : ''; ?>>Other</option>
                        </select>
                    </div>
                    <button type="submit" name="update">Update</button>
                    <button type="submit" name="cancel">Cancel</button>
                </form>
            </div>
        <?php else: ?>
            <p>No user information found.</p>
        <?php endif; ?>
    </main>
</body>
</html>