<?php
session_start();

if (isset($_SESSION['user_id']) && isset($_SESSION['cart'])) {
    if (!isset($_SESSION['user_carts'])) {
        $_SESSION['user_carts'] = array();
    }
    $_SESSION['user_carts'][$_SESSION['user_id']] = $_SESSION['cart'];
}

unset($_SESSION['user_id']);
unset($_SESSION['cart']);

header('Location: ../index.php');
exit();
