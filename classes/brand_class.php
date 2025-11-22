<?php

require_once '../settings/db_class.php';

class Vendor extends db_connection
{

    public function __construct()
    {
        parent::db_connect();
    }

    //function to add brand
    public function addBrand($brand_name, $user_id)
    {
        $stmt = $this->db->prepare("INSERT INTO brands(brand_name,added_by) values(?,?)");
        $stmt->bind_param("si",$brand_name,$user_id);
        return $stmt->execute();
    }

    //function to update brand
    public function updateBrand($brand_id,$brand_name)
    {
    $stmt = $this->db->prepare("UPDATE brands SET brand_name = ? WHERE brand_id = ?");
    $stmt->bind_param("si", $brand_name, $brand_id);
    return $stmt->execute();
    }

    //function to delete brand
    public function deleteBrand($brand_id)
    {
        $stmt = $this->db->prepare("DELETE FROM brands WHERE brand_id=?");
        $stmt->bind_param("i",$brand_id);
        return $stmt->execute();
    }

    //function to get brands based on user ID
    public function getBrand($user_id)
    {

        $stmt = $this->db->prepare("SELECT * FROM brands WHERE added_by = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);

        }


    //function to get all brands
    public function getAllBrands()
    {
        $stmt = $this->db->prepare("SELECT * FROM brands");
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);

        }



}