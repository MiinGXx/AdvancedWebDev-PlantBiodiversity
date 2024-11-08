<?php
session_start();
if ($_SESSION['type'] != 'admin') {
    header("Location: login.php");
    exit;
}

require 'db_connection.php';

$errors = isset($_SESSION['errors']) ? $_SESSION['errors'] : [];
$postData = isset($_SESSION['post_data']) ? $_SESSION['post_data'] : [];

unset($_SESSION['errors']);
unset($_SESSION['post_data']);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $dob = $_POST['dob'];
    $gender = $_POST['gender'];
    $hometown = $_POST['hometown'];
    $password = $_POST['password'];
    $type = $_POST['type'];

    if (empty($email)) {
        $errors['email'] = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Please provide a valid email.";
    } else {
        $email_check = $conn->prepare("SELECT email FROM user_table WHERE email = ?");
        $email_check->bind_param("s", $email);
        $email_check->execute();
        $email_check->store_result();
        if ($email_check->num_rows > 0) {
            $errors['email'] = "This email is already registered.";
        }
        $email_check->close();
    }

    if (empty($password) || strlen($password) < 8 || !preg_match('/[0-9]/', $password) || !preg_match('/[\W_]/', $password)) {
        $errors['password'] = "Password must be at least 8 characters and contain a number and a symbol.";
    } else {
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);
    }

    $profile_image = ($gender === "Male") ? "images/profile_images/default_male.jpg" : "images/profile_images/default_female.jpg";

    // Check for duplicate first name and last name
    $name_check = $conn->prepare("SELECT first_name, last_name FROM user_table WHERE first_name = ? AND last_name = ?");
    $name_check->bind_param("ss", $first_name, $last_name);
    $name_check->execute();
    $name_check->store_result();
    if ($name_check->num_rows > 0) {
        $errors['name'] = "An account with this first name and last name already exists.";
    }
    $name_check->close();

    if (empty($errors)) {
        $user_query = $conn->prepare("INSERT INTO user_table (email, first_name, last_name, dob, gender, hometown, profile_image) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $user_query->bind_param("sssssss", $email, $first_name, $last_name, $dob, $gender, $hometown, $profile_image);

        if ($user_query->execute()) {
            $account_query = $conn->prepare("INSERT INTO account_table (email, password, type) VALUES (?, ?, ?)");
            $account_query->bind_param("sss", $email, $hashed_password, $type);
            $account_query->execute();
            $account_query->close();

            header("Location: manage_accounts.php?added=1");
            exit;
        } else {
            $errors['general'] = "Error adding account. Please try again.";
        }
        $user_query->close();
    } else {
        $_SESSION['errors'] = $errors;
        $_SESSION['post_data'] = $_POST;
        header("Location: add_account.php");
        exit;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en" class="add-account">
<head>
    <title>Plant Biodiversity Portal | Add New Account</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" type="text/css" href="style/style.css">
</head>
<body>
    <main class="container">
        <h2>Add New Account</h2>
        <form method="post">
            <div class="form-row">
                <div class="form-group">
                    <label for="first_name">First Name:</label>
                    <input type="text" id="first_name" name="first_name" required>
                    <div class="error"><?php echo isset($errors['name']) ? $errors['name'] : ''; ?></div>
                </div>
                
                <div class="form-group">
                    <label for="last_name">Last Name:</label>
                    <input type="text" id="last_name" name="last_name" required>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="dob">Date of Birth:</label>
                    <input type="date" id="dob" name="dob">
                </div>

                <div class="form-group">
                    <label>Gender:</label>
                    <div class="gender-group">
                        <input type="radio" id="male" name="gender" value="Male" checked>
                        <label for="male">Male</label>
                        <input type="radio" id="female" name="gender" value="Female">
                        <label for="female">Female</label>
                    </div>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required value="<?php echo isset($postData['email']) ? htmlspecialchars($postData['email']) : ''; ?>">
                    <div class="error"><?php echo isset($errors['email']) ? $errors['email'] : ''; ?></div>
                </div>


                <div class="form-group">
                    <label for="hometown">Hometown:</label>
                    <input type="text" id="hometown" name="hometown">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" required>
                    <div class="error"><?php echo isset($errors['password']) ? $errors['password'] : ''; ?></div>
                </div>

                <div class="form-group">
                    <label for="type">Account Type:</label>
                    <select id="type" name="type" required>
                        <option value="user">User</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
            </div>
            
            <div class="button-group">
                <button type="submit" class="submit-button">Add Account</button>
                <button type="button" onclick="window.location.href='manage_accounts.php'" class="cancel-button">Cancel</button>
            </div>

            <?php if (isset($errors['general'])) : ?>
                <div class="error"><?php echo $errors['general']; ?></div>
            <?php endif; ?>
        </form>
    </main>
</body>
</html>
