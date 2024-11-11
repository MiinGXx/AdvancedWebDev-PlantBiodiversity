<?php
// Include the database connection
require 'db_connection.php';
session_start();

// Initialize variables
$firstName = $lastName = $dob = $gender = $email = $hometown = $password = $confirmPassword = "";
$errors = [];

// Function to validate email format
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Capture form data
    $email = $_POST['email'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $dob = $_POST['dob'];
    $gender = $_POST['gender'];
    $hometown = $_POST['hometown'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Set default profile image based on gender
    $profile_image = ($gender == "Male") ? "images/profile_images/boys.jpg" : "images/profile_images/girl.png";
    $contact_number = "";

    // Validate first name
    if (empty($first_name)) {
        $errors['first_name'] = "First Name is required.";
    } elseif (!preg_match('/^[a-zA-Z ]+$/', $first_name)) {
        $errors['first_name_validation'] = "First Name must contain only letters and spaces.";
    }

    // Validate last name
    if (empty($last_name)) {
        $errors['last_name'] = "Last Name is required.";
    } elseif (!preg_match('/^[a-zA-Z ]+$/', $last_name)) {
        $errors['last_name_validation'] = "Last Name must contain only letters and spaces.";
    }

    // Validate and check for duplicate first and last names
    $result = $conn->query("SELECT first_name, last_name FROM user_table WHERE first_name = '$first_name' AND last_name = '$last_name'");
    if ($result->num_rows > 0) {
        $errors['name_used'] = "A user with this First Name and Last Name already exists.";
    }

    // Validate date of birth
    if (empty($dob)) {
        $errors['dob'] = "Date of Birth is required.";
    }

    // Validate gender
    if (empty($gender)) {
        $errors['gender'] = "Gender is required.";
    }

    // Validate hometown
    if (empty($hometown)) {
        $errors['hometown'] = "Hometown is required.";
    }

    // Validate email and check for duplicate
    if (empty($email)) {
        $errors['email'] = "Email is required.";
    } elseif (!isValidEmail($email)) {
        $errors['email_validation'] = "Please provide a valid email.";
    } else {
        // Check if email already exists in the database
        $email_check = $conn->query("SELECT email FROM user_table WHERE email = '$email'");
        if ($email_check->num_rows > 0) {
            $errors['email_used'] = "This email is already registered.";
        }
    }

    // Validate password
    if (empty($password)) {
        $errors['password'] = "Password is required.";
    } elseif (strlen($password) < 8 || !preg_match('/[0-9]/', $password) || !preg_match('/[\W_]/', $password)) {
        $errors['password_validation'] = "Password must be at least 8 characters, with one number and one symbol.";
    }

    // Confirm passwords match
    if (empty($confirm_password)) {
        $errors['confirm_password'] = "Please confirm your password.";
    } elseif ($password != $confirm_password) {
        $errors['password_match'] = "Passwords do not match.";
    }

    // If there are errors, store in session and redirect back to registration
    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        $_SESSION['post_data'] = $_POST; // Store form data to repopulate fields
        header("Location: registration.php");
        exit;
    } else {
        // If no errors, proceed to insert data into user_table and account_table
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        // Prepare and bind user data insertion
        $user_query = $conn->prepare("INSERT INTO user_table (email, first_name, last_name, dob, gender, contact_number, hometown, profile_image) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $user_query->bind_param("ssssssss", $email, $first_name, $last_name, $dob, $gender, $contact_number, $hometown, $profile_image);

        if ($user_query->execute()) {
            // Insert into account_table
            $account_query = $conn->prepare("INSERT INTO account_table (email, password, type) VALUES (?, ?, 'user')");
            $account_query->bind_param("ss", $email, $hashed_password);
            
            if ($account_query->execute()) {
                // Redirect to login page after successful registration
                header("Location: login.php");
                exit;
            } else {
                echo "Error: Could not create account. Please try again.";
            }
            $account_query->close();
        } else {
            echo "Error: Could not create user profile. Please try again.";
        }
        $user_query->close();
    }
}

// Close database connection
$conn->close();
?>
