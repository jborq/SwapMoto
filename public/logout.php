<?php
session_start();

// Restore user cart or create new one
if (isset($_SESSION['user_id'])) {
    if (!isset($_SESSION['user_carts'])) {
        $_SESSION['user_carts'] = array();
    }
    $_SESSION['user_carts'][$_SESSION['user_id']] = $_SESSION['cart'];
}

// Unset user_id and cart
unset($_SESSION['user_id']);
unset($_SESSION['cart']);

header('Location: ../index.php');
exit();