<?php
session_start();
include '../../env/conf.php';
include '../../assets/temps/functions.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Action failed! Restricted access!']);
    exit;
}


$user_id = (int)$_SESSION['user_id'];

function handleDelete($conn, $table, $id, $successMsg, $errorMsg, $fieldName, $fieldType) {
    recordExists($conn, $table, $fieldType, $fieldName, $id);
    $stmt = $conn->prepare("DELETE FROM $table WHERE $fieldName=?");
    $stmt->bind_param("$fieldType", $id);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => $successMsg]);
    } else {
        echo json_encode(['status' => 'error', 'message' => $errorMsg]);
    }
    exit;
}


function handleCatAddition($conn, $cat_name){
    if (empty($cat_name) || $cat_name === '') {
        return [
            'status' => false,
            'message' => 'Please provide the category name'
        ];
    }
    $stmt = $conn->prepare("INSERT INTO categories (name) VALUES (?)");
    $stmt->bind_param("s", $cat_name);
    if (!$stmt->execute()) {
        return [
            'status' => false,
            'message' => 'Ooops! An error occured'
        ];
    }else{
        return [
            'status' => true,
            'message' => 'Category Was added successfully!'
        ];
    }
}


function updateCategory ($conn, $cat_id, $name){
    if (empty($cat_id) || empty($name)) {
        return [
            'status' => false,
            'message' => 'Operation failed! Please fill in all the fields!'
        ];
    }

    $stmt = $conn->prepare("UPDATE categories SET name=? WHERE id=?");
    $stmt->bind_param("si", $name, $cat_id);

    if (!$stmt->execute()) {
        return [
            'status' => false,
            'message' => 'An error occured! Please try again!'
        ];
    }

    return [
            'status' => true,
            'message' => 'Category was updated successfully!'
    ];

}


function addExpense ($conn, $userId, $amount, $catId, $description, $expDate){
    if(empty($userId) || empty($description) || empty($catId) || empty($amount) || $amount <= 0){
        return [
            'status' => false,
            'message' => 'Operation failed! Please fill in all the fields!'
        ];
    }

    $stmt = $conn->prepare("INSERT INTO expenses (user_id, amount, category_id, description, expense_date) VALUES (?,?,?,?,?)");
    $stmt->bind_param("idiss", $userId, $amount, $catId, $description, $expDate);

    if ($stmt->execute()) {
        return [
            'status' => true,
            'message' => 'Expense was inserted successfully!'
        ];
    }else{
        return [
            'status' => false,
            'message' => 'An error occured! Please try again!'
        ];
    }
}

function updateExpense ($conn, $expId, $userId, $amount, $catId, $description, $expDate){
    if(empty($expId) ||empty($userId) || empty($description) || empty($catId) || empty($amount) || $amount <= 0){
        return [
            'status' => false,
            'message' => 'Operation failed! Please fill in all the fields!'
        ];
    }

    recordExists($conn, 'expenses', 'i', 'id', $expId);

    $stmt = $conn->prepare("UPDATE expenses SET amount=?, category_id=?, description=?, expense_date=? WHERE user_id=? AND id=?");
    $stmt->bind_param("dissii", $amount, $catId, $description, $expDate, $userId, $expId);
    
    if (!$stmt->execute()) {
        return [
            'status' => false,
            'message' => 'An error occured! Please try again!'
        ];
    }

    return [
            'status' => true,
            'message' => 'Expense was updated successfully!'
    ];
}




if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if(!empty($_POST['action']) && isset($_POST['action'])){
        
        $action = trim($_POST['action']);
        $actionResult = null;


        if ($action === 'addCat') {
            $catName = clean_input($_POST['cat_name']);
            $actionResult = handleCatAddition($conn, $catName);
            
        } else if ($action === 'addExpense') {
            $expAmount = floatval($_POST['exp_amt']);
            $expDate = clean_input($_POST['exp_date']);
            $expDesc = clean_input($_POST['exp_desc']);
            $expCategory = intval($_POST['exp_cat']);
            $actionResult = addExpense($conn, $user_id, $expAmount, $expCategory, $expDesc, $expDate);

        } elseif ($action === 'updateItem') {
            if (empty($_POST['item_id']) || empty($_POST['entity'])) {
                echo json_encode(['status' => 'error', 'message' => 'Operation Failed! Please refresh the page and try again!']);
                exit;
            }

            $itemId = intval($_POST['item_id']);
            $toUpdate = clean_input($_POST['entity']);

            if ($toUpdate === 'category') {
                $catName = clean_input($_POST['cat_name']);
                $actionResult = updateCategory($conn, $itemId, $catName);
                
            }else if ($toUpdate === 'expense'){
                $expAmount = floatval($_POST['exp_amt']);
                $expDate = clean_input($_POST['exp_date']);
                $expDesc = clean_input($_POST['exp_desc']);
                $expCategory = intval($_POST['exp_cat']);

                $actionResult = updateExpense($conn, $itemId, $user_id, $expAmount, $expCategory, $expDesc, $expDate);

            }

        }


        if ($actionResult !== null) {
            if (!$actionResult['status']) {
                echo json_encode(['status' => 'error', 'message' => $actionResult['message']]);
                exit;
            }

            echo json_encode(['status' => 'success', 'message' => $actionResult['message']]);
            exit;
        }

    }else{
        echo json_encode([
            'status' => 'error',
            'message' => 'An error occured. Please Try Again later'
        ]);
        exit;
    }
    
    
}


?>