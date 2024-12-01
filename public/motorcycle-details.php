<?php
include '../database/db.php';
session_start();

$base_path = '..';

$id = isset($_GET['id']) ? $_GET['id'] : 0;

$query = "SELECT Motocykle.Marka, Motocykle.Model, Motocykle.Cena, Motocykle.Pojemność_silnika, Motocykle.Rok_produkcji, Motocykle.Kategoria_prawa_jazdy, Motocykle.Zdjęcie, Lokalizacje.Miasto, Lokalizacje.Kod_pocztowy, Lokalizacje.Adres, Lokalizacje.Numer_kontaktowy, Lokalizacje.Godzina_otwarcia, Lokalizacje.Godzina_zamknięcia
          FROM Motocykle 
          JOIN Lokalizacje ON Motocykle.IDlokalizacji = Lokalizacje.IDlokalizacji 
          WHERE Motocykle.IDmotocykla = ?";

$stmt = $conn->prepare($query);
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();
$moto = $result->fetch_assoc();

$reviewsQuery = "SELECT Imię, Ocena, Komentarz, Data_dodania FROM Opinie WHERE IDmotocykla = ?";
$reviewsStmt = $conn->prepare($reviewsQuery);
$reviewsStmt->bind_param('i', $id);
$reviewsStmt->execute();
$reviewsResult = $reviewsStmt->get_result();
$reviews = [];
if ($reviewsResult->num_rows > 0) {
    while ($row = $reviewsResult->fetch_assoc()) {
        $reviews[] = $row;
    }
}

