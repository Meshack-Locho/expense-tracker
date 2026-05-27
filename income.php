<?php
require_once 'env/conf.php';
require_once 'bootstrap.php';
require_once 'assets/temps/statics.php';

if(!isset($_SESSION['user_id'])){
    header('Location: '.WEB_URL.'');
}


require_once 'header.php';
// echo "USER ID: " . $_SESSION['user_id'] . '<br><br>';
// echo "USER ROLE: ".$_SESSION['role'] . '<br><br>';
// echo 'COMAPNY ID: ' . $_SESSION['c_id'] . '<br><br>';
?>

<main class="main-dashboard-content">
        <h2 class="top-head-table-form">Log Income</h2>
        <div class="form-wrapper">
          
            <form action="" method="POST" enctype="multipart/form-data" class="forms addition-forms" id="incomeForm">

            <input type="hidden" name="action" id="action" value="addIncome">
              
                <div class="form-group">
                    <label>Amount</label>
                    <span class="error"></span>
                    <input type="text" name="inc_amt" id="inc_amt">
                </div>

                <div class="form-group">
                    <label>Source</label>
                    <span class="error"></span>
                    <input type="text" name="inc_src" id="inc_src">
                </div>

                <div class="form-group">
                    <label>Income Date</label>
                    <span class="error"></span>
                    <input type="date" name="inc_date" id="inc_date">
                </div>


                <div class="form-group">
                    <!-- Submit -->
                   <div class="row-btns">
                        <button type="submit" class="main-btns add-pos-btn with-loader-btns" id="add-dep-btn">Save</button>
                        <button type="button" onclick="history.back()" class="main-btns warning">Back</button>
                    </div>
                </div>
        </form>
        </div>



</main>

<?php include 'footer.php'?>