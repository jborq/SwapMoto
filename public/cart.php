<?php
include '../database/db.php';
session_start();

$base_path = '..';

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = array();
}

if (isset($_POST['remove_item'])) {
    $index = $_POST['remove_item'];
    if (isset($_SESSION['cart'][$index])) {
        unset($_SESSION['cart'][$index]);
        $_SESSION['cart'] = array_values($_SESSION['cart']);
    }
}
?>

<!DOCTYPE html>
<html lang="pl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Cart - SwapMoto</title>
    <link rel="stylesheet" href="./css/cart-style.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="./css/navbar-footer.css?v=<?php echo time(); ?>">
</head>

<body>
    <?php include '../partials/navbar.php'; ?>
    <div class="container">
        <div class="content-container">
            <h1>Continue your reservation</h1>
            <?php if (empty($_SESSION['cart'])): ?>
                <p>Your cart is empty.</p>
            <?php else: ?>
                <?php foreach ($_SESSION['cart'] as $index => $item):
                    $query = "SELECT Marka, Model, Cena, Zdjęcie FROM Motocykle WHERE IDmotocykla = ?";
                    $stmt = $conn->prepare($query);
                    $stmt->bind_param('i', $item['id_motocykla']);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $moto = $result->fetch_assoc();
                ?>
                    <div class="cart-item">
                        <div class="item-image">
                            <img src="../uploads/bikes/<?php echo htmlspecialchars($moto['Zdjęcie']); ?>" alt="Motorcycle">
                        </div>
                        <div class="item-info">
                            <h3><?php echo htmlspecialchars($moto['Marka'] . ' ' . $moto['Model']); ?></h3>
                            <p>Pickup: <?php echo htmlspecialchars($item['start_date']); ?> at <?php echo htmlspecialchars($item['start_time']); ?></p>
                            <p>Return: <?php echo htmlspecialchars($item['end_date']); ?> at <?php echo htmlspecialchars($item['end_time']); ?></p>
                            <p>Daily price: <?php echo htmlspecialchars($moto['Cena']); ?> zł</p>
                        </div>
                        <div class="item-actions">
                            <form method="post">
                                <input type="hidden" name="remove_item" value="<?php echo htmlspecialchars($index); ?>">
                                <button type="submit" class="remove-btn">Remove</button>
                            </form>
                            <form action="checkout.php" method="post">
                                <input type="hidden" name="id_motocykla" value="<?php echo htmlspecialchars($item['id_motocykla']); ?>">
                                <input type="hidden" name="start_date" value="<?php echo htmlspecialchars($item['start_date']); ?>">
                                <input type="hidden" name="end_date" value="<?php echo htmlspecialchars($item['end_date']); ?>">
                                <input type="hidden" name="start_time" value="<?php echo htmlspecialchars($item['start_time']); ?>">
                                <input type="hidden" name="end_time" value="<?php echo htmlspecialchars($item['end_time']); ?>">
                                <button type="submit">Continue Reservation</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
    <div class="footer-container">
        &copy SwapMoto 2024
    </div>
</body>

</html>