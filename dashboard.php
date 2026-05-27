<?php
require_once 'env/conf.php';
require_once 'bootstrap.php';

if(!isset($_SESSION['user_id'])){
    header('Location: '.WEB_URL.'');
}

require_once 'header.php';

$userId = (int)$_SESSION['user_id'];
$year = 2026;
$month = 5;

$start = date("Y-m-01", strtotime("$year-$month-01"));
$end = date("Y-m-t", strtotime("$year-$month-01"));


function getRecentExpenses($conn, $user){
    $userId = (int)$user;
    $stmt = $conn->prepare("SELECT e.id, e.category_id, e.amount, e.expense_date, e.description, c.name AS category_name FROM expenses e LEFT JOIN categories c ON e.category_id=c.id WHERE e.user_id=?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if($result->num_rows === 0){
        return [
            'success' => false,
            'data' => []
        ];
    }

    $row = $result->fetch_all(MYSQLI_ASSOC);

    return [
        'success' => true,
        'data' => $row
    ];


}

function getMonthlyIncome ($conn, $user_id, $startDate, $EndDate){
    $stmt = $conn->prepare("SELECT COALESCE(SUM(amount), 0) AS total_income
        FROM income
        WHERE user_id = ?
        AND income_date BETWEEN ? AND ?;");
    $stmt->bind_param("iss", $user_id, $startDate, $EndDate);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        return [
            'success' => false,
            'data' => 0
        ];
    }

    $row = $result->fetch_assoc();
    $income = (int)$row['total_income'];

    return [
        'success' => true,
        'data' => $income
    ];

}


function getSpent ($conn, $user_id, $startDate, $endDate){
    $stmt = $conn->prepare("SELECT COALESCE(SUM(amount), 0) AS total_expenses
            FROM expenses
            WHERE user_id = ?
            AND expense_date BETWEEN ? AND ?;");
    $stmt->bind_param("iss", $user_id, $startDate, $endDate);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        return [
            'success' => false,
            'data' => 0
        ];
    }

    $row = $result->fetch_assoc();
    $spent = (int)$row['total_expenses'];

    return [
        'success' => true,
        'data' => $spent
    ];
}


function getTopExpense ($conn, $user_id, $startDate, $endDate){
    $stmt = $conn->prepare("SELECT
        c.name,
        SUM(e.amount) AS total
        FROM expenses e
        LEFT JOIN categories c ON e.category_id = c.id
        WHERE e.user_id = ?
        AND e.expense_date BETWEEN ? AND ?
        GROUP BY e.category_id
        ORDER BY total DESC
        LIMIT 1");
    $stmt->bind_param("iss", $user_id, $startDate, $endDate);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        return [
            'success' => false,
            'category' => '',
            'total_amt' => 0
        ];
    }

    $row = $result->fetch_assoc();
    $catName = $row['name'];
    $amtSpent = $row['total'];

    return [
        'success' => true,
        'category' => $catName,
        'total_amt' => $amtSpent
    ];
}


function getLastExpense ($conn, $user_id, $startDate, $endDate){
    $stmt = $conn->prepare("SELECT
        c.name,
        e.amount,
        e.description
        FROM expenses e
        LEFT JOIN categories c ON e.category_id = c.id
        WHERE e.user_id = ?
        AND e.expense_date BETWEEN ? AND ?
        GROUP BY e.category_id
        ORDER BY e.expense_date DESC
        LIMIT 1");
    $stmt->bind_param("iss", $user_id, $startDate, $endDate);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        return [
            'success' => false,
            'category' => '',
            'total_amt' => 0,
            'descrption' => ''
        ];
    }

    $row = $result->fetch_assoc();
    $catName = $row['name'];
    $amtSpent = $row['amount'];
    $description = $row['description'];

    return [
        'success' => true,
        'category' => $catName,
        'total_amt' => $amtSpent,
        'descrption' => $description
    ];
}

