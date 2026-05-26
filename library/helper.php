<?php

class CoreHelper {
    public function login($email, $password, $hashedToken, $token, $conn) {
        if (empty($email) || empty($password)) {
            return ['status' => 'error', 'message' => 'Login failed. Please fill in all fields.'];
        }

        $status = 1;
        $stmt = $conn->prepare("SELECT user_id, role, company_id, password FROM users WHERE email=? AND status=?");
        if (!$stmt) {
            return ['status' => 'error', 'message' => 'System error. Please try again later.'];
        }

        $stmt->bind_param("si", $email, $status);
        if (!$stmt->execute()) {
            return ['status' => 'error', 'message' => 'A System error occured. Please Try Again.'];
        }

        $result = $stmt->get_result();
        if ($result->num_rows === 0) {
            return ['status' => 'error', 'message' => 'Email not found or inactive.'];
        }

        $row = $result->fetch_assoc();
        if (!password_verify($password, $row['password'])) {
            return ['status' => 'error', 'message' => 'Incorrect password.'];
        }

        //remember me 

        if ($hashedToken && !empty($hashedToken)) {
            $update = $conn->prepare("UPDATE users SET remember_tok=? WHERE email=?");
            if (!$update) {
                return ['status' => 'error', 'message' => 'System error. Please try again later.'];
            }

            $update->bind_param("ss", $hashedToken, $email);
            if (!$update->execute()) {
                return ['status' => 'error', 'message' => 'A System error occured. Please Try Again.'];
            }    

            setcookie(
                "remember_me",
                $token,
                time() + (86400 * 30),
                "/",
                "",
                true,   // Secure (HTTPS)
                true    
            );
        }
        

        $_SESSION['user_id'] = $row['user_id'];
        $_SESSION['role']    = $row['role'];
        $_SESSION['c_id']    = $row['company_id'];

        return ['status' => 'success', 'message' => 'Login successful. Redirecting...', 'r'=> $row['role']];
    }

    public function addDepartment($department_name, $conn, $action, $dataId=false){
        $msgBack = '';
        if (empty($department_name)) {
            return ['status' => 'error', 'message' => 'Failed to Add Department. Please fill in all fields.'];
        }

        if ($action === 'add') {
            $stmt = $conn->prepare("INSERT INTO tb_departments (name) VALUES (?)"); 
             if (!$stmt) {
                return ['status' => 'error', 'message' => 'System error. Please try again later.'];
            }
            $stmt->bind_param("s", $department_name);  
            $msgBack = 'Department was added successfully'; 
        }else if ($action === 'update'){
            $stmt = $conn->prepare("UPDATE tb_departments SET name=? WHERE d_id=?");
            $stmt->bind_param("si", $department_name, $dataId);
            $msgBack = 'Department updated successfully.';
        }else{
            return ['status' => 'error', 'message' => 'System error. No action was provided.'];
        }

    
        if (!$stmt->execute()) {
            return ['status' => 'error', 'message' => 'A System error occured. Please Try Again.'];
        }else{
            return ['status' => 'success', 'message' => $msgBack];
        }
    }

    public function addPos($posName, $dep_id, $posDesc, $conn, $action, $dataId=false){
        if (empty($posName) || empty($dep_id)) {
            return ['status' => 'error', 'message' => 'Failed to Add Position. Please fill in all fields.'];
        }

        if ($action === 'add') {
            $stmt = $conn->prepare("INSERT INTO positions (title, d_id, pos_desc) VALUES (?,?,?)");
            if (!$stmt) {
                return ['status' => 'error', 'message' => 'System error. Please try again later.'];
            }
            $stmt->bind_param("sis", $posName, $dep_id, $posDesc);    
            $msgBack = 'Position was added successfully';
        }else if($action === 'update'){
            $stmt = $conn->prepare("UPDATE positions SET title=?, d_id=?, pos_desc=? WHERE p_id=?");
            if (!$stmt) {
                return ['status' => 'error', 'message' => 'System error. Please try again later.'];
            }
            $stmt->bind_param("sisi", $posName, $dep_id, $posDesc, $dataId);
            $msgBack = 'Position was Updated successfully';
        }else{
            return ['status' => 'error', 'message' => 'Invalid action specified.'];
        }

        
        if (!$stmt->execute()) {
            return ['status' => 'error', 'message' => 'A System error occured. Please Try Again.'];
        }else{
            return ['status' => 'success', 'message' => $msgBack];
        }
    }

