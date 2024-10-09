<?php
session_start(); // Start the session

// Get errors from session if available
$errors = isset($_SESSION['errors']) ? $_SESSION['errors'] : [];
$postData = isset($_SESSION['post_data']) ? $_SESSION['post_data'] : [];

// Clear session data after using it
unset($_SESSION['errors']);
unset($_SESSION['post_data']);
?>

<!DOCTYPE html>
<html lang="en" class="registration">
<head>
    <title>Plant Biodiversity Portal | Register</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>

<body>
    <main class="container">
        <h1>Register for a New Account</h1>
        <form action="process_register.php" method="POST">
            <!-- First Name and Last Name -->
            <div class="form-group">
                <div>
                    <label for="first_name">First Name:</label>
                    <input type="text" id="first_name" name="first_name" size="25" value="<?php echo isset($postData['first_name']) ? htmlspecialchars($postData['first_name']) : ''; ?>">
                    <div class="error"><?php echo isset($errors['first_name']) ? $errors['first_name'] : ''; ?></div>
                    <div class="error"><?php echo isset($errors['first_name_validation']) ? $errors['first_name_validation'] : ''; ?></div>
                </div>
                <div>
                    <label for="last_name">Last Name:</label>
                    <input type="text" id="last_name" name="last_name" size="25" value="<?php echo isset($postData['last_name']) ? htmlspecialchars($postData['last_name']) : ''; ?>">
                    <div class="error"><?php echo isset($errors['last_name']) ? $errors['last_name'] : ''; ?></div>
                    <div class="error"><?php echo isset($errors['last_name_validation']) ? $errors['last_name_validation'] : ''; ?></div>
                </div>
            </div>

            <!-- Date of Birth and Gender -->
            <div class="form-group">
                <div>
                    <label for="dob">Date of Birth:</label>
                    <input type="date" id="dob" name="dob" value="<?php echo isset($postData['dob']) ? htmlspecialchars($postData['dob']) : ''; ?>">
                    <div class="error"><?php echo isset($errors['dob']) ? $errors['dob'] : ''; ?></div>
                </div>
                <div>
                    <label>Gender:</label>
                    <div class="gender-group">
                        <input type="radio" id="male" name="gender" value="male" <?php echo (isset($postData['gender']) && $postData['gender'] == 'male') ? 'checked' : ''; ?>>
                        <label for="male">Male</label>
                        
                        <input type="radio" id="female" name="gender" value="female" <?php echo (isset($postData['gender']) && $postData['gender'] == 'female') ? 'checked' : ''; ?>>
                        <label for="female">Female</label>
                    </div>
                    <div class="error"><?php echo isset($errors['gender']) ? $errors['gender'] : ''; ?></div>
                </div>
            </div>

            <!-- Email and Hometown -->
            <div class="form-group">
                <div>
                    <label for="email">Email:</label>
                    <input type="text" id="email" name="email" value="<?php echo isset($postData['email']) ? htmlspecialchars($postData['email']) : ''; ?>">
                    <div class="error"><?php echo isset($errors['email']) ? $errors['email'] : ''; ?></div>
                    <div class="error"><?php echo isset($errors['email_validation']) ? $errors['email_validation'] : ''; ?></div>
                </div>
                <div>
                    <label for="hometown">Hometown:</label>
                    <input type="text" id="hometown" name="hometown" size="25" value="<?php echo isset($postData['hometown']) ? htmlspecialchars($postData['hometown']) : ''; ?>">
                    <div class="error"><?php echo isset($errors['hometown']) ? $errors['hometown'] : ''; ?></div>
                </div>
            </div>

            <!-- Password and Confirm Password -->
            <div class="form-group">
                <div>
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password">
                    <div class="error"><?php echo isset($errors['password']) ? $errors['password'] : ''; ?></div>
                    <div class="error"><?php echo isset($errors['password_validation']) ? $errors['password_validation'] : ''; ?></div>
                </div>
                <div>
                    <label for="confirm_password">Confirm Password:</label>
                    <input type="password" id="confirm_password" name="confirm_password">
                    <div class="error"><?php echo isset($errors['confirm_password']) ? $errors['confirm_password'] : ''; ?></div>
                    <div class="error"><?php echo isset($errors['confirm_password_validation']) ? $errors['confirm_password_validation'] : ''; ?></div>
                </div>
            </div>

            <!-- Submit and Reset Buttons -->
            <div class="button-group">
                <button type="submit" class="submit-button">Register</button>
                <button type="reset" class="reset-button">Reset</button>
            </div>

            <!-- Display email duplicate error at the bottom -->
            <?php if (isset($errors['email_used'])): ?>
            <div class="error-email">
                <strong><?php echo $errors['email_used']; ?></strong>
            </div>
            <?php endif; ?>
        </form>
        <p>Already have an account? <a href="login.php">Login here</a></p>
    </main>
</body>
</html>
