<?php
include '../database/db.php';
session_start();

$base_path = '..';

$id_motocykla = isset($_POST['id_motocykla']) ? $_POST['id_motocykla'] : 0;
$start_date = isset($_POST['start_date']) ? $_POST['start_date'] : '';
$end_date = isset($_POST['end_date']) ? $_POST['end_date'] : '';
$start_time = isset($_POST['start_time']) ? $_POST['start_time'] : '';
$end_time = isset($_POST['end_time']) ? $_POST['end_time'] : '';

$first_name = htmlspecialchars(trim($_POST['first_name']));
$last_name = htmlspecialchars(trim($_POST['last_name']));
$email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
$phone = htmlspecialchars(trim($_POST['phone']));
$dob = $_POST['dob'];
$license = $_POST['license'];
$card_number = htmlspecialchars(trim($_POST['card_number']));
$card_holder = htmlspecialchars(trim($_POST['card_holder']));
$expiry_date = htmlspecialchars(trim($_POST['expiry_date']));
$cvv = htmlspecialchars(trim($_POST['cvv']));

// Valdation checks
$errors = [];

if (empty($first_name) || empty($last_name)) {
    $errors[] = "First name and last name are required.";
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Invalid email format.";
}

if (!empty($phone) && !preg_match('/^\d{9}$/', $phone)) {
    $errors[] = "Invalid phone number format.";
}

$dob_date = DateTime::createFromFormat('Y-m-d', $dob);
if (!$dob_date || $dob_date > new DateTime('-13 years')) {
    $errors[] = "You must be at least 13 years old.";
}

if (!in_array($license, ['A', 'A1', 'A2', 'B'])) {
    $errors[] = "Invalid license category.";
}

if (!preg_match('/^\d{16}$/', str_replace(' ', '', $card_number))) {
    $errors[] = "Invalid card number format.";
}

if (empty($card_holder)) {
    $errors[] = "Card holder name is required.";
}

$expiry_parts = explode('/', $expiry_date);
if (count($expiry_parts) !== 2 || !checkdate($expiry_parts[0], 1, 2000 + $expiry_parts[1])) {
    $errors[] = "Invalid expiry date format.";
}

if (!preg_match('/^\d{3,4}$/', $cvv)) {
    $errors[] = "Invalid CVV format.";
}

if (!empty($errors)) {
    foreach ($errors as $error) {
        echo "<p>$error</p>";
    }
    exit();
}

// Remove item from cart
if (isset($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $key => $item) {
        if (
            $item['id_motocykla'] == $id_motocykla &&
            $item['start_date'] == $start_date &&
            $item['end_date'] == $end_date &&
            $item['start_time'] == $start_time &&
            $item['end_time'] == $end_time
        ) {
            unset($_SESSION['cart'][$key]);
            $_SESSION['cart'] = array_values($_SESSION['cart']);
            break;
        }
    }
}

unset($_SESSION['rental_data']);

$query = "SELECT Motocykle.Marka, Motocykle.Model, Motocykle.Cena, Lokalizacje.Miasto, Lokalizacje.Adres
          FROM Motocykle 
          JOIN Lokalizacje ON Motocykle.IDlokalizacji = Lokalizacje.IDlokalizacji 
          WHERE IDmotocykla = ?";

$stmt = $conn->prepare($query);
$stmt->bind_param('i', $id_motocykla);
$stmt->execute();
$result = $stmt->get_result();
$moto = $result->fetch_assoc();

$total_price = 0;
if ($start_date && $end_date) {
    $start = new DateTime($start_date);
    $end = new DateTime($end_date);
    $interval = $start->diff($end);
    $days = $interval->days + 1;
    $total_price = $days * $moto['Cena'];
}

$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 2;

$query = "INSERT INTO Rezerwacje (IDużytkownika, IDmotocykla, Status_rezerwacji, 
          Data_rozpoczęcia, Data_zakończenia, Godzina_odbioru, Godzina_oddania, 
          Imię_kierowcy, Nazwisko_kierowcy, Email_kierowcy, Telefon_kierowcy, 
          Data_urodzenia_kierowcy, Kategoria_prawa_jazdy, Całkowita_cena) 
          VALUES (?, ?, 'trwa', ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($query);
$start_datetime = $start_date . ' ' . $start_time;
$end_datetime = $end_date . ' ' . $end_time;

$stmt->bind_param(
    'iissssssssssd',
    $user_id,
    $id_motocykla,
    $start_datetime,
    $end_datetime,
    $start_time,
    $end_time,
    $first_name,
    $last_name,
    $email,
    $phone,
    $dob,
    $license,
    $total_price
);
$stmt->execute();

?>

<!DOCTYPE html>
<html lang="pl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Summary - SwapMoto</title>
    <link rel="stylesheet" href="./css/checkout-style.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="./css/navbar-footer.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="./css/order-summary-style.css?v=<?php echo time(); ?>">
</head>

<body>
    <?php include '../partials/navbar.php'; ?>
    <div class="container">
        <div class="content-container">
            <div class="order-summary-header">
                <h1>Order Summary</h1>
                <p>Your order has been successfully placed!</p>
            </div>
            <hr>
            <div class="order-details">
                <h3>Motorcycle Details</h3>
                <p>Motorcycle: <?php echo htmlspecialchars($moto['Marka'] . ' ' . $moto['Model']); ?></p>
                <p>Location: <?php echo htmlspecialchars($moto['Miasto'] . ', ' . $moto['Adres']); ?></p>
                <hr>
                <h3>Pickup Details</h3>
                <p>Date: <?php echo htmlspecialchars($start_date); ?></p>
                <p>Time: <?php echo htmlspecialchars($start_time); ?></p>
                <h3>Drop-off Details</h3>
                <p>Date: <?php echo htmlspecialchars($end_date); ?></p>
                <p>Time: <?php echo htmlspecialchars($end_time); ?></p>
                <hr>
                <h3>Price Details</h3>
                <p>Motorcycle rental days: <?php echo ($days); ?></p>
                <p>Daily price: <?php echo htmlspecialchars($moto['Cena'] . ' zł'); ?></p>
                <p class="total-price">Total price: <?php echo htmlspecialchars($total_price . ' zł'); ?></p>
            </div>
            <div class="order-summary-footer">
                <button type="button" onclick="location.href='../index.php'">Back to Homepage</button>
            </div>
        </div>
    </div>
    <div class="footer-container">
        &copy SwapMoto 2024
    </div>
</body>

</html>