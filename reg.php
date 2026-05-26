<?php
require_once("env/conf.php");
require_once("assets/temps/functions.php");
$pageErrors = [];
function handle_registration(
    $conn,
    $name,
    $email,
    $password
){

    $errors = [];

    if (
        recordExists(
            $conn,
            'users',
            's',
            'email',
            $email
        )
    ) {

        $errors[] =
            'Email already exists';

    }

    if (!empty($errors)) {

        return [
            'success' => false,
            'errors' => $errors
        ];

    }

    $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?,?,?)");
    $stmt->bind_param("sss", $name, $email, $password);
    if($stmt->execute()){
        return [
            'success' => true
        ];
    }else{
        return [
            'success'=>false,
            'errors'=>[
                'Registration failed'
            ]
        ];
    }

}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $errors = [];

    $required = ['action', 'email', 'name', 'password'];

    foreach ($required as $field) {

        if (empty($_POST[$field])) {
            $errors[] = ucfirst($field) . ' is required';
        }

    }


    if(!empty($errors)){
        $pageErrors = $errors;
    }else{
        $action = clean_input($_POST['action']);
        $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
        $name = clean_input($_POST['name']);
        $email = clean_input($_POST['email']);

        if ($action === 'register') {
            $result = handle_registration($conn, $name, $email, $password);

            if (!$result['success']) {
                $pageErrors = $result['errors'];
            }else{
                header("Location: " . WEB_URL);
            }


        }
    }


    

}


?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Expense Tracker Portal</title>
<link rel="stylesheet" href="assets/css/styles.css?v=<?php echo time();?>">
<!-- FontAwesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

</head>
<body>

<header>
    <h1><i class="fa-solid fa-building"></i> Expense Tracker</h1>
    <span style="font-size:0.8rem; color:#64748b;">Secure Access</span>
</header>

<main>
    <div class="container">
        

        <div class="toggle-tabs">
            <button class="tab-btn" data-target="register">Register</button>
        </div>

        <?php if (!empty($pageErrors)): ?>

        <ul class="form-errors">

        <?php foreach ($pageErrors as $error): ?>

        <li><?= $error ?></li>

        <?php endforeach; ?>

        </ul>

        <?php endif; ?>

        <!-- ACCOUNT REQUEST FORM -->
        <form action="" method="post" class="form-section active" id="register">
            <div class="message success" id="registerSuccess"></div>

            <div class="form-group">
                <label>Name</label>
                <input type="text" id="userName" name="name">
            </div>

            <div class="form-group">
                <label>Email</label>
                <input type="email" id="registerEmail" name="email">
            </div>

            <div class="form-group">
                <label>Password</label>
                <input type="password" id="loginPassword" name="password">
            </div>

            <input type="text" name="data" id="data" style="display: none;">

            <button class="submit-btn" id="registerBtn" type="submit" name="action" value="register">
                <i class="fa-solid fa-paper-plane"></i> Register
            </button>
            

        </form>

    </div>
</main>

<footer>
    Track your expenses!
</footer>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="assets/js/index.js?v=<?php echo time();?>"></script>

</body>
</html>