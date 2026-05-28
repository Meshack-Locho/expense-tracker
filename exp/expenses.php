<?php
require_once '../env/conf.php';
require_once '../bootstrap.php';
require_once '../assets/temps/statics.php';
if(!isset($_SESSION['user_id'])){
    header('Location: '.WEB_URL.'');
}

$userId = (int)$_SESSION['user_id'];
$sql =
"
SELECT
    e.id,
    e.category_id,
    e.amount,
    e.expense_date,
    e.description,
    c.name AS category_name
FROM expenses e
LEFT JOIN categories c
ON e.category_id = c.id
WHERE e.user_id = ?
";

$params = [$userId];
$types = "i";

$year = date('Y');
$month = date('m');
$additionalQuery = '';


if (isset($_GET['m']) && !empty($_GET['m'])) {
    $month = intval($_GET['m']);
    $sql .= " AND MONTH(e.expense_date)=? ";

    $params[] = $month;
    $types .= "i";
}

if (isset($_GET['y']) && !empty($_GET['m'])) {
    $year = intval($_GET['y']);
    $sql .= " AND YEAR(e.expense_date)=? ";

    $params[] = $year;
    $types .= "i";
}

require_once '../header.php';
?>
 
 <main class="main-adm main-dashboard-content">
    <h2 class="top-head-table-form">Expenses</h2>
    <!-- <input type="text" id="globalSearch" placeholder="Search all columns..." style="margin-bottom: 10px; padding: 5px; width: 200px;"> -->
    <section class="full-sect flex-col">
        <h4>Filter Data</h4>
        <div class="date-filter">
            <div class="filter-handlers form-group flex-row">
                <select name="month" id="month-selector" class="date-filter-selectors">
                    <option value="">Select Month</option>
                    <option value="1" <?= $month === 1 ? 'selected' : '' ?>>January</option>
                    <option value="2" <?= $month === 2 ? 'selected' : '' ?>>Feb</option>
                    <option value="3" <?= $month === 3 ? 'selected' : '' ?>>March</option>
                    <option value="4" <?= $month === 4 ? 'selected' : '' ?>>April</option>
                    <option value="5" <?= $month === 5 ? 'selected' : '' ?>>May</option>
                    <option value="6" <?= $month === 6 ? 'selected' : '' ?>>June</option>
                    <option value="7" <?= $month === 7 ? 'selected' : '' ?>>July</option>
                    <option value="8" <?= $month === 8 ? 'selected' : '' ?>>Aug</option>
                    <option value="9" <?= $month === 9 ? 'selected' : '' ?>>Sept</option>
                    <option value="10" <?= $month === 10 ? 'selected' : '' ?>>Oct</option>
                    <option value="11" <?= $month === 11 ? 'selected' : '' ?>>Nov</option>
                    <option value="12" <?= $month === 12 ? 'selected' : '' ?>>Dec</option>
                </select>
                <select name="year" id="year-selector" class="date-filter-selectors">
                    <option value="">Select Year</option>
                    <option value="2026" <?= $year === 2026 ? 'selected' : '' ?>>2026</option>
                    <option value="2027" <?= $month === 2027 ? 'selected' : '' ?>>2027</option>
                    <option value="2028" <?= $month === 2028 ? 'selected' : '' ?>>2028</option>
                </select>

                <button type="button" class="main-btns reset-d-filter-btn" style="padding: 13px 16px;">Reset</button>
            </div>
        </div>
    </section>

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
                            $result_cats = $conn->prepare($sql);
                            $result_cats->bind_param($types, ...$params);
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
                                            <form action="" method="post" class="del-form">
                                                <input type="hidden" name="item_id" id="item_id" value="<?= htmlspecialchars($row['id']) ?>">
                                                <input type="hidden" name="action" id="action" value="delete-expense">
                                                <button class="tbl-actions delete delete-item-btn" id="#delete-exp-btn"><i class="fa-solid fa-trash"></i></button>
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