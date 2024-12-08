<?php
include '../database/db.php';
session_start();

$base_path = '..';

// Check if user is admin (IDroli = 2)
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$query = "SELECT IDroli FROM Użytkownicy WHERE IDużytkownika = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($user['IDroli'] != 2) {
    header('Location: ../index.php');
    exit();
}

$table_names = [
    'Motocykle' => 'Motorcycles',
    'Użytkownicy' => 'Users',
    'Rezerwacje' => 'Reservations',
    'Lokalizacje' => 'Locations',
    'Opinie' => 'Reviews'
];

$visible_columns = [
    'Motocykle' => ['IDmotocykla', 'Marka', 'Model', 'Rok_produkcji'],
    'Użytkownicy' => ['IDużytkownika', 'Imię', 'Nazwisko', 'Email'],
    'Rezerwacje' => ['IDrezerwacji', 'Status_rezerwacji', 'Data_rozpoczęcia', 'Całkowita_cena'],
    'Lokalizacje' => ['IDlokalizacji', 'Miasto', 'Adres', 'Numer_kontaktowy'],
    'Opinie' => ['IDopinii', 'IDmotocykla', 'Imię', 'Ocena']
];

// Handle table selection
$selected_table = isset($_GET['table']) ? $_GET['table'] : 'Motocykle';
$allowed_tables = array_keys($table_names);

if (!in_array($selected_table, $allowed_tables)) {
    $selected_table = 'Motocykle';
}

// Get table data
$query = "SELECT * FROM $selected_table";
$result = $conn->query($query);
$data = $result->fetch_all(MYSQLI_ASSOC);

?>

<!DOCTYPE html>
<html lang="pl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - SwapMoto</title>
    <link rel="stylesheet" href="./css/admin-style.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="./css/navbar-footer.css?v=<?php echo time(); ?>">
</head>

<body>
    <?php include '../partials/navbar.php'; ?>
    <div class="admin-container">
        <div class="sidebar">
            <h2>Admin Panel</h2>
            <nav>
                <?php foreach ($allowed_tables as $table): ?>
                    <a href="?table=<?php echo $table; ?>"
                        class="<?php echo $selected_table === $table ? 'active' : ''; ?>">
                        <?php echo $table_names[$table]; ?>
                    </a>
                <?php endforeach; ?>
            </nav>
        </div>
        <div class="content">
            <h1><?php echo $table_names[$selected_table]; ?></h1>
            <div class="table-actions">
                <button onclick="location.href='admin_add.php?table=<?php echo $selected_table; ?>'">
                    Add New Record
                </button>
            </div>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <?php foreach ($visible_columns[$selected_table] as $column): ?>
                                <th><?php echo htmlspecialchars($column); ?></th>
                            <?php endforeach; ?>
                            <th class='actions'>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($data as $row): ?>
                            <tr onclick="location.href='../public/admin-detail.php?table=<?php echo $selected_table; ?>&id=<?php echo $row[array_key_first($row)]; ?>'" style="cursor: pointer;">
                                <?php foreach ($visible_columns[$selected_table] as $column): ?>
                                    <td><?php echo htmlspecialchars($row[$column]); ?></td>
                                <?php endforeach; ?>
                                <td class="actions" onclick="event.stopPropagation();">
                                    <button onclick="location.href='admin_edit.php?table=<?php echo $selected_table; ?>&id=<?php echo $row[array_key_first($row)]; ?>'"
                                        class="action-btn edit-btn">Edit</button>
                                    <button onclick="if(confirm('Are you sure you want to delete this record?')) location.href='admin_delete.php?table=<?php echo $selected_table; ?>&id=<?php echo $row[array_key_first($row)]; ?>'"
                                        class="action-btn delete-btn">Delete</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="footer-container-admin">
        &copy SwapMoto 2024 - Admin Panel
    </div>
</body>

</html>