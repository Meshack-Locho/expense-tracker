<?php
require_once '../env/conf.php';
require_once '../bootstrap.php';
require_once '../assets/temps/statics.php';

if(!isset($_SESSION['user_id'])){
    header('Location: '.WEB_URL.'');
}

require_once '../header.php';

function getCategories($conn){
    $stmt = $conn->prepare("SELECT * FROM categories");
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        return [
            'status' => false,
            'data' => []
        ];
    }

    $row = $result->fetch_All(MYSQLI_ASSOC);

    return [
        'status' => true,
        'data' => $row
    ];
}

// echo "USER ID: " . $_SESSION['user_id'] . '<br><br>';
// echo "USER ROLE: ".$_SESSION['role'] . '<br><br>';
// echo 'COMAPNY ID: ' . $_SESSION['c_id'] . '<br><br>';
?>

<main class="main-dashboard-content">
        <h2 class="top-head-table-form">Add/Edit Category</h2>
        <div class="form-wrapper">
          
            <form action="" method="POST" enctype="multipart/form-data" class="exp-form forms addition-forms" id="addExpForm">
                <input type="hidden" name="action" id="action" value="addExpense">
                <div class="form-group">
                    <label>Expense Category</label>
                    <span class="error"></span>
                    <select name="exp_cat" id="exp_cat">
                        <option value="">--------- Select Category ----------</option>
                        <?php
                            $categories = getCategories($conn);
                            foreach ($categories['data'] as $category) {
                                echo "<option value='".(int)$category['id']."'>".$category['name']."</option>";
                            }
                            
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Expense Amount</label>
                    <span class="error"></span>
                    <input type="text" name="exp_amt" id="exp_amt">
                </div>

                <div class="form-group">
                    <label>Expense Description</label>
                    <span class="error"></span>
                    <textarea name="exp_desc" id="exp_desc"></textarea>
                </div>

                <div class="form-group">
                    <label>Expense Date</label>
                    <span class="error"></span>
                    <input type="date" name="exp_date" id="exp_date">
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

<?php include '../footer.php'?>