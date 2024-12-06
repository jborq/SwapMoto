<?php
include '../database/db.php';
session_start();

$base_path = '..';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$query = "SELECT u.*, dk.Numer_telefonu, dk.Adres_zamieszkania 
          FROM Użytkownicy u 
          LEFT JOIN Dane_kontaktowe dk ON u.IDużytkownika = dk.IDużytkownika 
          WHERE u.IDużytkownika = ?";

$stmt = $conn->prepare($query);
$stmt->bind_param('i', $_SESSION['user_id']);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

$query = "SELECT r.*, m.Marka, m.Model, m.Zdjęcie, l.Miasto, l.Adres
          FROM Rezerwacje r
          JOIN Motocykle m ON r.IDmotocykla = m.IDmotocykla
          JOIN Lokalizacje l ON m.IDlokalizacji = l.IDlokalizacji
          WHERE r.IDużytkownika = ?
          ORDER BY r.Data_rozpoczęcia DESC";

$stmt = $conn->prepare($query);
$stmt->bind_param('i', $_SESSION['user_id']);
$stmt->execute();
$reservations = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="pl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Profile - SwapMoto</title>
    <link rel="stylesheet" href="./css/profile-style.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="./css/navbar-footer.css?v=<?php echo time(); ?>">
</head>

<body>
    <?php include '../partials/navbar.php'; ?>
    <div class="container">
        <div class="content-container">
            <div class="profile-section">
                <h1>Profile Information</h1>
                <?php if (isset($_GET['success'])): ?>
                    <div class="alert success">Contact information updated successfully!</div>
                <?php endif; ?>
                <?php if (isset($_GET['error'])): ?>
                    <div class="alert error">An error occurred while updating contact information.</div>
                <?php endif; ?>
                <div class="profile-info">
                    <div class="info-row">
                        <div>
                            <p><strong>Name:</strong> <?php echo htmlspecialchars($user['Imię'] . ' ' . $user['Nazwisko']); ?></p>
                            <p><strong>Email:</strong> <?php echo htmlspecialchars($user['Email']); ?></p>
                            <p><strong>Phone:</strong> <?php echo htmlspecialchars($user['Numer_telefonu'] ?? 'Not provided'); ?></p>
                            <p><strong>Address:</strong> <?php echo htmlspecialchars($user['Adres_zamieszkania'] ?? 'Not provided'); ?></p>
                        </div>
                        <button class="edit-btn" onclick="toggleEditForm()">Edit Contact Info</button>
                    </div>
                    <div id="editForm" class="edit-form" style="display: none;">
                        <form action="update-profile.php" method="post">
                            <div class="form-group">
                                <label for="phone">Phone Number:</label>
                                <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($user['Numer_telefonu'] ?? ''); ?>">
                            </div>
                            <div class="form-group">
                                <label for="address">Address:</label>
                                <input type="text" id="address" name="address" value="<?php echo htmlspecialchars($user['Adres_zamieszkania'] ?? ''); ?>">
                            </div>
                            <div class="form-buttons">
                                <button type="submit">Save Changes</button>
                                <button type="button" onclick="toggleEditForm()">Cancel</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="reservations-section">
                <h2>Reservation History</h2>
                <?php if (empty($reservations)): ?>
                    <p>No reservations found.</p>
                <?php else: ?>
                    <?php foreach ($reservations as $reservation): ?>
                        <div class="reservation-card">
                            <div class="reservation-header">
                                <div class="moto-info">
                                    <img src="<?php echo htmlspecialchars('../uploads/bikes/' . $reservation['Zdjęcie']); ?>"
                                        alt="<?php echo htmlspecialchars($reservation['Marka'] . ' ' . $reservation['Model']); ?>">
                                    <div>
                                        <h3><?php echo htmlspecialchars($reservation['Marka'] . ' ' . $reservation['Model']); ?></h3>
                                        <p><?php echo htmlspecialchars($reservation['Miasto'] . ', ' . $reservation['Adres']); ?></p>
                                    </div>
                                </div>
                                <div class="reservation-status">
                                    <span class="status-badge <?php echo htmlspecialchars($reservation['Status_rezerwacji']); ?>">
                                        <?php echo htmlspecialchars(ucfirst($reservation['Status_rezerwacji'])); ?>
                                    </span>
                                </div>
                            </div>
                            <div class="reservation-details">
                                <div>
                                    <p><strong>Pickup:</strong><br>
                                        <?php echo htmlspecialchars(date('Y-m-d', strtotime($reservation['Data_rozpoczęcia']))); ?> at
                                        <?php echo htmlspecialchars(date('H:i', strtotime($reservation['Godzina_odbioru']))); ?>
                                    </p>
                                </div>
                                <div>
                                    <p><strong>Return:</strong><br>
                                        <?php echo htmlspecialchars(date('Y-m-d', strtotime($reservation['Data_zakończenia']))); ?> at
                                        <?php echo htmlspecialchars(date('H:i', strtotime($reservation['Godzina_oddania']))); ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="footer-container">
        &copy SwapMoto 2024
    </div>

    <script>
        function toggleEditForm() {
            const form = document.getElementById('editForm');
            form.style.display = form.style.display === 'none' ? 'block' : 'none';
        }
    </script>

</body>

</html>