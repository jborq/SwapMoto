<?php
include '../database/db.php';
session_start();

$base_path = '..';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$table = $_GET['table'] ?? '';
$id = $_GET['id'] ?? '';

$primary_keys = [
    'Motocykle' => 'IDmotocykla',
    'Użytkownicy' => 'IDużytkownika',
    'Rezerwacje' => 'IDrezerwacji',
    'Lokalizacje' => 'IDlokalizacji',
    'Opinie' => 'IDopinii'
];

if (!isset($primary_keys[$table])) {
    header('Location: admin.php');
    exit();
}

$primary_key = $primary_keys[$table];

$query = "SELECT * FROM $table WHERE $primary_key = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();
$record = $result->fetch_assoc();

if (!$record) {
    header('Location: admin.php');
    exit();
}

$excluded_columns = [
    'Użytkownicy' => ['Hasło']
];

?>

<!DOCTYPE html>
<html lang="pl">

<head>
    <meta charset="UTF-8">
    <title>Detail View - SwapMoto Admin</title>
    <link rel="stylesheet" href="./css/admin-style.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="./css/navbar-footer.css?v=<?php echo time(); ?>">
</head>

<body>
    <?php include '../partials/navbar.php'; ?>
    <div class="admin-container">
        <div class="detail-view">
            <h1>Detailed View</h1>
            <div class="detail-card">
                <?php foreach ($record as $key => $value): ?>
                    <?php if (!isset($excluded_columns[$table]) || !in_array($key, $excluded_columns[$table])): ?>
                        <div class="detail-row">
                            <strong><?php echo htmlspecialchars($key); ?>:</strong>
                            <span><?php echo htmlspecialchars($value); ?></span>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
            <div class="detail-actions">
                <button onclick="location.href='admin.php?table=<?php echo $table; ?>'">Back</button>
                <button onclick="location.href='../public/admin-edit.php?table=<?php echo $table; ?>&id=<?php echo $id; ?>'">Edit</button>
            </div>
        </div>
    </div>
    <div class="footer-container-admin details">
        &copy SwapMoto 2024 - Admin Panel
    </div>
</body>

</html>