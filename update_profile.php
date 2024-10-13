<?php
// Path to the text file containing user information
$userFile = 'data/user.txt';

// Start the session to access logged-in user information
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

// Function to get all users' information from the text file
function getAllUsers($file) {
    $users = [];
    
    if (file_exists($file)) {
        $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        
        foreach ($lines as $line) {
            $fields = explode('|', $line);
            $userInfo = [];
            foreach ($fields as $field) {
                list($key, $value) = explode(':', $field);
                $userInfo[trim($key)] = trim($value);
            }
            $users[] = $userInfo; // Add the user information to the list
        }
    }
    
    return $users;
}

// Function to find the logged-in user by email
function findUserByEmail($users, $email) {
    foreach ($users as $user) {
        if ($user['Email'] === $email) {
            return $user;
        }
    }
    return null;
}

// Function to check if the email already exists
function emailExists($users, $newEmail, $currentEmail) {
    foreach ($users as $user) {
        if ($user['Email'] === $newEmail && $user['Email'] !== $currentEmail) {
            return true; // Email exists and is different from the current email
        }
    }
    return false;
}

// Function to update user information and save it back to the file
function updateUserInfo($file, $updatedUser) {
    $users = getAllUsers($file);

    foreach ($users as &$user) {
        if ($user['Email'] === $updatedUser['Email']) {
            $user = $updatedUser; // Update the user data
            break;
        }
    }

    // Write all users back to the file
    $data = '';
    foreach ($users as $user) {
        $data .= "First Name:{$user['First Name']}|Last Name:{$user['Last Name']}|DOB:{$user['DOB']}|Gender:{$user['Gender']}|Email:{$user['Email']}|Hometown:{$user['Hometown']}|Password:{$user['Password']}\n";
    }
    file_put_contents($file, $data);
}

// Get all users from the file
$users = getAllUsers($userFile);

// Get the logged-in user by their email
$loggedInEmail = $_SESSION['user'];
$userInfo = findUserByEmail($users, $loggedInEmail);

// Default profile images based on gender
$defaultImages = [
    'male' => 'images/profile_images/boys.jpg',
    'female' => 'images/profile_images/girl.png',
];

// Handle form submission
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update'])) {
        // Validate and update user information from the form
        if (!empty($_POST['first_name']) && !empty($_POST['last_name']) && !empty($_POST['dob']) && !empty($_POST['gender']) && !empty($_POST['email']) && !empty($_POST['hometown'])) {
            $userInfo['First Name'] = $_POST['first_name'];
            $userInfo['Last Name'] = $_POST['last_name'];
            $userInfo['DOB'] = $_POST['dob'];
            $userInfo['Gender'] = $_POST['gender'];
            $newEmail = $_POST['email'];
            $userInfo['Email'] = $newEmail;
            $userInfo['Hometown'] = $_POST['hometown'];

            // Check if the new email already exists
            if (emailExists($users, $newEmail, $loggedInEmail)) {
                $errors['email'] = 'The email address already exists. Please use a different email.';
            }

            // Check for old password
            if (!empty($_POST['old_password']) && !empty($_POST['new_password'])) {
                // Verify the old password
                if ($_POST['old_password'] === $userInfo['Password']) {
                    $userInfo['Password'] = $_POST['new_password'];
                } else {
                    $errors['old_password'] = 'Old password is incorrect.';
                }
            }

            // If no errors, save updated information to the file
            if (empty($errors)) {
                updateUserInfo($userFile, $userInfo);
                header('Location: main_menu.php');
                exit;
            }
        } else {
            $errors['form'] = 'All fields are required except for password.';
        }
    } elseif (isset($_POST['cancel'])) {
        // Redirect to main menu
        header('Location: main_menu.php');
        exit;
    }
}

// Determine the profile image to display
$profileImage = isset($userInfo['Gender']) && isset($defaultImages[$userInfo['Gender']]) ? $defaultImages[$userInfo['Gender']] : null;
?>

<!DOCTYPE html>
<html lang="en" class="update-profile">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="style.css">
    <title>Plant Biodiversity | Update Profile</title>
</head>
<body>
    <main>
        <section>
            <h1>Update Profile</h1>
            <?php if ($userInfo): ?>
                <div class="profile-card">
                    <?php if ($profileImage): ?>
                        <img src="<?php echo $profileImage; ?>" alt="Profile Image">
                    <?php endif; ?>
                    <form method="post">
                        <!-- First Name and Last Name -->
                        <div class="form-group">
                            <div>
                                <label for="first_name">First Name:</label>
                                <input type="text" id="first_name" name="first_name" size="25" value="<?php echo htmlspecialchars($userInfo['First Name']); ?>">
                            </div>
                            <div>
                                <label for="last_name">Last Name:</label>
                                <input type="text" id="last_name" name="last_name" size="25" value="<?php echo htmlspecialchars($userInfo['Last Name']); ?>">
                            </div>
                        </div>

                        <!-- Date of Birth and Gender -->
                        <div class="form-group">
                            <div>
                                <label for="dob">Date of Birth:</label>
                                <input type="date" id="dob" name="dob" value="<?php echo htmlspecialchars($userInfo['DOB']); ?>">
                            </div>
                            <div>
                                <label>Gender:</label>
                                <div class="gender-group">
                                    <input type="radio" id="male" name="gender" value="male" <?php echo $userInfo['Gender'] === 'male' ? 'checked' : ''; ?>>
                                    <label for="male">Male</label>
                                    
                                    <input type="radio" id="female" name="gender" value="female" <?php echo $userInfo['Gender'] === 'female' ? 'checked' : ''; ?>>
                                    <label for="female">Female</label>
                                </div>
                            </div>
                        </div>

                        <!-- Email and Hometown -->
                        <div class="form-group">
                            <div>
                                <label for="email">Email:</label>
                                <input type="text" id="email" name="email" value="<?php echo htmlspecialchars($userInfo['Email']); ?>">
                                <div class="error"><?php echo isset($errors['email']) ? $errors['email'] : ''; ?></div>
                            </div>
                            <div>
                                <label for="hometown">Hometown:</label>
                                <input type="text" id="hometown" name="hometown" size="25" value="<?php echo htmlspecialchars($userInfo['Hometown']); ?>">
                            </div>
                        </div>

                        <!-- Old Password and New Password -->
                        <div class="form-group">
                            <div>
                                <label for="old_password">Old Password:</label>
                                <input type="password" id="old_password" name="old_password">
                                <div class="error"><?php echo isset($errors['old_password']) ? $errors['old_password'] : ''; ?></div>
                            </div>
                            <div>
                                <label for="new_password">New Password:</label>
                                <input type="password" id="new_password" name="new_password">
                            </div>
                        </div>

                        <!-- Display error messages -->
                        <?php if (isset($errors['form'])): ?>
                            <div class="error"><?php echo $errors['form']; ?></div>
                        <?php endif; ?>

                        <!-- Submit and Reset Buttons -->
                        <div class="button-group">
                            <button type="submit" name="update" class="submit-button">Update</button>
                            <button type="submit" name="cancel" class="reset-button">Cancel</button>
                        </div>
                    </form>
                </div>
            <?php else: ?>
                <p>No user information found.</p>
            <?php endif; ?>
        </section>
    </main>
</body>
</html>
