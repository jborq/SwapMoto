<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="./css/login-style.css?v=<?php echo time(); ?>">
</head>
<body>

    <div class="logo">
        <img src=".\images\SwapMoto.png" alt="Logo" />
    </div>

    <div class="login">
        <form action="login.php" method="post">
            <h1>Sign in</h1>
            <div>
                <label for="email">Email:</label><br>
                <input type="email" id="email" name="email" required><br>
            </div>
            <div>
                <label for="password">Password:</label><br>
                <input type="password" id="password" name="password" required><br>
            </div>
            <button type="submit">Sign in</button>
            <p>Don't have an account? <a href="register.php">Sign up</a></p>
        </form>
    </div>

    <div class="footer">
        SwapMoto &copy 2024
    </div>
    
</body>
</html>