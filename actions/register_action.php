<?php

header('Content-Type: application/json');

session_start();

$response = array();


require_once '../controllers/customer_controller.php';
require_once '../helpers/upload_helper.php';

//recieve data
$name = $_POST['name'];
$email = $_POST['email'];
$password = $_POST['password'];
$country=$_POST['country'];
$city=$_POST['city'];
$phone_number = $_POST['phone_number'];
$user_image=null;
$role = $_POST['role'];

if (empty($name)||empty($email)||empty($password)||empty($country)||empty($phone_number)||empty($role))
    {
    $response['status'] = 'error';
    $response['message'] = 'Please fill in all fields!';
    echo json_encode($response);
    exit();
}


// For image upload - using remote upload API
if (isset($_FILES['user_image']) && $_FILES['user_image']['error'] === UPLOAD_ERR_OK) {
    $allowedExts = ['jpg', 'jpeg', 'png', 'gif'];
    $uploadResult = upload_file_to_api($_FILES['user_image'], $allowedExts);

    if ($uploadResult['success']) {
        // Store the full URL returned from the API
        $user_image = $uploadResult['url'];
    } else {
        $response['status'] = 'error';
        $response['message'] = $uploadResult['error'];
        echo json_encode($response);
        exit();
    }
}




//call register_user_ctr & return message
$user_id = register_user_ctr($name, $email, $password, $country, $city, $phone_number, $role, $user_image);

if ($user_id) {
    $response['status'] = 'success';
    $response['message'] = 'Registered successfully';
    $response['user_id'] = $user_id;

    // If this user is a vendor (role == 2), create a vendor record in `eventify_vendor`
    if ((int)$role === 2) {
        require_once '../settings/db_class.php';
        $dbconn = new db_connection();
        $dbconn->db_connect();
        $mysqli = $dbconn->db;
        if ($mysqli) {
            $stmt = $mysqli->prepare("INSERT INTO eventify_vendor (vendor_desc, vendor_type) VALUES (?, ?)");
            if ($stmt) {
                $vendor_desc = $name;
                $vendor_type = 'default';
                $stmt->bind_param('ss', $vendor_desc, $vendor_type);
                $ok = $stmt->execute();
                if ($ok) {
                    $response['vendor_id'] = $mysqli->insert_id;
                }
            }
        }
    }
} else {
    $response['status'] = 'error';
    $response['message'] = 'Failed to register. See register_customer_action.php_register_user_ctr';
}

echo json_encode($response);