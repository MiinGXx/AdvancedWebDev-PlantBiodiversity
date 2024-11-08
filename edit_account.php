<?php
session_start();
if ($_SESSION['type'] != 'admin') {
    header("Location: login.php");
    exit;
}

require 'db_connection.php';

// Initialize error array and retrieve user data based on the email passed in the URL
$errors = [];
$email = isset($_GET['email']) ? $_GET['email'] : '';

if ($email) {
    // Modified SQL query to include type from account_table
    $stmt = $conn->prepare("SELECT user_table.*, account_table.type FROM user_table JOIN account_table ON user_table.email = account_table.email WHERE user_table.email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $userData = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

// Check if form is submitted for updating the account
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $dob = $_POST['dob'];
    $gender = $_POST['gender'];
    $hometown = $_POST['hometown'];
    $type = $_POST['type'];

    // Validate email format and other fields as necessary
    if (empty($email)) {
        $errors['email'] = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Please provide a valid email.";
    }

    // Handle any other validation as necessary

    // Update the database if no errors
    if (empty($errors)) {
        $profile_image = ($gender === "Male") ? "images/profile_images/default_male.jpg" : "images/profile_images/default_female.jpg";
        
        // Update user data in `user_table`
        $updateUser = $conn->prepare("UPDATE user_table SET first_name = ?, last_name = ?, dob = ?, gender = ?, hometown = ?, profile_image = ? WHERE email = ?");
        $updateUser->bind_param("sssssss", $first_name, $last_name, $dob, $gender, $hometown, $profile_image, $email);
        $updateUser->execute();
        $updateUser->close();

        // Update account type in `account_table`
        $updateAccount = $conn->prepare("UPDATE account_table SET type = ? WHERE email = ?");
        $updateAccount->bind_param("ss", $type, $email);
        $updateAccount->execute();
        $updateAccount->close();

        // Redirect back to manage_accounts.php with a success message
        header("Location: manage_accounts.php?updated=1");
        exit;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en" class="edit-account">
<head>
    <title>Plant Biodiversity Portal | Edit Account</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" type="text/css" href="style/style.css">
</head>
<body>
    <main class="container">
        <h2>Edit Account</h2>
        <form method="post">
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
                    <input type="date" id="dob" name="dob" value="<?php echo htmlspecialchars($userData['dob']); ?>">
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
                    <input type="email" id="email" name="email" required value="<?php echo htmlspecialchars($userData['email']); ?>" readonly>
                    <div class="error"><?php echo isset($errors['email']) ? $errors['email'] : ''; ?></div>
                </div>

                <div class="form-group">
                    <label for="hometown">Hometown:</label>
                    <input type="text" id="hometown" name="hometown" value="<?php echo htmlspecialchars($userData['hometown']); ?>">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password">
                </div>

                <div class="form-group">
                    <label for="type">Account Type:</label>
                    <select id="type" name="type" required>
                        <option value="user" <?php echo ($userData['type'] == 'user') ? 'selected' : ''; ?>>User</option>
                        <option value="admin" <?php echo ($userData['type'] == 'admin') ? 'selected' : ''; ?>>Admin</option>
                    </select>
                </div>
            </div>

            <div class="button-group">
                <button type="submit" class="submit-button">Update Account</button>
                <button type="button" onclick="window.location.href='manage_accounts.php'" class="cancel-button">Cancel</button>
            </div>

            <?php if (isset($errors['general'])) : ?>
                <div class="error"><?php echo $errors['general']; ?></div>
            <?php endif; ?>
        </form>
    </main>
</body>
</html>
