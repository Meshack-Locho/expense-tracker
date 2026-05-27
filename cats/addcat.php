<?php
require_once '../env/conf.php';
require_once '../bootstrap.php';
require_once '../assets/temps/statics.php';

if(!isset($_SESSION['user_id'])){
    header('Location: '.WEB_URL.'');
}

$action = 'addCat';
$categoryName = '';

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $action = 'updateItem';
    $itemId = intval($_GET['id']);

    $stmt = $conn->prepare("SELECT name FROM categories WHERE id=?");
    $stmt->bind_param("i", $itemId);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $categoryName = $row['name'];
}

require_once '../header.php';
// echo "USER ID: " . $_SESSION['user_id'] . '<br><br>';
// echo "USER ROLE: ".$_SESSION['role'] . '<br><br>';
// echo 'COMAPNY ID: ' . $_SESSION['c_id'] . '<br><br>';
?>

<main class="main-dashboard-content">
        <h2 class="top-head-table-form">Add/Edit Category</h2>
        <div class="form-wrapper">
          
            <form action="" method="POST" enctype="multipart/form-data" class="cat-form forms addition-forms" id="addCatForm">
                <input type="hidden" name="action" id="action" value="<?= htmlspecialchars($action) ?>">
                <?php
                    if (isset($_GET['id']) && is_numeric($_GET['id'])) {?>
                        <input type="hidden" name="item_id" id="item_id" value="<?= htmlspecialchars($_GET['id']) ?>">
                        <input type="hidden" name="entity" id="entity" value="category">
                    <?php }
                ?>
                <div class="form-group">
                    <label>Category Name</label>
                    <span class="error"></span>
                    <input type="text" name="cat_name" id="cat_name" value="<?= htmlspecialchars($categoryName) ?>">
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