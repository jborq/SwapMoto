<?php
include '../database/db.php';

$id_motocykla = isset($_POST['id_motocykla']) ? $_POST['id_motocykla'] : 0;
$start_date = isset($_POST['start_date']) ? $_POST['start_date'] : '';
$end_date = isset($_POST['end_date']) ? $_POST['end_date'] : '';
$start_time = isset($_POST['start_time']) ? $_POST['start_time'] : '';
$end_time = isset($_POST['end_time']) ? $_POST['end_time'] : '';

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
    <div class="header-container">
        <div class="logo">
            <a href="../index.php">
                <img src="./images/SwapMoto.png" alt="Logo" />
            </a>
        </div>
        <div class="header-button">
            <button type="button" onclick="location.href='../public/login.php'">Login</button>
            <button type="button" onclick="location.href='../public/register.php'">Register at SwapMoto</button>
        </div>
    </div>
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