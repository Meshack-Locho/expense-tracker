<?php
session_start();
require_once("env/conf.php");
require_once("assets/temps/functions.php");
$pageErrors = [];
function handle_login(
    $conn,
    $email,
    $password
){

    $errors = [];

    if (
        !recordExists(
            $conn,
            'users',
            's',
            'email',
            $email
        )
    ) {

        $errors[] =
            'Invalid login details provided';

    }else{
        $stmt = $conn->prepare("SELECT id, password FROM users WHERE email=?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result= $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            if (!password_verify($password, $row['password'])) {
                $errors[] =
            'Invalid login details provided - p';
            }else{
                $_SESSION['user_id'] = $row['id'];
            }
        }else{
            $errors[] =
            'Invalid login details provided - e';
        }
    }

    if (!empty($errors)) {

        return [
            'success' => false,
            'errors' => $errors
        ];

    }

    return [
            'success' => true,
        ];


}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $errors = [];

    $required = ['action', 'loginEmail', 'loginPassword'];

    foreach ($required as $field) {

        if (empty($_POST[$field])) {
            $errors[] = ucfirst($field) . ' is required';
        }

    }

    if (!empty($errors)) {
        $pageErrors = $errors;
    }

    $action = clean_input($_POST['action']);
    $password = clean_input($_POST['loginPassword']);
    $email = clean_input($_POST['loginEmail']);

    if ($action === 'login') {
        $result = handle_login($conn, $email, $password);

        if (!$result['success']) {
            $pageErrors = $result['errors'];
        }else{
            header("Location: " . WEB_URL . 'dashboard');
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
            <button class="tab-btn active" data-target="login">Sign In</button>
        </div>

        <!-- LOGIN FORM -->
        <div class="form-section active" id="login">

            <?php if (!empty($pageErrors)): ?>

            <ul class="form-errors">

            <?php foreach ($pageErrors as $error): ?>

            <li><?= $error ?></li>

            <?php endforeach; ?>

            </ul>

        <?php endif; ?>

            <form action="" method="post">
                <div class="form-group">
                    <label>Email</label>
                    <input type="text" id="loginEmail" placeholder="Enter email" name="loginEmail">
                </div>

                <div class="form-group">
                    <label>Password</label>
                    <input type="password" id="loginPassword" placeholder="Enter password" name='loginPassword'>
                </div>


                <button class="submit-btn" id="loginBtn" type="submit" value="login" name="action">
                    <i class="fa-solid fa-right-to-bracket"></i> Sign In
                </button>

                <div class="note">
                    Access is restricted to regsitered users!
                </div>
            </form>
        </div>


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