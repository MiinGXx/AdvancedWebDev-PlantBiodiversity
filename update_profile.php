<?php
// Start the session to access logged-in user information
session_start();
if (!isset($_SESSION['email'])) {
    header('Location: login.php');
    exit;
}

// Include the database connection
require 'db_connection.php';

// Fetch user information from the database
$email = $_SESSION['email'];
$query = $conn->prepare("SELECT * FROM user_table WHERE email = ?");
$query->bind_param("s", $email);
$query->execute();
$result = $query->get_result();
$userInfo = $result->fetch_assoc();

// Default profile images based on gender
$defaultImages = [
    'Male' => 'images/profile_images/boys.jpg',
    'Female' => 'images/profile_images/girl.png',
];

// Initialize an array to store any errors
$errors = [];

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Update user information from the form
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $dob = $_POST['dob'];
    $gender = $_POST['gender'];
    $hometown = $_POST['hometown'];
    $new_email = $_POST['email'];
    
    // Set profile image based on updated gender
    $profile_image = $defaultImages[$gender];

    // Validate email and check for duplicates if email has been changed
    if ($new_email !== $email) {
        $email_check = $conn->prepare("SELECT email FROM user_table WHERE email = ?");
        $email_check->bind_param("s", $new_email);
        $email_check->execute();
        $email_check->store_result();
        
        if ($email_check->num_rows > 0) {
            $errors['email'] = 'The email address is already in use. Please use a different email.';
        }
        $email_check->close();
    }

    // Handle password update
    if (!empty($_POST['old_password']) && !empty($_POST['new_password'])) {
        $old_password = $_POST['old_password'];
        $new_password = $_POST['new_password'];

        // Check old password
        $account_query = $conn->prepare("SELECT password FROM account_table WHERE email = ?");
        $account_query->bind_param("s", $email);
        $account_query->execute();
        $account_result = $account_query->get_result();
        $account_data = $account_result->fetch_assoc();

        if (!password_verify($old_password, $account_data['password'])) {
            $errors['old_password'] = 'Old password is incorrect.';
        } elseif (strlen($new_password) < 8 || !preg_match('/[0-9]/', $new_password) || !preg_match('/[\W_]/', $new_password)) {
            $errors['new_password'] = 'Password must be at least 8 characters long and contain a number and a symbol.';
        } else {
            $hashed_new_password = password_hash($new_password, PASSWORD_BCRYPT);
        }
        $account_query->close();
    }

    // If there are no errors, proceed with the update
    if (empty($errors)) {
        // Update user_table, including profile image based on gender
        $update_user = $conn->prepare("UPDATE user_table SET first_name = ?, last_name = ?, dob = ?, gender = ?, hometown = ?, email = ?, profile_image = ? WHERE email = ?");
        $update_user->bind_param("ssssssss", $first_name, $last_name, $dob, $gender, $hometown, $new_email, $profile_image, $email);
        
        if ($update_user->execute()) {
            // Update account_table if the password has been changed
            if (!empty($hashed_new_password)) {
                $update_account = $conn->prepare("UPDATE account_table SET password = ? WHERE email = ?");
                $update_account->bind_param("ss", $hashed_new_password, $email);
                $update_account->execute();
                $update_account->close();
            }

            // Update session email if email was changed
            if ($new_email !== $email) {
                $_SESSION['email'] = $new_email;
            }

            header("Location: main_menu.php");
            exit;
        } else {
            echo "Error updating profile. Please try again.";
        }
        $update_user->close();
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en" class="update-profile">
<head>
    <title>Plant Biodiversity | Update Profile</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" type="text/css" href="style/style.css">
</head>
<body>
    <main class="update-profile">
        <section>
            <h1>Update Profile</h1>
            <div class="profile-card">
                <img src="<?php echo $profileImage; ?>" alt="Profile Image">
                <form method="post">
                    <!-- First Name and Last Name -->
                    <div class="form-group">
                        <div>
                            <label for="first_name">First Name:</label>
                            <input type="text" id="first_name" name="first_name" value="<?php echo htmlspecialchars($userInfo['first_name']); ?>">
                        </div>
                        <div>
                            <label for="last_name">Last Name:</label>
                            <input type="text" id="last_name" name="last_name" value="<?php echo htmlspecialchars($userInfo['last_name']); ?>">
                        </div>
                    </div>

                    <!-- Date of Birth and Gender -->
                    <div class="form-group">
                        <div>
                            <label for="dob">Date of Birth:</label>
                            <input type="date" id="dob" name="dob" value="<?php echo htmlspecialchars($userInfo['dob']); ?>">
                        </div>
                        <div>
                            <label>Gender:</label>
                            <div class="gender-group">
                                <input type="radio" id="male" name="gender" value="Male" <?php echo $userInfo['gender'] === 'Male' ? 'checked' : ''; ?>>
                                <label for="male">Male</label>
                                <input type="radio" id="female" name="gender" value="Female" <?php echo $userInfo['gender'] === 'Female' ? 'checked' : ''; ?>>
                                <label for="female">Female</label>
                            </div>
                        </div>
                    </div>

                    <!-- Email and Hometown -->
                    <div class="form-group">
                        <div>
                            <label for="email">Email:</label>
                            <input type="text" id="email" name="email" value="<?php echo htmlspecialchars($userInfo['email']); ?>">
                            <div class="error"><?php echo isset($errors['email']) ? $errors['email'] : ''; ?></div>
                        </div>
                        <div>
                            <label for="hometown">Hometown:</label>
                            <input type="text" id="hometown" name="hometown" value="<?php echo htmlspecialchars($userInfo['hometown']); ?>">
                        </div>
                    </div>

                    <!-- Password Change -->
                    <div class="form-group">
                        <div>
                            <label for="old_password">Old Password:</label>
                            <input type="password" id="old_password" name="old_password">
                            <div class="error"><?php echo isset($errors['old_password']) ? $errors['old_password'] : ''; ?></div>
                        </div>
                        <div>
                            <label for="new_password">New Password:</label>
                            <input type="password" id="new_password" name="new_password">
                            <div class="error"><?php echo isset($errors['new_password']) ? $errors['new_password'] : ''; ?></div>
                        </div>
                    </div>

                    <!-- Submit and Cancel Buttons -->
                    <div class="button-group">
                        <button type="submit" name="update" class="submit-button">Update</button>
                        <button type="button" onclick="window.location.href='main_menu.php'" class="reset-button">Cancel</button>
                    </div>
                </form>
            </div>
        </section>
    </main>
</body>
</html>
