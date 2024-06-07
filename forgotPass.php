<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Find your account</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #455E4B;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .container {
            max-width: 400px; /* Increased max-width */
            margin: 20px; /* Increased margin */
            background-color: #F1F6F4;
            padding: 30px; /* Increased padding */
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            position: relative;
            overflow: hidden;
        }
        .back-btn {
            position: absolute;
            left: 2%;
            top: 10%; /* Adjusted top value */
            transform: translateY(-50%);
            width: 50px;
            height: 50px;
            cursor: pointer;
            transition: transform 0.3s ease;
        }
        .back-btn:hover {
            transform: translateY(-50%) scale(1.1);
            background-color: #F1F6F4;
        }
        .back-btn svg {
            width: 100%;
            height: 100%;
        }
        .back-btn svg path {
            fill: #666;
            transition: fill 0.3s ease;
        }
        .back-btn:hover svg path {
            fill: #333;
        }
        .back-btn::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: #424a3f;
            opacity: 0;
            transition: opacity 0.3s ease;
            animation: glow 2s infinite;
            border-radius: 10px;
        }
        .back-btn:hover::before {
            opacity: 0.2;
        }
        @keyframes glow {
            0% {
                box-shadow: 0 0 5px #60705D, 0 0 10px #60705D, 0 0 15px #60705D, 0 0 20px #60705D;
            }
            100% {
                box-shadow: 0 0 10px #60705D, 0 0 20px #60705D, 0 0 30px #60705D, 0 0 40px #60705D;
            }
        }
        h1 {
            text-align: center;
            color: #3e3d3d;
            margin-bottom: 30px; /* Increased margin */
        }
        input[type="email"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }
        button {
            width: 100%;
            padding: 10px;
            margin: 10px 0 -5px; /* Increased margin */
            border: none;
            background-color: #302C2A;
            color: #fff;
            border-radius: 10px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            font-weight: 700;
            font-size: 17px;
        }
        button:hover {
            background-color: #3c78dc;
        }
        .separator {
            margin: 30px 0; /* Increased margin */
            text-align: center;
            position: relative;
        }
        .separator::before {
            content: "";
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            border-top: 1px solid #ccc;
        }
        .links {
            text-align: center;
            margin-top: 30px; /* Increased margin */
            padding-top: 30px; /* Increased padding */
            border-top: 1px solid #ccc;
        }
        .links a {
            color: #4285f4;
            text-decoration: none;
            margin: 0 10px;
            transition: color 0.3s ease;
        }
        .links a:hover {
            color: #3c78dc;
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="login.php" class="back-btn">
            <svg viewBox="0 0 24 24">
                <path d="M15.41 7.41L14 6l-6 6 6 6 1.41-1.41L10.83 12z"/>
            </svg>
        </a>
        <h1>Find your account</h1>
        <form action="#">
            <label for="email">Please enter your email to search for your account.</label>
            <input type="email" id="email" name="email" placeholder="email@address.com" required>
            <button type="submit">Reset Password</button>
        </form>
        <div class="separator"></div>
        <div class="links">
            <a href="#">Terms of Service</a>
            <a href="#">Privacy Policy</a>
            <a href="createAcc.php">Create Account</a>
        </div>
    </div>
</body>
</html>