<?php
session_start(); // Start the session

// Define variables and initialize with empty values
$firstName = $lastName = $dob = $gender = $email = $hometown = $password = $confirmPassword = "";
$errors = [];

// Function to validate email
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $firstName = trim($_POST["first_name"]);
    $lastName = trim($_POST["last_name"]);
    $dob = trim($_POST["dob"]);
    $gender = trim($_POST["gender"]);
    $email = trim($_POST["email"]);
    $hometown = trim($_POST["hometown"]);
    $password = trim($_POST["password"]);
    $confirmPassword = trim($_POST["confirm_password"]);

    // Validate first name
    if (empty($firstName)) {
        $errors['first_name'] = "First Name is required.";
    } elseif (!preg_match("/^[a-zA-Z ]*$/", $firstName)) {
        $errors['first_name_validation'] = "First Name should only contain alphabets and spaces.";
    }

    // Validate last name
    if (empty($lastName)) {
        $errors['last_name'] = "Last Name is required.";
    } elseif (!preg_match("/^[a-zA-Z ]*$/", $lastName)) {
        $errors['last_name_validation'] = "Last Name should only contain alphabets and spaces.";
    }

    // Validate date of birth
    if (empty($dob)) {
        $errors['dob'] = "Date of Birth is required.";
    }

    // Validate gender
    if (empty($gender)) {
        $errors['gender'] = "Gender is required.";
    }

    // Validate email
    if (empty($email)) {
        $errors['email'] = "Email is required.";
    } elseif (!isValidEmail($email)) {
        $errors['email_validation'] = "Valid Email is required.";
    } else {
        // Check if email already exists in the file
        $file = "data/user.txt"; // File path to include "data"
        if (file_exists($file)) {
            $contents = file($file);
            foreach ($contents as $line) {
                if (strpos($line, "Email:{$email}") !== false) {
                    $errors['email_used'] = "There is already an account registered with this email.";
                    break;
                }
            }
        }
    }

    // Validate hometown
    if (empty($hometown)) {
        $errors['hometown'] = "Hometown is required.";
    }

    // Validate password
    if (empty($password)) {
        $errors['password'] = "Password is required.";
    } elseif (strlen($password) < 8 || !preg_match('/[0-9]/', $password) || !preg_match('/[\W_]/', $password)) {
        $errors['password_validation'] = "Password must be at least 8 characters long and include at least one number and one symbol.";
    }

    // Validate confirm password
    if (empty($confirmPassword)) {
        $errors['confirm_password'] = "Confirm Password is required.";
    } elseif ($password !== $confirmPassword) {
        $errors['confirm_password_validation'] = "Confirm Password must match the Password.";
    }

    // If there are no errors, save the data
    if (empty($errors)) {
        // Create data directory if it doesn't exist
        $dir = "data";
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        // Prepare the record
        $record = "First Name:{$firstName}|Last Name:{$lastName}|DOB:{$dob}|Gender:{$gender}|Email:{$email}|Hometown:{$hometown}|Password:{$password}\n";

        // Save to user.txt
        file_put_contents($file, $record, FILE_APPEND | LOCK_EX);

        // Clear any errors stored in session and redirect to the login page after successful registration
        unset($_SESSION['errors']); // Clear errors
        header("Location: login.php");
        exit;
    } else {
        // Store errors in session to display them on the form
        $_SESSION['errors'] = $errors;
        $_SESSION['post_data'] = $_POST; // Store post data to repopulate fields
    }
}

// Redirect back to the registration form if there are errors
header("Location: registration.php");
exit;
?>
