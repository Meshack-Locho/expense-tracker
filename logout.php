<?php
require_once 'env/conf.php';
session_start();

if ($_SESSION['type'] === 'customer') {
    session_destroy();
    header("Location: ".WEB_URL."");
}

if ($_SESSION['type'] === 'admin') {
    session_destroy();
    header("Location: ".WEB_URL."ad-login");
}


?>