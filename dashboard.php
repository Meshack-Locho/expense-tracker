<?php
require_once 'env/conf.php';
require_once 'bootstrap.php';

if(!isset($_SESSION['user_id'])){
    header('Location: '.WEB_URL.'');
}

require_once 'header.php';

$userId = (int)$_SESSION['user_id'];
function getRecentExpenses($conn, $user){
    $userId = (int)$user;
    $stmt = $conn->prepare("SELECT amount, category_id, description, expense_date FROM expenses WHERE user_id=?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if($result->num_rows === 0){
        return [
            'success' => false,
            'data' => []
        ];
    }

    $row = $result->fetch_assoc();

    return [
        'success' => true,
        'data' => $row
    ];


}

?>

<main class="main-adm main-dashboard-content">

    <section class="dash-sect full-sect flex-col">
        <div class="sect-header flex-row">
            <i class="fa-solid fa-thumbtack"></i>
            <h3>Quick Overview</h3>
        </div>
        <div class="sect-body">
            <div class="data-cards-home flex-row">
                <div class="data-card">
                    <div class="top flex-row">
                        <div class="left">
                            <h4>Income</h4>
                            <h3 class="main-dat"><?php
                              echo 'KES 22000'
                            ?></h3>
                        </div>
                        <i class="fa-solid fa-boxes-stacked"></i>
                    </div>
                    <a href="orders/order_history" class="bottom">More Info</a>
                </div>
                <div class="data-card">
                    <div class="top flex-row">
                        <div class="left">
                            <h4>Spent</h4>
                            <h3 class="main-dat"><?php
                              echo "KES 12000";
                            ?></h3>
                        </div>
                        <i class="fa-solid fa-circle-exclamation" style="color: red;"></i>
                    </div>
                    <a href="orders/pending_approvals" class="bottom">More Info</a>
                </div>

                <div class="data-card">
                    <div class="top flex-row">
                        <div class="left">
                            <h4>Remaining</h4>
                            <h3 class="main-dat"><?php
                              echo "KES 10000";
                            ?></h3>
                        </div>
                        <i class="fa-solid fa-circle-check" style="color: green;"></i>
                    </div>
                    <a href="orders/approved_orders" class="bottom">More Info</a>
                </div>

                <div class="data-card">
                    <div class="top flex-row">
                        <div class="left">
                            <h4>Savings</h4>
                            <h3 class="main-dat"><?php
                              echo "KES 50000";
                            ?></h3>
                        </div>
                        <i class="fa-solid fa-circle-check" style="color: green;"></i>
                    </div>
                    <a href="orders/approved_orders" class="bottom">More Info</a>
                </div>

                <div class="data-card">
                    <div class="top flex-row">
                        <div class="left">
                            <h4>Top Category</h4>
                            <h3 class="main-dat"><?php
                              echo "Food (KES 4000)";
                            ?></h3>
                        </div>
                        <i class="fa-solid fa-circle-check" style="color: green;"></i>
                    </div>
                    <a href="orders/approved_orders" class="bottom">More Info</a>
                </div>

                <div class="data-card">
                    <div class="top flex-row">
                        <div class="left">
                            <h4>Last Expense</h4>
                            <h3 class="main-dat"><?php
                              echo "Milk (KES 30)";
                            ?></h3>
                        </div>
                        <i class="fa-solid fa-circle-check" style="color: green;"></i>
                    </div>
                    <a href="orders/approved_orders" class="bottom">More Info</a>
                </div>
    

            </div>
        </div>
        <div class="bottom"></div>
    </section>

    


    <section class="dash-sect recent-employee-add">
      <div class="sect-header flex-row">
        <i class="fa-solid fa-boxes-stacked"></i>
        <h3>Recent Expenses</h3>
      </div>
      <div class="sect-body leave-table">
        <table class="tables table table-bordered table-striped dt-responsive">
            <thead>
              <tr>
                <th>Expense</th>
                <th>Date</th>
                <th>Amount</th>
                <th>Description</th>
              </tr>
            </thead>
            <tbody>
              <?php
                $expenses = getRecentExpenses($conn, $userId);
                $expensesArray = $expenses['data'];
                foreach ($expensesArray as $expense) { ?>
                    <tr>
                          <td><?= htmlspecialchars($expense['category_id']) ?></td>
                          <td><?= htmlspecialchars($expense['date']) ?></td>
                          <td><?= 'KES ' . htmlspecialchars(number_format($expense['amount'])) ?></td>
                          <td><?= 'KES ' . htmlspecialchars($expense['description']) ?></td>
                        </tr>
                <?php }
              ?>
              
              
            </tbody>
          </table>
          
          <a href="<?= htmlspecialchars(WEB_URL) ?>orders/order_history" class="main-btns small">See all</a>
      </div>
    </section>
</main>


<?php include 'footer.php';?>