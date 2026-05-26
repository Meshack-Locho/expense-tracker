<?php
    define('_AMSCODESECURITY', '16343');
    define("WEB_URL", "http://localhost:8080/mysite/expense_t/");
    define('BASE_PATH', dirname(__DIR__));
    define('DB_HOSTNAME', 'localhost');
    define('DB_USERNAME', 'root');
    define('DB_PASSWORD', '');
    define('DB_DATABASE', 'expense_tracker');
    define('MAIL_PASS', 'LET MESH = DEV;');
    $conn = new mysqli(DB_HOSTNAME,DB_USERNAME,DB_PASSWORD,DB_DATABASE);

?>