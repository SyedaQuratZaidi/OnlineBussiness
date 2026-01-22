<?php
session_start();
// destroy user session for front-end users
unset($_SESSION['user_logged_in'], $_SESSION['user_id'], $_SESSION['user_name']);
session_regenerate_id(true);
header('Location: products.php');
exit();
