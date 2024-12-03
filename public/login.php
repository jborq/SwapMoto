<?php
include '../database/db.php';
session_start();

$error_message = '';
$success_message = '';

if (isset($_GET['registration']) && $_GET['registration'] === 'success') {
    $success_message = "Registration successful! Please log in.";
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $original_email = $_POST['email'];
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];
    $redirect_url = isset($_POST['redirect_url']) ? $_POST['redirect_url'] : '../index.php';

    // Validate email
    if ($original_email != $email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Invalid email format";
    } else {
        $query = "SELECT IDużytkownika, Hasło FROM Użytkownicy WHERE Email = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['Hasło'])) {
                $_SESSION['user_id'] = $user['IDużytkownika'];

                // Restore user cart or create new one     
                if (!isset($_SESSION['user_carts'])) {
                    $_SESSION['user_carts'] = array();
                }
                if (!isset($_SESSION['user_carts'][$user['IDużytkownika']])) {
                    $_SESSION['user_carts'][$user['IDużytkownika']] = array();
                }

                // Move cart items to user cart
                if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
                    foreach ($_SESSION['cart'] as $item) {
                        $_SESSION['user_carts'][$user['IDużytkownika']][] = $item;
                    }
                    unset($_SESSION['cart']);
                }

                // Set user cart as current cart
                $_SESSION['cart'] = &$_SESSION['user_carts'][$user['IDużytkownika']];

                $redirect_url = filter_var($redirect_url, FILTER_SANITIZE_URL);
                if (filter_var($redirect_url, FILTER_VALIDATE_URL)) {
                    header('Location: ' . $redirect_url);
                } else {
                    header('Location: ../index.php');
                }
                exit();
            } else {
                $error_message = "Invalid email or password";
            }
        } else {
            $error_message = "Invalid email or password";
        }
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
            <img src="./images/SwapMoto.png" alt="Logo" />
        </a>
    </div>

    <div class="login">
        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post" id="loginForm" novalidate>
            <h1>Sign in</h1>

            <?php if ($error_message): ?>
                <p class="error"><?php echo htmlspecialchars($error_message); ?></p>
            <?php endif; ?>
            <?php if ($success_message): ?>
                <p class="success"><?php echo htmlspecialchars($success_message); ?></p>
            <?php endif; ?>
            <input type="hidden" name="redirect_url" value="<?php echo htmlspecialchars($_SERVER['HTTP_REFERER'] ?? '../index.php'); ?>">

            <div class="input-group">
                <label for="email">Email:</label>
                <input type="email"
                    id="email"
                    name="email"
                    required
                    pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$">
                <div class="validation-message" id="emailError"></div>
            </div>

            <div class="input-group">
                <label for="password">Password:</label>
                <input type="password"
                    id="password"
                    name="password"
                    required
                    minlength="8">
                <div class="validation-message" id="passwordError"></div>
            </div>

            <button type="submit">Sign in</button>
            <p>Don't have an account? <a href="./register.php">Sign up</a></p>
        </form>
    </div>

    <div class="footer">
        SwapMoto &copy 2024
    </div>

    <script src="../src/loginValidation.js"></script>

</body>

</html>