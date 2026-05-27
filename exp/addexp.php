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

$userId = (int)$_SESSION['user_id'];
$action = 'addExpense';
$categoryId= 0;
$description = '';
$expenseDate = '';
$expenseAmount = 0;
$marker = '';

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $action = 'updateItem';
    $itemId = intval($_GET['id']);

    $stmt = $conn->prepare("SELECT amount, category_id, description, expense_date FROM expenses WHERE id=? AND user_id=?");
    $stmt->bind_param("ii", $itemId, $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        if ($row = $result->fetch_assoc()) {
            $categoryId = $row['category_id'];
            $description = $row['description'];
            $expenseAmount = (float)$row['amount'];
            $expenseDate = $row['expense_date'];
        }
    }
}

// echo "USER ID: " . $_SESSION['user_id'] . '<br><br>';
// echo "USER ROLE: ".$_SESSION['role'] . '<br><br>';
// echo 'COMAPNY ID: ' . $_SESSION['c_id'] . '<br><br>';
?>

<main class="main-dashboard-content">
        <h2 class="top-head-table-form">Add/Edit Category</h2>
        <div class="form-wrapper">
          
            <form action="" method="POST" enctype="multipart/form-data" class="exp-form forms addition-forms" id="addExpForm">
                <input type="hidden" name="action" id="action" value="<?= $action ?>">
                <?php
                    if (isset($_GET['id']) && is_numeric($_GET['id'])) {?>
                        <input type="hidden" name="item_id" id="item_id" value="<?= htmlspecialchars($_GET['id']) ?>">
                        <input type="hidden" name="entity" id="entity" value="expense">
                    <?php }
                ?>
                <div class="form-group">
                    <label>Expense Category</label>
                    <span class="error"></span>
                    <select name="exp_cat" id="exp_cat">
                        <option value="">--------- Select Category ----------</option>
                        <?php
                            $categories = getCategories($conn);
                            foreach ($categories['data'] as $category) {
                                $marker = '';
                                if ((int)$category['id'] === (int)$categoryId) {
                                    $marker = 'selected';
                                }
                                echo "<option value='".(int)$category['id']."' ".htmlspecialchars($marker).">".$category['name']."</option>";
                            }
                            
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Expense Amount</label>
                    <span class="error"></span>
                    <input type="text" name="exp_amt" id="exp_amt" value="<?= htmlspecialchars($expenseAmount) ?>">
                </div>

                <div class="form-group">
                    <label>Expense Description</label>
                    <span class="error"></span>
                    <textarea name="exp_desc" id="exp_desc"><?= htmlspecialchars($description) ?></textarea>
                </div>

                <div class="form-group">
                    <label>Expense Date</label>
                    <span class="error"></span>
                    <input type="date" name="exp_date" id="exp_date" value="<?= $expenseDate ?>">
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