$totalIncome = getMonthlyIncome($conn, $userId, $start, $end);
$totalExpenses = getSpent($conn, $userId, $start, $end);
$topExpData = getTopExpense($conn, $userId, $start, $end);
$lastExp = getLastExpense($conn, $userId, $start, $end);

//FINANCIALS 
//MAIN
$totalIncomeFig = $totalIncome['data'];
$totalExpensesFig = $totalExpenses['data'];
$balance = (int)$totalIncomeFig - (int)$totalExpensesFig;

//TOP SPENT

$topExpName = $topExpData['category'];
$topExpAmt = $topExpData['total_amt'];

//LAST EXPENSE
$lastExpCat = $lastExp['category'];
$lastExpAmt = $lastExp['total_amt'];
$lastExpDesc = $lastExp['descrption'];

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
                            <span class="main-dat">
                                <?= htmlspecialchars("KES " . number_format($totalIncomeFig)) ?>
                            </span>
                        </div>
                        <i class="fa-solid fa-money-bill"></i>
                    </div>
                    <a href="orders/order_history" class="bottom">More Info</a>
                </div>
                <div class="data-card">
                    <div class="top flex-row">
                        <div class="left">
                            <h4>Spent</h4>
                            <span class="main-dat">
                                <?= htmlspecialchars("KES " . number_format($totalExpensesFig)) ?>
                            </span>
                        </div>
                        <i class="fa-solid fa-arrow-right-from-bracket"></i>
                    </div>
                    <a href="exp/expenses" class="bottom">More Info</a>
                </div>

                <div class="data-card">
                    <div class="top flex-row">
                        <div class="left">
                            <h4>Remaining</h4>
                            <span class="main-dat">
                                <?= htmlspecialchars("KES " . number_format($balance)) ?>
                            </span>
                        </div>
                        <i class="fa-solid fa-hand-holding-dollar" style="color: <?php if((int)$balance > 0){echo 'green';}else{echo 'red';}?>"></i>
                    </div>
                    <a href="orders/approved_orders" class="bottom">More Info</a>
                </div>

                <div class="data-card">
                    <div class="top flex-row">
                        <div class="left">
                            <h4>Savings</h4>
                            <span class="main-dat"><?php
                              echo "KES 50000";
                            ?></span>
                        </div>
                        <i class="fa-solid fa-circle-check" style="color: green;"></i>
                    </div>
                    <a href="orders/approved_orders" class="bottom">More Info</a>
                </div>

                <div class="data-card">
                    <div class="top flex-row">
                        <div class="left">
                            <h4>Top Category</h4>
                            <span class="main-dat">
                                <?= htmlspecialchars($topExpName . ' (KES ' . number_format($topExpAmt) . ")") ?>
                            </span>
                        </div>
                        <i class="fa-solid fa-arrow-trend-up" style="color: purple;"></i>
                    </div>
                    <a href="exp/expenses" class="bottom">More Info</a>
                </div>

                <div class="data-card">
                    <div class="top flex-row">
                        <div class="left">
                            <h4>Last Expense</h4>
                            <span class="main-dat">
                                <?= htmlspecialchars($lastExpCat . ": " . number_format($lastExpAmt)) ?>
                                <small style="font-size: 13px;"><?= htmlspecialchars('(' . $lastExpDesc . ')') ?></small>
                            </span>
                        </div>
                        <i class="fa-solid fa-clock-rotate-left"></i>
                    </div>
                    <a href="exp/expenses" class="bottom">More Info</a>
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
                <th>Description</th>
                <th>Date</th>
                <th>Amount</th>
              </tr>
            </thead>
            <tbody>
              <?php
                $expenses = getRecentExpenses($conn, $userId);
                $expensesArray = $expenses['data'];
                foreach ($expensesArray as $expense) { ?>
                    <tr>
                          <td><?= htmlspecialchars($expense['category_name']) ?></td>
                          <td><?= htmlspecialchars($expense['description']) ?></td>
                          <td><?= htmlspecialchars($expense['expense_date']) ?></td>
                          <td><?= 'KES ' . htmlspecialchars(number_format($expense['amount'])) ?></td>
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