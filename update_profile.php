<?php
session_start();
if (!isset($_SESSION['email'])) {
    header('Location: login.php');
    exit;
}

require 'db_connection.php';

$email = $_SESSION['email'];
$errors = [];

// Fetch user data
$query = $conn->prepare("SELECT * FROM user_table WHERE email = ?");
$query->bind_param("s", $email);
$query->execute();
$userInfo = $query->get_result()->fetch_assoc();
$query->close();

// Initialize profile image
$profileImage = $userInfo['profile_image'];

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $dob = $_POST['dob'];
    $gender = $_POST['gender'];
    $hometown = $_POST['hometown'];
    $profile_image = $userInfo['profile_image'];
    $resume = $userInfo['resume'];


    // Handle reset to default profile image
    if (isset($_POST['reset_profile_image']) && $_POST['reset_profile_image'] == 'yes') {
        // Delete the current profile image if it's not the default one
        if ($profile_image != "profile_images/boys.jpg" && $profile_image != "profile_images/girl.png" && file_exists($profile_image)) {
            unlink($profile_image);
        }
        $profile_image = ($gender == "Male") ? "profile_images/boys.jpg" : "profile_images/girl.png";
    }

    // Profile image upload handling
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == 0) {
        $image_name = $_FILES['profile_image']['name'];
        $image_tmp_name = $_FILES['profile_image']['tmp_name'];
        $image_size = $_FILES['profile_image']['size'];
        $image_ext = strtolower(pathinfo($image_name, PATHINFO_EXTENSION));

        // Check if image size is bigger than 5MB
        if ($image_size > 5 * 1024 * 1024) {
            $errors['profile_image'] = "The profile image size should not exceed 5MB.";
        } else {
            if (in_array($image_ext, ['jpg', 'jpeg', 'png'])) {
                $new_image_name = $first_name . "_" . $last_name . "_profile" . '.' . $image_ext;
                $image_upload_path = 'profile_images/' . $new_image_name;

                if ($profile_image == "profile_images/boys.jpg" || $profile_image == "profile_images/girl.png") {
                    $profile_image = $image_upload_path;
                } else {
                    // Delete the old profile image if it's not the default one
                    if (file_exists($profile_image)) {
                        unlink($profile_image);
                    }
                    $profile_image = $image_upload_path;
                }

                // Upload the new image
                if (move_uploaded_file($image_tmp_name, $image_upload_path)) {
                    $profile_image = $image_upload_path;
                } else {
                    $errors['profile_image'] = "Failed to upload image.";
                }
            } else {
                $errors['profile_image'] = "Invalid file type. Only JPG, JPEG, and PNG are allowed.";
            }
        }
    }

    // Handle Resume upload
    if (isset($_FILES['resume']) && $_FILES['resume']['error'] == 0) {
        $resume_name = $_FILES['resume']['name'];
        $resume_tmp_name = $_FILES['resume']['tmp_name'];
        $resume_size = $_FILES['resume']['size'];
        $resume_ext = strtolower(pathinfo($resume_name, PATHINFO_EXTENSION));

        // Check if resume size is bigger than 7MB
        if ($resume_size > 7 * 1024 * 1024) {
            $errors['resume'] = "The resume size should not exceed 7MB.";
        } else {
            if ($resume_ext == 'pdf') {
                $new_resume_name = $first_name . "_" . $last_name . "_resume" . '.' . $resume_ext;
                $resume_upload_path = 'resumes/' . $new_resume_name;

                // Check if the resumes folder exists, if not, create it
                if (!is_dir('resumes')) {
                    mkdir('resumes', 0777, true);
                }

                // Check if the user already has a resume
                $stmt = $conn->prepare("SELECT resume FROM user_table WHERE email = ?");
                $stmt->bind_param("s", $email);
                $stmt->execute();
                $stmt->bind_result($existing_resume);
                $stmt->fetch();
                $stmt->close();

                // If a resume exists, delete the old resume file
                if ($existing_resume && file_exists($existing_resume)) {
                    unlink($existing_resume);
                }

                // Upload the new resume
                if (move_uploaded_file($resume_tmp_name, $resume_upload_path)) {
                    $resume = $resume_upload_path;
                    $stmt = $conn->prepare("UPDATE user_table SET resume = ? WHERE email = ?");
                    $stmt->bind_param("ss", $resume, $email);
                    $stmt->execute();
                    $stmt->close();
                } else {
                    $errors['resume'] = "Failed to upload resume.";
                }
            } else {
                $errors['resume'] = "Invalid file type. Only PDF files are allowed.";
            }
        }
    }

    // Handle Contact Number
    $contact_number = $_POST['contact_number'];
    if (!preg_match("/^[0-9]{10,11}$/", $contact_number)) {
        $errors['contact_number'] = "Contact number must be a valid 10 or 11-digit number.";
    }

    // Handle password change
    $old_password = $_POST['old_password'];
    $new_password = $_POST['new_password'];

    if (!empty($old_password) && !empty($new_password)) {
        // Check if the new password meets the criteria
        if (preg_match('/^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/', $new_password)) {
            $password_check = $conn->prepare("SELECT password FROM account_table WHERE email = ?");
            $password_check->bind_param("s", $email);
            $password_check->execute();
            $password_hash = $password_check->get_result()->fetch_assoc()['password'];
            $password_check->close();
    
            if (password_verify($old_password, $password_hash)) {
                $new_password_hash = password_hash($new_password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("UPDATE account_table SET password = ? WHERE email = ?");
                $stmt->bind_param("ss", $new_password_hash, $email);
                $stmt->execute();
                $stmt->close();
            } else {
                $errors['old_password'] = "Incorrect old password.";
            }
        } else {
            $errors['new_password'] = "New password must be at least 8 characters long and include alphabets, numbers, and special characters.";
        }
    }

    // Update user data in the database
    if (empty($errors)) {
        $stmt = $conn->prepare("UPDATE user_table SET first_name = ?, last_name = ?, dob = ?, gender = ?, hometown = ?, profile_image = ?, contact_number = ? WHERE email = ?");
        $stmt->bind_param("ssssssss", $first_name, $last_name, $dob, $gender, $hometown, $profile_image, $contact_number, $email);
        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Profile updated successfully.";
            header('Location: main_menu.php');
        } else {
            $errors['database'] = "Failed to update profile. Please try again later.";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en" class="update-profile">
<head>
    <title>Update Profile</title>
    <link rel="stylesheet" type="text/css" href="css/style.css">
</head>
<body>
<main class="update-profile">
        <section>
            <h1>Update Profile</h1>
            <div class="profile-card">
                <img src="<?php echo htmlspecialchars($profileImage); ?>" alt="Profile Image">
                <form method="post" enctype="multipart/form-data">
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
                            <input type="text" id="email" name="email" value="<?php echo htmlspecialchars($userInfo['email']); ?>" disabled>
                            <div class="error"><?php echo isset($errors['email']) ? $errors['email'] : ''; ?></div>
                        </div>
                        <div>
                            <label for="hometown">Hometown:</label>
                            <input type="text" id="hometown" name="hometown" value="<?php echo htmlspecialchars($userInfo['hometown']); ?>">
                        </div>
                    </div>

                    <!-- Profile Image and Resume -->
                    <div class="form-group">
                        <div>
                            <label for="profile_image">Profile Image (< 5Mb):</label>
                            <input type="file" id="profile_image" name="profile_image">
                            <div class="error"><?php echo isset($errors['profile_image']) ? $errors['profile_image'] : ''; ?></div>
                            <div>
                                <input type="checkbox" id="reset_profile_image" name="reset_profile_image" value="yes">
                                <label for="reset_profile_image">Reset Profile Image</label>
                            </div>
                        </div>

                        <div>
                            <label for="resume">Resume (PDF) (< 7Mb):</label>
                            <input type="file" id="resume" name="resume" accept=".pdf">
                            <div class="error"><?php echo isset($errors['resume']) ? $errors['resume'] : ''; ?></div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div>
                            <label for="contact_number">Contact Number:</label>
                            <input type="text" id="contact_number" name="contact_number" value="<?php echo htmlspecialchars($userInfo['contact_number']); ?>">
                            <div class="error"><?php echo isset($errors['contact_number']) ? $errors['contact_number'] : ''; ?></div>
                        </div>
                    </div>

                    <br>
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