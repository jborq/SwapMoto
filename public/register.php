<?php
include '../database/db.php';
session_start();

$error_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize input data
    $original_email = $_POST['email'];
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $firstName = htmlspecialchars(trim($_POST['first_name']));
    $lastName = htmlspecialchars(trim($_POST['last_name']));
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validate email
    if ($original_email != $email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Invalid email format";
    }
    // Check password strength
    elseif (strlen($password) < 8) {
        $error_message = "Password must be at least 8 characters long";
    }
    // Verify passwords match
    elseif ($password !== $confirm_password) {
        $error_message = "Passwords do not match";
    }
    else {
        // Check if email already exists
        $query = "SELECT IDużytkownika FROM Użytkownicy WHERE Email = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $error_message = "Email already registered";
        } else {
            // Hash password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            try {
                $conn->begin_transaction();

                // Insert new user
                $stmt = $conn->prepare("INSERT INTO Użytkownicy (IDroli, Imię, Nazwisko, Email, Hasło) VALUES (?, ?, ?, ?, ?)");
                $role_id = 1; // Basic user role
                $stmt->bind_param('issss', $role_id, $firstName, $lastName, $email, $hashed_password);
                $stmt->execute();

                $conn->commit();
                
                // Redirect to login page
                header('Location: login.php?registration=success');
                exit();
            } catch (Exception $e) {
                $conn->rollback();
                $error_message = "Registration failed. Please try again.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign up</title>
    <link rel="stylesheet" href="./css/login-style.css?v=<?php echo time(); ?>">
</head>
<body>
    <div class="logo">
        <a href="../index.php">
            <img src="./images/SwapMoto.png" alt="Logo" />
        </a>
    </div>

    <div class="login">
        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
            <h1>Sign up</h1>
            <?php if ($error_message): ?>
                <p class="error"><?php echo $error_message; ?></p>
            <?php endif; ?>
            <div>
                <label for="first_name">First Name:</label><br>
                <input type="text" id="first_name" name="first_name" required><br>
            </div>
            <div>
                <label for="last_name">Last Name:</label><br>
                <input type="text" id="last_name" name="last_name" required><br>
            </div>
            <div>
                <label for="email">Email:</label><br>
                <input type="email" id="email" name="email" required><br>
            </div>
            <div>
                <label for="password">Password:</label><br>
                <input type="password" id="password" name="password" 
                       pattern="(?=.*[a-z])(?=.*[A-Z])(?=.*[!@#$%^&*]).{8,}" 
                       title="Password must be at least 8 characters long and contain uppercase, lowercase and special characters"
                       required><br>
            </div>
            <div>
                <label for="confirm_password">Confirm password:</label><br>
                <input type="password" id="confirm_password" name="confirm_password" required><br>
            </div>
            <button type="submit">Sign up</button>
            <p>Already have an account? <a href="./login.php">Sign in</a></p>
        </form>
    </div>

    <div class="footer">
        SwapMoto &copy 2024
    </div>
</body>
</html>