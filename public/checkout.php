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
    <title>SwapMoto - Checkout <?php echo htmlspecialchars($moto['Marka'] . ' ' . $moto['Model']); ?></title>
    <link rel="stylesheet" href="./css/checkout-style.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="./css/navbar-footer.css?v=<?php echo time(); ?>">
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
    <form action="order-summary.php" method="post">
        <div class="container">
            <div class="content-container">
                <h1>Reservation details</h1>
                <hr>
                <div class="reservation-details">
                    <div class="driver-info">
                        <h3>Driver information</h3>
                        <p>This is the information that will be used for the Rental Confirmation.</p>
                        <div class=form-group-container>
                            <div class="form-group">
                                <label for="first_name">Drivers Name *</label>
                                <input type="text" id="first_name" name="first_name" required>
                            </div>
                            <div class="form-group">
                                <label for="last_name">Drivers Surname *</label>
                                <input type="text" id="last_name" name="last_name" required>
                            </div>
                            <div class="form-group">
                                <label for="email">Email *</label>
                                <input type="email" id="email" name="email" required>
                            </div>
                            <div class="form-group">
                                <label for="phone">Phone</label>
                                <input type="tel" id="phone" name="phone" required>
                            </div>
                            <div class="form-group">
                                <label for="dob">Date of Birth *</label>
                                <input type="date" id="dob" name="dob" required>
                            </div>
                            <div class="form-group">
                                <label for="license">License category *</label>
                                <select id="license" name="license" required>
                                    <option value="A">A</option>
                                    <option value="A1">A1</option>
                                    <option value="A2">A2</option>
                                    <option value="B">B</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="payment-info">
                        <h3>Payment information</h3>
                        <div class=form-group-container>
                            <div class="form-group">
                                <label for="card_number">Card number *</label>
                                <input type="text" id="card_number" name="card_number" required>
                            </div>
                            <div class="form-group">
                                <label for="card_holder">Card holder *</label>
                                <input type="text" id="card_holder" name="card_holder" required>
                            </div>
                            <div class="form-group">
                                <label for="expiry_date">Expiry date *</label>
                                <input type="text" id="expiry_date" name="expiry_date" required>
                            </div>
                            <div class="form-group">
                                <label for="cvv">CVV *</label>
                                <input type="text" id="cvv" name="cvv" required>
                            </div>
                        </div>
                    </div>
                    <div class="optional-login">
                        <hr>
                        <h3>Managing you booking</h3>
                        <p>Having an account makes it easier to manage your booking at a later date.</p>
                        <div class="button-container">
                            <button type="button" onclick="location.href='../public/login.php'">Login</button>
                            <button type="button" onclick="location.href='../public/register.php'">Register at SwapMoto</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="confirmation-container">
                <div class="confirmation-card">
                    <div class="moto-info">
                        <h3><?php echo htmlspecialchars($moto['Marka'] . ' ' . $moto['Model']); ?></h3>
                        <p>Located in <?php echo htmlspecialchars($moto['Miasto'] . ', ' . $moto['Adres']); ?></p>
                    </div>
                    <hr>
                    <div class="pickup-container">
                        <div class=pickup-info>
                            <h4>Pickup</h4>
                            <p>Date: <span><?php echo htmlspecialchars($start_date); ?></span></p>
                            <p>Time: <span><?php echo htmlspecialchars($start_time); ?></span></p>
                        </div>
                        <div class="return-info">
                            <h4>Drop-off</h4>
                            <p>Date: <span><?php echo htmlspecialchars($end_date); ?></span></p>
                            <p>Time: <span><?php echo htmlspecialchars($end_time); ?></span></p>
                        </div>
                    </div>
                    <hr>
                    <input type="hidden" name="id_motocykla" value="<?php echo htmlspecialchars($id_motocykla); ?>">
                    <input type="hidden" name="start_date" value="<?php echo htmlspecialchars($start_date); ?>">
                    <input type="hidden" name="end_date" value="<?php echo htmlspecialchars($end_date); ?>">
                    <input type="hidden" name="start_time" value="<?php echo htmlspecialchars($start_time); ?>">
                    <input type="hidden" name="end_time" value="<?php echo htmlspecialchars($end_time); ?>">
                    <div class="price-info">
                        <p>Motorcycle rental days:<span><?php echo ($days); ?></span></p>
                        <p>Daily price: <span><?php echo htmlspecialchars($moto['Cena'] . ' zł'); ?></span></p>
                        <p class="total-price">Total price: <span><?php echo htmlspecialchars($total_price . ' zł'); ?></span></p>
                    </div>
                    <button type="submit">Pay Now</button>
                </div>
            </div>
        </div>
    </form>
    <div class="footer-container">
        &copy SwapMoto 2024
    </div>
</body>

</html>