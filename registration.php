<!DOCTYPE html>
<html lang="en">
<head>
    <title>Plant Biodiversity Portal | Register</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>

<body>
    <h1>Register for a New Account</h1>
    <form action="submit_registration.php" method="POST">
        <p>
            <label for="first_name">First Name:</label>
            <input type="text" id="first_name" name="first_name" required>

            <label for="last_name">Last Name:</label>
            <input type="text" id="last_name" name="last_name" required>
        </p>

        <p>
        <label for="dob">Date of Birth:</label>
            <input type="date" id="dob" name="dob" required>

            <label for="gender">Gender:</label>
            <select id="gender" name="gender">
                <option value="female" selected>Female</option>
                <option value="male">Male</option>
            </select>
        </p>
        
        <p>
            <label for="email">Email:</label>
            <input type="text" id="email" name="email" required>

            <label for="hometown">Hometown:</label>
            <input type="text" id="hometown" name="hometown" required>
        </p>

        <p>
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>

            <label for="confirm_password">Confirm Password:</label>
            <input type="password" id="confirm_password" name="confirm_password" required>
        </p>
        
        <button type="submit">Register</button>
        <button type="reset">Reset</button>
    </form>
</body>
</html>
