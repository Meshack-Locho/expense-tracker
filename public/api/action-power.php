<?php
include '../../env/conf.php';
include '../../assets/temps/functions.php';
header('Content-Type: application/json');


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



if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if(!empty($_POST['action']) && isset($_POST['action'])){
        if (empty($_POST['item_id']) && !isset($_POST['item_id'])) {
            $response = ['status' => 'error', 'message' => 'Operation Failed. Some Parameters are missing. Please refresh the page and try again!'];
            echo json_encode($response);
            exit;
        }
        if (is_numeric($_POST['item_id'])) {
            $itemId = intval($_POST['item_id']);
        }else if(is_string($_POST['item_id'])){
            $itemId = clean_input($_POST['item_id']);
        }else{
            echo json_encode(['status' => 'error', 'message' => 'Failed. Item type is invalid']);
            exit;
        }
        
        
        $action = trim($_POST['action']);

        if ($action === 'delete-category') {
            handleDelete($conn, 'categories', $itemId, 'The category was deleted successfully!', 'Category not deleted! Please Try Again', 'id', 'i');
        } else if ($action === 'delete-expense') {
            handleDelete($conn, 'expenses', $itemId, 'Expense was deleted successfully!', 'Expense not deleted! Please Try Again', 'id', 'i');
        } else{
            echo json_encode(['status' => 'error', 'message' => 'Action failed. Some parameters are missing. Please refresh the page and try again']);
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