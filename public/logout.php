<?php
session_start();

if (isset($_SESSION['user_id'])) {
    session_unset();
    header('Location: ../index.php');
    exit();
} else {
    header('Location: ../index.php');
    exit();
}
?>