    public function addComp ($c_email, $c_phone, $c_name, $c_location, $c_status, $c_admin, $c_plan, $comp_id, $conn){
        $stmt = $conn->prepare("SELECT * FROM tb_companies WHERE c_email=?");
        if (!$stmt) {
            return ['status' => 'error', 'message' => 'System error. Please try again later.'];
        }

        $stmt->bind_param("s", $c_email);

        if (!$stmt->execute()) {
            return ['status' => 'error', 'message' => 'A System error occured. Please Try Again.'];
        }else{
            $results = $stmt->get_result();

            if ($results->num_rows > 0) {
                return ['status' => 'error', 'message' => 'Failed. A company with the same email address is already registered. Please use another email.'];
            }else{
                $stmt = $conn->prepare("INSERT INTO tb_companies (company_id, c_name, c_email, c_phone, c_admin, c_plan, location, status) VALUES (?,?,?,?,?,?,?,?)");
                $stmt->bind_param("ssssiisi", $comp_id, $c_name, $c_email, $c_phone, $c_admin, $c_plan, $c_location, $c_status);

                if (!$stmt->execute()) {
                    return ['status' => 'error', 'message' => 'A System error occured. Please Try Again.'];
                }else{
                    return ['status' => 'success', 'message' => 'Company was added successfully'];
                }
            }
        }
    }

    public function addStation($conn, $s_id, $s_name, $latitude, $longitude, $radius, $action, $dataId = false)
{
        if ($action === 'add') {
            $checkStmt = $conn->prepare("SELECT * FROM stations WHERE latitude = ? AND longitude = ?");
            if (!$checkStmt) {
                return ['status' => 'error', 'message' => 'System error. Please try again later.'];
            }

            $checkStmt->bind_param("dd", $latitude, $longitude);
            if (!$checkStmt->execute()) {
                return ['status' => 'error', 'message' => 'A system error occurred. Please try again.'];
            }

            $results = $checkStmt->get_result();
            if ($results->num_rows > 0) {
                return ['status' => 'error', 'message' => 'Failed. A station with the same latitude and longitude already exists.'];
            }

            $checkStmt->close();

            $stmt = $conn->prepare("INSERT INTO stations (station_id, name, latitude, longitude, m_radius) VALUES (?, ?, ?, ?, ?)");
            if (!$stmt) {
                return ['status' => 'error', 'message' => 'System error. Please try again later.'];
            }

            $stmt->bind_param("ssddi", $s_id, $s_name, $latitude, $longitude, $radius);
            $msgBack = 'Station was added successfully';

        } elseif ($action === 'update') {
            $stmt = $conn->prepare("UPDATE stations SET name = ?, latitude = ?, longitude = ?, m_radius = ? WHERE station_id = ?");
            if (!$stmt) {
                return ['status' => 'error', 'message' => 'System error. Please try again later.'];
            }

            $stmt->bind_param("sddis", $s_name, $latitude, $longitude, $radius, $dataId);
            $msgBack = 'Station was updated successfully';

        } else {
            return ['status' => 'error', 'message' => 'Invalid action specified.'];
        }

        if (!$stmt->execute()) {
            return ['status' => 'error', 'message' => 'A system error occurred. Please try again.'];
        }

        $stmt->close();
        return ['status' => 'success', 'message' => $msgBack];
    }       

}
?>
