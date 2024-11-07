<?php
// Start the session
session_start();

// Include the database connection
require 'db_connection.php';

// Initialize variables for error messages
$errors = [];
$postData = [];

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

    // If no input errors, proceed to check credentials
    if (empty($errors)) {
        // Query to fetch user credentials
        $stmt = $conn->prepare("SELECT email, password, type FROM account_table WHERE email = ?");
        $stmt->bind_param("s", $postData['email']);
        $stmt->execute();
        $result = $stmt->get_result();

        // Check if user exists
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            // Verify password
            if (password_verify($postData['password'], $user['password'])) {
                // Set session variables
                $_SESSION['email'] = $user['email'];
                $_SESSION['type'] = $user['type'];
                
                // Redirect based on user type
                if ($user['type'] == 'admin') {
                    header("Location: main_menu_admin.php");
                } else {
                    header("Location: main_menu.php");
                }
                exit;
            } else {
                $errors['login'] = "Incorrect password. Please try again.";
            }
        } else {
            $errors['login'] = "No account found with this email.";
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
