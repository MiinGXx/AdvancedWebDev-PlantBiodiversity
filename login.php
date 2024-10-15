<?php
// Start the session
session_start();

// Initialize variables for error messages
$errors = [];
$postData = [];

// Function to read user credentials from a text file
function readUsersFromFile($filename) {
    $users = [];
    
    if (file_exists($filename)) {
        // Open the file
        $file = fopen($filename, "r");
        
        // Read each line and store as an associative array
        while (($line = fgets($file)) !== false) {
            // Split the line into individual pieces
            $userData = [];
            $fields = explode('|', trim($line));
            
            // Process each field
            foreach ($fields as $field) {
                list($key, $value) = explode(':', $field);
                $userData[trim($key)] = trim($value);
            }
            
            // Use the email as the key for authentication
            $users[$userData['Email']] = $userData['Password'];
        }

        fclose($file);
    }

    return $users;
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect and sanitize input data
    $postData['email'] = isset($_POST['email']) ? trim($_POST['email']) : '';
    $postData['password'] = isset($_POST['password']) ? trim($_POST['password']) : '';

    // Validate the input
    if (empty($postData['email'])) {
        $errors['email'] = "Email is required.";
    }
    if (empty($postData['password'])) {
        $errors['password'] = "Password is required.";
    }

    // Check credentials from the user.txt file
    if (empty($errors)) {
        $users = readUsersFromFile('data/user.txt');

        // Validate user credentials
        if (!isset($users[$postData['email']]) || $users[$postData['email']] !== $postData['password']) {
            $errors['login'] = "Incorrect email or password. Please try again.";
        } else {
            // Successful login, redirect or set session
            $_SESSION['user'] = $postData['email']; // Set session variable or perform redirect
            header("Location: main_menu.php"); // Redirect to a dashboard page
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en" class="login">
<head>
    <title>Plant Biodiversity Portal | Login</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" type="text/css" href="style/style.css">
</head>

<body>
    <main class="container">
        <h1>Login to Your Account</h1>
        <form action="" method="POST">
            <div class="group">
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="text" id="email" name="email" value="<?php echo isset($postData['email']) ? htmlspecialchars($postData['email']) : ''; ?>">
                    <div class="error"><?php echo isset($errors['email']) ? $errors['email'] : ''; ?></div>
                </div>

                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password">
                    <div class="error"><?php echo isset($errors['password']) ? $errors['password'] : ''; ?></div>
                </div>
            </div>

            <button type="submit" class="submit-button">Login</button>
            <button type="button" class="cancel-button" onclick="window.location.href='index.php'">Cancel</button>

            <?php if (isset($errors['login'])): ?>
                <div class="error"><?php echo $errors['login']; ?></div>
            <?php endif; ?>
        </form>

        <p>Don't have an account? <a href="registration.php">Register here</a></p>
    </main>
</body>
</html>
