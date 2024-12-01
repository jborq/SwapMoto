<?php
include '../database/db.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: profile.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$phone = !empty($_POST['phone']) ? $_POST['phone'] : null;
$address = !empty($_POST['address']) ? $_POST['address'] : null;

try {
    $conn->begin_transaction();

    $check_user = "SELECT IDużytkownika FROM Użytkownicy WHERE IDużytkownika = ?";
    $stmt = $conn->prepare($check_user);
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        throw new Exception("User not found");
    }

    $check_contact = "SELECT IDdanych FROM Dane_kontaktowe WHERE IDużytkownika = ?";
    $stmt = $conn->prepare($check_contact);
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        if ($phone !== null && $address !== null) {
            $query = "UPDATE Dane_kontaktowe 
                      SET Numer_telefonu = ?, Adres_zamieszkania = ? 
                      WHERE IDużytkownika = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param('ssi', $phone, $address, $user_id);
        } elseif ($phone !== null) {
            $query = "UPDATE Dane_kontaktowe 
                      SET Numer_telefonu = ? 
                      WHERE IDużytkownika = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param('si', $phone, $user_id);
        } elseif ($address !== null) {
            $query = "UPDATE Dane_kontaktowe 
                      SET Adres_zamieszkania = ? 
                      WHERE IDużytkownika = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param('si', $address, $user_id);
        }
    } else {
        if ($phone !== null && $address !== null) {
            $query = "INSERT INTO Dane_kontaktowe (IDużytkownika, Numer_telefonu, Adres_zamieszkania) 
                      VALUES (?, ?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->bind_param('iss', $user_id, $phone, $address);
        } elseif ($phone !== null) {
            $query = "INSERT INTO Dane_kontaktowe (IDużytkownika, Numer_telefonu) 
                      VALUES (?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->bind_param('is', $user_id, $phone);
        } elseif ($address !== null) {
            $query = "INSERT INTO Dane_kontaktowe (IDużytkownika, Adres_zamieszkania) 
                      VALUES (?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->bind_param('is', $user_id, $address);
        }
    }

    $stmt->execute();
    $conn->commit();
    
    header('Location: profile.php?success=1');
    exit();

} catch (Exception $e) {
    $conn->rollback();
    header('Location: profile.php?error=1&message=' . urlencode($e->getMessage()));
    exit();
}
?>