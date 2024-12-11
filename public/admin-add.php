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

$table_fields = [
    'Motocykle' => [
        'IDkategorii' => 'number',
        'IDlokalizacji' => 'number',
        'Marka' => 'text',
        'Model' => 'text',
        'Rok_produkcji' => 'number',
        'Pojemność_silnika' => 'number',
        'Kategoria_prawa_jazdy' => ['AM', 'A1', 'A2', 'A'],
        'Status' => ['dostępny', 'niedostępny'],
        'Cena' => 'number',
        'Zdjęcie' => 'file'
    ],
    'Użytkownicy' => [
        'IDroli' => 'number',
        'Imię' => 'text',
        'Nazwisko' => 'text',
        'Email' => 'email',
        'Hasło' => 'password'
    ],
    'Rezerwacje' => [
        'IDużytkownika' => 'number',
        'IDmotocykla' => 'number',
        'Status_rezerwacji' => ['trwa', 'zrealizowana', 'anulowana'],
        'Data_rozpoczęcia' => 'datetime-local',
        'Data_zakończenia' => 'datetime-local',
        'Godzina_odbioru' => 'time',
        'Godzina_oddania' => 'time',
        'Imię_kierowcy' => 'text',
        'Nazwisko_kierowcy' => 'text',
        'Email_kierowcy' => 'email',
        'Telefon_kierowcy' => 'tel',
        'Data_urodzenia_kierowcy' => 'date',
        'Kategoria_prawa_jazdy' => ['A', 'A1', 'A2', 'B'],
        'Całkowita_cena' => 'number'
    ],
    'Lokalizacje' => [
        'Oddział' => 'text',
        'Miasto' => 'text',
        'Kod_pocztowy' => 'text',
        'Adres' => 'text',
        'Numer_kontaktowy' => 'text',
        'Godzina_otwarcia' => 'time',
        'Godzina_zamknięcia' => 'time'
    ],
    'Opinie' => [
        'IDmotocykla' => 'number',
        'Imię' => 'text',
        'Ocena' => ['1', '2', '3', '4', '5'],
        'Komentarz' => 'textarea',
        'Data_dodania' => 'datetime-local'
    ]
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $conn->begin_transaction();

        $fields = [];
        $placeholders = [];
        $types = '';
        $values = [];

        foreach ($_POST as $key => $value) {
            if ($key !== 'submit' && !empty($value)) {
                $fields[] = "`$key`";
                $placeholders[] = "?";

                // Handle password encryption
                if ($table === 'Użytkownicy' && $key === 'Hasło') {
                    $value = password_hash($value, PASSWORD_DEFAULT);
                }

                $types .= is_numeric($value) ? 'i' : 's';
                $values[] = $value;
            }
        }

        // Handle file upload for Motocykle
        if ($table === 'Motocykle' && isset($_FILES['Zdjęcie']) && $_FILES['Zdjęcie']['error'] === 0) {
            $target_dir = "../uploads/bikes/";
            $file_extension = pathinfo($_FILES["Zdjęcie"]["name"], PATHINFO_EXTENSION);
            $file_name = uniqid() . '.' . $file_extension;
            $target_file = $target_dir . $file_name;

            if (move_uploaded_file($_FILES["Zdjęcie"]["tmp_name"], $target_file)) {
                $fields[] = "`Zdjęcie`";
                $placeholders[] = "?";
                $types .= 's';
                $values[] = $file_name;
            }
        }

        $query = "INSERT INTO $table (" . implode(', ', $fields) . ") VALUES (" . implode(', ', $placeholders) . ")";
        $stmt = $conn->prepare($query);
        $stmt->bind_param($types, ...$values);
        $stmt->execute();

        $conn->commit();
        $_SESSION['admin_success'] = "Record added successfully";
        header("Location: admin.php?table=$table");
        exit();
    } catch (Exception $e) {
        $conn->rollback();
        $_SESSION['admin_error'] = "Error adding record: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="pl">

<head>
    <meta charset="UTF-8">
    <title>Add New Record - SwapMoto Admin</title>
    <link rel="stylesheet" href="./css/admin-style.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="./css/navbar-footer.css?v=<?php echo time(); ?>">
</head>

<body>
    <?php include '../partials/navbar.php'; ?>
    <div class="admin-container">
        <div class="add-form-container">
            <h1>Add New Record</h1>
            <?php if (isset($table_fields[$table])): ?>
                <form method="POST" class="add-form" enctype="multipart/form-data">
                    <?php foreach ($table_fields[$table] as $field => $type): ?>
                        <div class="form-group">
                            <label for="<?php echo $field; ?>"><?php echo $field; ?>:</label>
                            <?php if (is_array($type)): ?>
                                <select name="<?php echo $field; ?>" id="<?php echo $field; ?>">
                                    <?php foreach ($type as $option): ?>
                                        <option value="<?php echo $option; ?>"><?php echo $option; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            <?php elseif ($type === 'textarea'): ?>
                                <textarea name="<?php echo $field; ?>" id="<?php echo $field; ?>" required></textarea>
                            <?php else: ?>
                                <input type="<?php echo $type; ?>"
                                    name="<?php echo $field; ?>"
                                    id="<?php echo $field; ?>"
                                    <?php if ($type === 'tel'): ?>
                                    pattern="\d{9}"
                                    title="Phone number must be 9 digits"
                                    <?php elseif ($type === 'email'): ?>
                                    pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$"
                                    title="Enter a valid email address"
                                    <?php elseif ($type === 'password'): ?>
                                    minlength="8"
                                    pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[!@#$%^&*]).{8,}"
                                    title="Must contain at least 8 characters, one uppercase, one lowercase, one number and one special character"
                                    <?php elseif ($type === 'number' && $field === 'Całkowita_cena'): ?>
                                    step="0.01"
                                    min="0"
                                    <?php endif; ?>
                                    required>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                    <div class="form-actions">
                        <button type="button" onclick="location.href='admin.php?table=<?php echo $table; ?>'">Cancel</button>
                        <button type="submit" name="submit">Add Record</button>
                    </div>
                </form>
            <?php else: ?>
                <p>Invalid table selected</p>
            <?php endif; ?>
        </div>
    </div>
    <div class="footer-container-admin">
        &copy SwapMoto 2024 - Admin Panel
    </div>
</body>

</html>