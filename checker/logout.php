<?php
session_start();

if (!isset($_SESSION['message'])) {
    $_SESSION['message'] = "You have been logged out.";
}

$message = $_SESSION['message'];

session_unset();
session_destroy();

session_start();
$_SESSION['message'] = $message;

echo '<script>window.location.href = "../login";</script>';
exit();
?>
