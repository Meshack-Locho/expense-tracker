<?php
require_once '../env/conf.php';
require_once '../bootstrap.php';
require_once '../assets/temps/statics.php';
if(!isset($_SESSION['user_id'])){
    header('Location: '.WEB_URL.'');
}

$userId = (int)$_SESSION['user_id'];

require_once '../header.php';
?>
 
 <main class="main-adm main-dashboard-content">
    <h2 class="top-head-table-form">Expenses</h2>
    <!-- <input type="text" id="globalSearch" placeholder="Search all columns..." style="margin-bottom: 10px; padding: 5px; width: 200px;"> -->

  <div class="pos-table dash-sect">
        <div class="sect-body">
            <table id="catTable" class="tables table table-bordered table-striped dt-responsive dataTable no-footer dtr-inline collapsed">
                <thead>
                    <tr>
                
                        <th>Category</th>
                        <th>Amount</th>
                        <th>Expense Date</th>
                        <th>Description</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                        <?php
                            $result_cats = $conn->prepare("SELECT e.id, e.category_id, e.amount, e.expense_date, e.description, c.name AS category_name FROM expenses e LEFT JOIN categories c ON e.category_id=c.id WHERE e.user_id=?");
                            $result_cats->bind_param("i", $userId);
                            $result_cats->execute();

                            $result = $result_cats->get_result();

                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {?>
                                <tr class="category-row item-row">
                                    <td><?= htmlspecialchars($row['category_name']) ?></td>
                                    <td><?= 'KES ' . htmlspecialchars(number_format($row['amount'], 2)) ?></td>
                                    <td><?= htmlspecialchars($row['expense_date']) ?></td>
                                    <td><?= htmlspecialchars($row['description']) ?></td>
                                    <td class="action-btns">
                                        <div class="actions">
                                            <a href="<?php echo WEB_URL;?>exp/addexp?id=<?= htmlspecialchars($row['id']) ?>" class="tbl-actions edit"><i class="fa-solid fa-pen"></i></a>
                                            <form action="" method="post" class="prod-del-form">
                                                <input type="hidden" name="cat_id" id="cat_id">
                                                <input type="hidden" name="action" id="action" value="delete-category">
                                                <button class="tbl-actions delete delete-item-btn" id="#delete-category-btn"><i class="fa-solid fa-trash"></i></button>
                                            </form>
                                        </div>
                                    </td>
                                </tr> 
                                <?php }
                            }
                        ?>
                   
                </tbody>
            
            </table>
        </div>
  </div>
 </main>
 <?php include '../footer.php'?>