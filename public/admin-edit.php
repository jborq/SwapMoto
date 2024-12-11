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

$table = $_GET['table'] ?? '';
$id = $_GET['id'] ?? '';

$primary_keys = [
    'Motocykle' => 'IDmotocykla',
    'Użytkownicy' => 'IDużytkownika',
    'Rezerwacje' => 'IDrezerwacji',
    'Lokalizacje' => 'IDlokalizacji',
    'Opinie' => 'IDopinii'
];

// Validate table name
if (!isset($primary_keys[$table])) {
    $_SESSION['admin_error'] = "Invalid table name";
    header('Location: admin.php');
    exit();
}

$primary_key = $primary_keys[$table];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $conn->begin_transaction();

        $fields = [];
        $types = '';
        $values = [];

        foreach ($_POST as $key => $value) {
            if ($key !== 'submit' && $key !== $primary_key && $key !== 'Numer_telefonu' && $key !== 'Adres_zamieszkania') {
                $fields[] = "`$key` = ?";
                $types .= is_numeric($value) ? 'i' : 's';
                $values[] = $value;
            }
        }

        $types .= 'i';
        $values[] = $id;

        if (!empty($fields)) {
            $query = "UPDATE $table SET " . implode(', ', $fields) . " WHERE $primary_key = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param($types, ...$values);
            $stmt->execute();
        }

        // Update contact details if provided
        $phone = $_POST['Numer_telefonu'] ?? '';
        $address = $_POST['Adres_zamieszkania'] ?? '';

        if (!empty($phone) || !empty($address)) {
            $check_query = "SELECT IDdanych FROM Dane_kontaktowe WHERE IDużytkownika = ?";
            $stmt = $conn->prepare($check_query);
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $query = "UPDATE Dane_kontaktowe SET Numer_telefonu = ?, Adres_zamieszkania = ? WHERE IDużytkownika = ?";
            } else {
                $query = "INSERT INTO Dane_kontaktowe (Numer_telefonu, Adres_zamieszkania, IDużytkownika) VALUES (?, ?, ?)";
            }

            $stmt = $conn->prepare($query);
            $stmt->bind_param('ssi', $phone, $address, $id);
            $stmt->execute();
        }

        $conn->commit();
        $_SESSION['admin_success'] = "Record updated successfully";
        header("Location: admin.php?table=$table");
        exit();
    } catch (Exception $e) {
        $conn->rollback();
        $_SESSION['admin_error'] = "Error updating record: " . $e->getMessage();
    }
}

// Get record data
$query = "SELECT * FROM $table WHERE $primary_key = ?";

if ($table === 'Użytkownicy') {
    $query = "SELECT u.*, dk.Numer_telefonu, dk.Adres_zamieszkania 
              FROM Użytkownicy u 
              LEFT JOIN Dane_kontaktowe dk ON u.IDużytkownika = dk.IDużytkownika 
              WHERE u.IDużytkownika = ?";
}

$stmt = $conn->prepare($query);
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();
$record = $result->fetch_assoc();

if (!$record) {
    $_SESSION['admin_error'] = "Record not found";
    header('Location: admin.php');
    exit();
}

// Define excluded fields (not to be edited)
$excluded_fields = [
    'Użytkownicy' => ['IDużytkownika', 'IDroli', 'Hasło'],
    'Motocykle' => ['IDmotocykla'],
    'Rezerwacje' => ['IDrezerwacji', 'Data_utworzenia'],
    'Lokalizacje' => ['IDlokalizacji'],
    'Opinie' => ['IDopinii', 'Data_dodania']
];

// Define field types for special inputs
$field_types = [
    'Status' => ['dostępny', 'niedostępny'],
    'Status_rezerwacji' => ['trwa', 'zrealizowana', 'anulowana'],
    'Kategoria_prawa_jazdy' => ['AM', 'A1', 'A2', 'A']
];
?>

<!DOCTYPE html>
<html lang="pl">

<head>
    <meta charset="UTF-8">
    <title>Edit Record - SwapMoto Admin</title>
    <link rel="stylesheet" href="./css/admin-style.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="./css/navbar-footer.css?v=<?php echo time(); ?>">
</head>

<body>
    <?php include '../partials/navbar.php'; ?>
    <div class="admin-container">
        <div class="edit-form-container">
            <h1>Edit Record</h1>
            <form method="POST" class="edit-form">
                <?php foreach ($record as $field => $value): ?>
                    <?php if (!isset($excluded_fields[$table]) || !in_array($field, $excluded_fields[$table])): ?>
                        <div class="form-group">
                            <label for="<?php echo htmlspecialchars($field); ?>"><?php echo htmlspecialchars($field); ?>:</label>
                            <?php if (isset($field_types[$field])): ?>
                                <select name="<?php echo htmlspecialchars($field); ?>" id="<?php echo htmlspecialchars($field); ?>">
                                    <?php foreach ($field_types[$field] as $option): ?>
                                        <option value="<?php echo htmlspecialchars($option); ?>" <?php echo htmlspecialchars($value) === $option ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($option); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            <?php else: ?>
                                <input type="<?php echo strpos($field, 'Data') === 0 ? 'datetime-local' : 'text'; ?>"
                                    name="<?php echo $field; ?>"
                                    id="<?php echo $field; ?>"
                                    value="<?php echo htmlspecialchars($value); ?>">
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
                <div class="form-actions">
                    <button type="button" onclick="location.href='admin.php?table=<?php echo $table; ?>'">Cancel</button>
                    <button type="submit" name="submit">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
    <div class="footer-container-admin">
        &copy SwapMoto 2024 - Admin Panel
    </div>
</body>

</html>