<?php

require_once '../settings/db_class.php';

/**
 * 
 */
class Customer extends db_connection
{

    public function __construct()
    {
        parent::db_connect();
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    //function to add customer
    public function addCustomer($name,$email, $password,$country,$city,$phone_number,$role,$user_image)
    {
        $hashpassword=password_hash($password, PASSWORD_DEFAULT);
            $stmt = $this->db->prepare("INSERT INTO eventify_customer (customer_name,customer_email,customer_pass,customer_country,customer_city,customer_contact,user_role,customer_image) values(?,?,?,?,?,?,?,?)");
            $stmt->bind_param("ssssssis", $name, $email, $hashpassword, $country, $city, $phone_number, $role, $user_image);
            $ok = $stmt->execute();
            if ($ok) {
                return $this->last_insert_id();
            }
            return false;
    }

    //function to edit customer
    public function editCustomer($user_id,$name,$email,$country,$city,$phone_number,$role,$user_image)
    {
        $stmt = $this->db->prepare("UPDATE eventify_customer SET customer_name=?,customer_email=?,customer_country=?,customer_city=?,customer_contact=?,user_role=?,customer_image=? WHERE customer_id=?");
            $stmt->bind_param("ssssisi", $name, $email, $country, $city, $phone_number, $role, $user_image, $user_id);
            return $stmt->execute();
    }
    //function to delete customer
    public function deleteCustomer($user_id)
    {
        $stmt = $this->db->prepare("DELETE FROM eventify_customer WHERE customer_id=?");
        $stmt->bind_param("i",$user_id);
        return $stmt->execute();
    }

    //function to get customer via email
    private function getCustomerByEmail($email)
    {

        $stmt = $this->db->prepare("SELECT * FROM eventify_customer WHERE customer_email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result;

        }

    //function to get customer name
    private function getUserName($user_id)
    {

        $stmt = $this->db->prepare("SELECT customer_name FROM eventify_customer WHERE customer_id = ?");
        $stmt->bind_param("s", $user_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result;

        }

    //function to get customer via id
    public function getUserId()
    {
        if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) {
            return $_SESSION['user_id'];
        }
        return null; // Return null if not logged in
    }

     //function to verify login by checking
    public function verifyLogin($email, $password)
    {
        $user = $this->getCustomerByEmail($email);

        if ($user && password_verify($password,$user['customer_pass'])) {
            return $user;
        }
        return false;
    }

}