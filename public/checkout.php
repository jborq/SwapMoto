<?php
include '../database/db.php';
session_start();

$base_path = '..';

if ($_SERVER['REQUEST_METHOD'] == 'POST' || isset($_SESSION['rental_data'])) {
    // Save rental data to session
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $id_motocykla = isset($_POST['id_motocykla']) ? $_POST['id_motocykla'] : 0;
        $start_date = isset($_POST['start_date']) ? $_POST['start_date'] : '';
        $end_date = isset($_POST['end_date']) ? $_POST['end_date'] : '';
        $start_time = isset($_POST['start_time']) ? $_POST['start_time'] : '';
        $end_time = isset($_POST['end_time']) ? $_POST['end_time'] : '';

        // Validate dates and times
        if (strtotime($start_date) === false || strtotime($end_date) === false) {
            $error_message = "Invalid date format";
        } elseif (strtotime($start_time) === false || strtotime($end_time) === false) {
            $error_message = "Invalid time format";
        } elseif (strtotime($end_date) < strtotime($start_date)) {
            $error_message = "End date cannot be earlier than start date";
        } else {
            $_SESSION['rental_data'] = [
                'id_motocykla' => $id_motocykla,
                'start_date' => $start_date,
                'end_date' => $end_date,
                'start_time' => $start_time,
                'end_time' => $end_time
            ];
        }

        $_SESSION['rental_data'] = [
            'id_motocykla' => $id_motocykla,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'start_time' => $start_time,
            'end_time' => $end_time
        ];
    } else {
        // Retrieve rental data from session
        $id_motocykla = $_SESSION['rental_data']['id_motocykla'];
        $start_date = $_SESSION['rental_data']['start_date'];
        $end_date = $_SESSION['rental_data']['end_date'];
        $start_time = $_SESSION['rental_data']['start_time'];
        $end_time = $_SESSION['rental_data']['end_time'];
    }

    // Add rental data to cart
    // Check if user is logged in
    if (isset($_SESSION['user_id'])) {
        if (!isset($_SESSION['user_carts'][$_SESSION['user_id']])) {
            $_SESSION['user_carts'][$_SESSION['user_id']] = array();
        }
        $cart = &$_SESSION['user_carts'][$_SESSION['user_id']];
    } else {
        // User is not logged in
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = array();
        }
        $cart = &$_SESSION['cart'];
    }

    // Check if item already exists in cart
    $exists = false;
    foreach ($cart as $item) {
        if (
            $item['id_motocykla'] == $id_motocykla &&
            $item['start_date'] == $start_date &&
            $item['end_date'] == $end_date &&
            $item['start_time'] == $start_time &&
            $item['end_time'] == $end_time
        ) {
            $exists = true;
            break;
        }
    }

    // Add item to cart if it doesn't exist
    if (!$exists) {
        $cart[] = $_SESSION['rental_data'];
    }
} else {
    header('Location: ../index.php');
    exit();
}

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
    <?php include '../partials/navbar.php'; ?>
    <form action="order-summary.php" method="post" id="checkoutForm" novalidate>
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
                                <input type="email"
                                    id="email"
                                    name="email"
                                    required
                                    pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$">
                                <div class="validation-message" id="emailError"></div>
                            </div>
                            <div class="form-group">
                                <label for="phone">Phone</label>
                                <input type="tel"
                                    id="phone"
                                    name="phone"
                                    pattern="\d{9}">
                                <div class="validation-message" id="phoneError"></div>
                            </div>
                            <div class="form-group">
                                <label for="dob">Date of Birth *</label>
                                <input type="date"
                                    id="dob"
                                    name="dob"
                                    required
                                    max="<?php echo date('Y-m-d', strtotime('-13 years')); ?>">
                                <div class="validation-message" id="dobError"></div>
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
                                <label for="card_number">Card Number *</label>
                                <input type="text"
                                    id="card_number"
                                    name="card_number"
                                    required
                                    pattern="\d{4}\s\d{4}\s\d{4}\s\d{4}"
                                    maxlength="19">
                                <div class="validation-message" id="cardNumberError"></div>
                            </div>
                            <div class="form-group">
                                <label for="card_holder">Card holder *</label>
                                <input type="text" id="card_holder" name="card_holder" required>
                            </div>
                            <div class="form-group">
                                <label for="expiry_date">Expiry Date (MM/YY) *</label>
                                <input type="text"
                                    id="expiry_date"
                                    name="expiry_date"
                                    required
                                    pattern="(0[1-9]|1[0-2])\/([0-9]{2})"
                                    maxlength="5">
                                <div class="validation-message" id="expiryDateError"></div>
                            </div>
                            <div class="form-group">
                                <label for="cvv">CVV *</label>
                                <input type="text"
                                    id="cvv"
                                    name="cvv"
                                    required
                                    pattern="[0-9]{3,4}"
                                    maxlength="4">
                                <div class="validation-message" id="cvvError"></div>
                            </div>
                        </div>
                    </div>
                    <?php if (!isset($_SESSION['user_id'])): ?>
                        <hr>
                        <div class="optional-login">
                            <h3>Managing you booking</h3>
                            <p>Having an account makes it easier to manage your booking at a later date.</p>
                            <div class="button-container">
                                <button type="button" onclick="location.href='../public/login.php'">Sign In</button>
                                <button type="button" onclick="location.href='../public/register.php'">Register at SwapMoto</button>
                            </div>
                        </div>
                    <?php endif; ?>
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

    <script src="../src/checkoutValidation.js"></script>

</body>

</html>