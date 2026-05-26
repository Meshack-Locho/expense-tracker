<?php
/**
 * audit log or activity
 * 
 * @param string $userType 'customer' | 'admin'
 * @param int|string $userId ID of user
 * @param string $action Description of the action
 * @param array|null $meta Optional extra data (order_id, invoice_no, product_id, etc.)
 */

function add_audit_log($userType, $userId, $action, $meta = null) {

    $logFile = BASE_PATH . '/data/logs.json';

    // file exists
    if (!file_exists($logFile)) {
        file_put_contents($logFile, json_encode([]), LOCK_EX);
    }

    // existing logs
    $logs = json_decode(file_get_contents($logFile), true);

    if (!is_array($logs)) {
        $logs = [];
    }

    // New log entry
    $logs[] = [
        'timestamp' => date('Y-m-d H:i:s'),
        'user_type' => $userType,
        'user_id' => $userId,
        'action' => $action,
        'meta' => $meta
    ];

    
    file_put_contents($logFile, json_encode($logs, JSON_PRETTY_PRINT), LOCK_EX);
}