<?php
include '../database/db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $redirect_url = isset($_POST['redirect_url']) ? $_POST['redirect_url'] : '../index.php';

    
    $query = "SELECT IDużytkownika, Hasło FROM Użytkownicy WHERE Email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if ($password == $user['Hasło']) {
            $_SESSION['user_id'] = $user['IDużytkownika'];
            header('Location: ' . $redirect_url);
            exit();
        } else {
            $error_message = "Invalid email or password.";
        }
    } else {
        $error_message = "Invalid email or password.";
    }
}
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign in</title>
    <link rel="stylesheet" href="./css/login-style.css?v=<?php echo time(); ?>">
</head>

<body>

    <div class="logo">
        <a href="../index.php">
            <img src=".\images\SwapMoto.png" alt="Logo" />
        </a>
    </div>

    <div class="login">
        <form action="./login.php" method="post">
            <h1>Sign in</h1>
            <?php if (isset($error_message)): ?>
                <p class="error"><?php echo $error_message; ?></p>
            <?php endif; ?>
            <input type="hidden" name="redirect_url" value="<?php echo htmlspecialchars($_SERVER['HTTP_REFERER']); ?>">
            <div>
                <label for="email">Email:</label><br>
                <input type="email" id="email" name="email" required><br>
            </div>
            <div>
                <label for="password">Password:</label><br>
                <input type="password" id="password" name="password" required><br>
            </div>
            <button type="submit">Sign in</button>
            <p>Don't have an account? <a href="./register.php">Sign up</a></p>
        </form>
    </div>

    <div class="footer">
        SwapMoto &copy 2024
    </div>

</body>

</html>