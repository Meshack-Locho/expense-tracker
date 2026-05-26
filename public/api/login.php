<?php
session_start();
header('Content-Type: application/json');

require_once '../../library/helper.php';
require_once '../../env/conf.php';
require_once '../../assets/temps/functions.php';


$helper = new CoreHelper();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = clean_input($_POST['email']) ?? '';
    $password = clean_input($_POST['password']) ?? '';
    
    if (!empty($_POST['remember_tok'])) {
        $token = bin2hex(random_bytes(32));
        $hashedToken = hash('sha256', $token);
    }

    $result = $helper->login($email, $password, $hashedToken, $token, $conn);

    echo json_encode($result);
}

