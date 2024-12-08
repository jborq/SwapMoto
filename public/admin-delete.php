<?php
include '../database/db.php';
session_start();

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

// Get table and ID from URL
$table = $_GET['table'] ?? '';
$id = $_GET['id'] ?? '';

// Define primary keys for each table
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

try {
    $conn->begin_transaction();

    // Handle special cases before deletion
    switch ($table) {
        case 'Użytkownicy':
            // Delete related records in Dane_kontaktowe
            $query = "DELETE FROM Dane_kontaktowe WHERE IDużytkownika = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param('i', $id);
            $stmt->execute();

            // Delete related records in Rezerwacje
            $query = "DELETE FROM Rezerwacje WHERE IDużytkownika = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param('i', $id);
            $stmt->execute();
            break;

        case 'Motocykle':
            // Delete related records in Rezerwacje
            $query = "DELETE FROM Rezerwacje WHERE IDmotocykla = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param('i', $id);
            $stmt->execute();

            // Delete related records in Opinie
            $query = "DELETE FROM Opinie WHERE IDmotocykla = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param('i', $id);
            $stmt->execute();

            // Delete motorcycle image if exists
            $query = "SELECT Zdjęcie FROM Motocykle WHERE IDmotocykla = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $result = $stmt->get_result();
            $moto = $result->fetch_assoc();
            
            if ($moto && $moto['Zdjęcie']) {
                $image_path = '../uploads/bikes/' . $moto['Zdjęcie'];
                if (file_exists($image_path)) {
                    unlink($image_path);
                }
            }
            break;

        case 'Lokalizacje':
            // Update related motorcycles to have no location
            $query = "UPDATE Motocykle SET IDlokalizacji = NULL WHERE IDlokalizacji = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param('i', $id);
            $stmt->execute();
            break;
    }

    // Delete the main record
    $primary_key = $primary_keys[$table];
    $query = "DELETE FROM $table WHERE $primary_key = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $id);
    $stmt->execute();

    $conn->commit();
    $_SESSION['admin_success'] = "Record deleted successfully";

} catch (Exception $e) {
    $conn->rollback();
    $_SESSION['admin_error'] = "Error deleting record: " . $e->getMessage();
}

header('Location: admin.php?table=' . htmlspecialchars($table));
exit();
?>