<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/loginStyles.css">
    <title>Sign In</title>
</head>
<body>
<div class="container">
    <h1>Sign in</h1>
    <form action="php/authenticate.php" method="post">
        <label for="email">Email address</label>
        <input type="email" id="email" name="email" placeholder="email@address.com" required>
        <label for="password">Password</label>
        <input type="password" id="password" name="password" placeholder="********" required>
        <a href="forgotPass.php">Forgot password?</a>
        <button type="submit">Log In</button>
        <label>
            <input type="checkbox" name="remember"> Remember me
        </label>
    </form>
    <div class="separator">
        <span class="separator-line"></span>
        <span class="or">or</span>
        <span class="separator-line"></span>
    </div>
    <button class="google-button">Sign in with Google</button>
    <div class="signup">
        Don't have an account? <a href="createAcc.php">Sign up here</a>
    </div>
</div>
</body>
</html>