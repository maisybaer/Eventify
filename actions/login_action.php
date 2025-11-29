<?php

header('Content-Type: application/json');
session_start();

require_once '../classes/customer_class.php';
require_once '../controllers/customer_controller.php';

// Check if the user is already logged in and redirect to the dashboard
if (isset($_SESSION['user_id'])) {
    $response['status'] = 'redirect';
    $response['message'] = 'You are already logged in';
    $response['redirect_url'] = '../index.php';
    echo json_encode($response);
    exit();
}

$email = $_POST['email'];
$password = $_POST['password'];

//Checks if user inputs are typed in
if(empty($email)||empty($password)){
    $response['status']='error';
    $response['message']='Please type your email and password';
    echo json_encode($response);
    exit();
}

//logs in user
$user = login_user_ctr($email, $password);


if ($user) {
    $_SESSION['user_id']=$user['customer_id'];
    $_SESSION['name']=$user['customer_name'];
    $_SESSION['role']=$user['user_role'];

    $response['status'] = 'success';
    $response['message'] = 'Login successfully';
    $response['user'] = [
        'id'=>$user['customer_id'],
        'name'=>$user['customer_name'],
        'email'=>$user['customer_email'],
        'role'=>$user['user_role']
    ];
} else {
    $response['status'] = 'error';
    $response['message'] = 'Invalid email or password';
}

echo json_encode($response);