function formatPhoneNumber($phoneNumber)
{
    return preg_replace('/(\d{3})(\d{3})(\d{3})/', '$1 $2 $3', $phoneNumber);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($moto['Marka'] . ' ' . $moto['Model']); ?> - SwapMoto</title>
    <link rel="stylesheet" href="./css/motorcycle-details.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="./css/navbar-footer.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body>
    <?php include '../partials/navbar.php'; ?>
    <div class="content-container">
        <div class="content">
            <div class="moto-container">
                <img src="<?php echo '../uploads/bikes/' . $moto['Zdjęcie']; ?>" alt="<?php echo $moto['Marka'] . ' ' . $moto['Model']; ?>">
                <h1><?php echo $moto['Marka'] . ' ' . $moto['Model']; ?></h1>
                <div class="moto-info">
                    <div class="moto-deatils">
                        <img src="./icons/motorbike.png" alt="Motorcycle Icon" />
                        <p><?php echo intval($moto['Pojemność_silnika']); ?>cc</p>
                    </div>
                    <div class="moto-deatils">
                        <img src="./icons/calendar.png" alt="Calendar Icon" />
                        <p><?php echo $moto['Rok_produkcji']; ?></p>
                    </div>
                    <div class="moto-deatils">
                        <img src="./icons/driving_license.png" alt="Driving License Icon" />
                        <p><?php echo $moto['Kategoria_prawa_jazdy']; ?></p>
                    </div>
                </div>
            </div>
            <div class="insurance-container">
                <h3>Included in the Price</h3>
                <div class="insurance-info">
                    <div class="insurance-details-left">
                        <div class="insurance-details">
                            <img src="./icons/breakdown_assistance.png" alt="Breakdown Assistance" />
                            <p>Breakdown Assistance</p>
                        </div>
                        <div class="insurance-details">
                            <img src="./icons/accident_insurance.png" alt="Accident Insurance" />
                            <p>Accident Insurance</p>
                        </div>
                    </div>
                    <div class="insurance-details-right">
                        <div class="insurance-details">
                            <img src="./icons/theft_insurance.png" alt="Theft Insurance" />
                            <p>Theft Insurance</p>
                        </div>
                        <div class="insurance-details">
                            <img src="./icons/liability_insurance.png" alt="Liability Insurance" />
                            <p>Liability Insurance</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="location-container">
                <h3>Rental Office in <?php echo $moto['Miasto']; ?></h3>
                <div class="location-info">
                    <div class="location-details">
                        <img src="./icons/location.png" alt="Location Icon" />
                        <p><?php echo $moto['Adres']; ?></p>
                    </div>
                    <div class="location-details">
                        <img src="./icons/telephone.png" alt="Phone Icon" />
                        <p>+48 <?php echo formatPhoneNumber($moto['Numer_kontaktowy']); ?></p>
                    </div>
                    <div class="location-details">
                        <img src="./icons/clock.png" alt="Clock Icon" />
                        <p><?php echo substr($moto['Godzina_otwarcia'], 0, 5) . ' - ' . substr($moto['Godzina_zamknięcia'], 0, 5); ?></p>
                    </div>
                </div>
            </div>
            <div class="review-container">
                <h3>Reviews</h3>
                <?php if (!empty($reviews)): ?>
                    <?php foreach ($reviews as $review): ?>
                        <div class="review">
                            <div class="review-header">
                                <span class="name"><?php echo $review['Imię']; ?></span>
                                <div>
                                    <span>&#x2022</span>
                                    <span><?php echo substr($review['Data_dodania'], 0, 10); ?></span>
                                    <span>&#x2022</span>
                                    <span class="star">
                                        <?php for ($i = 0; $i < $review['Ocena']; $i++): ?>
                                            <i class="fas fa-star"></i>
                                        <?php endfor; ?>
                                        <?php for ($i = $review['Ocena']; $i < 5; $i++): ?>
                                            <i class="far fa-star"></i>
                                        <?php endfor; ?>
                                    </span>
                                </div>
                            </div>
                            <div class="review-body">
                                <p><?php echo $review['Komentarz']; ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="review-body">No reviews yet.</p>
                <?php endif; ?>
            </div>
        </div>
        <div class="rent-container">
            <div class="rent-form">
                <p class="main-text" id="total-price"><?php echo $moto['Cena']; ?> zł</p>
                <p class="sub-text">(<?php echo $moto['Cena']; ?> zł per day)</p>
                <hr>
                <form action="checkout.php" method="post" id="rentForm">
                    <?php
                    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                        $_SESSION['rental_data'] = [
                            'id_motocykla' => $id,
                            'start_date' => $_POST['start_date'],
                            'end_date' => $_POST['end_date'],
                            'start_time' => $_POST['start_time'],
                            'end_time' => $_POST['end_time']
                        ];
                    }
                    ?>
                    <input type="hidden" name="id_motocykla" value="<?php echo $id; ?>">
                    <div class="date">
                        <div class="start-date">
                            <label for="start-date">Start Date:</label>
                            <input type="date" id="start_date" name="start_date" required>
                        </div>
                        <div class="end-date">
                            <label for="end-date">End Date:</label>
                            <input type="date" id="end_date" name="end_date" required>
                        </div>
                    </div>
                    <div class="time">
                        <div class="start-time">
                            <label for="start_time">Pickup Time:</label>
                            <input type="time" id="start_time" name="start_time" required>
                        </div>
                        <div class="end-time">
                            <label for="end_time">Return Time:</label>
                            <input type="time" id="end_time" name="end_time" required>
                        </div>
                    </div>
                    <button type="submit">GO TO CHECKOUT</button>
                </form>
                <p class="sub-text">Best price guaranteed.</p>
            </div>
        </div>
    </div>
    <div class="footer-container">
        &copy SwapMoto 2024
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const startDateInput = document.getElementById('start_date');
            const endDateInput = document.getElementById('end_date');
            const totalPriceElement = document.getElementById('total-price');
            const dailyPrice = <?php echo $moto['Cena']; ?>;

            function calculateTotalPrice() {
                const startDate = new Date(startDateInput.value);
                const endDate = new Date(endDateInput.value);
                if (startDate && endDate && endDate >= startDate) {
                    const timeDiff = endDate.getTime() - startDate.getTime();
                    const daysDiff = Math.ceil(timeDiff / (1000 * 3600 * 24)) + 1;
                    const totalPrice = daysDiff * dailyPrice;
                    totalPriceElement.textContent = totalPrice.toFixed(2) + ' zł';
                } else {
                    totalPriceElement.textContent = dailyPrice.toFixed(2) + ' zł';
                }
            }

            startDateInput.addEventListener('change', calculateTotalPrice);
            endDateInput.addEventListener('change', calculateTotalPrice);
        });
    </script>

</body>

</html>