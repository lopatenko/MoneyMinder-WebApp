<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/createAccStyles.css">
    <title>Create Your Account</title>
</head>
<body>
    <div class="container">
        <h1>Create Your Account</h1>
        <form action="php/register.php" method="post">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>

            <label for="confirm_password">Confirm Password:</label>
            <input type="password" id="confirm_password" name="confirm_password" required>

            <button type="submit">Register</button>
        </form>
        <div class="separator">
            <span class="separator-line"></span>
            <span class="or">or</span>
            <span class="separator-line"></span>
        </div>
        <button class="google-button">Continue with Google</button>
        <div class="signup">
            Already have an account? <a href="login.php">Log in here</a>
        </div>
        <div class="terms">
            By creating an account you agree to the <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a>.
        </div>
    </div>
</body>
</html>
