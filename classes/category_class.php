<?php

require_once '../settings/db_class.php';

class Category extends db_connection
{

    public function __construct()
    {
        parent::db_connect();
    }

    //function to add category
    public function addCat($cat_name, $user_id)
    {
        $stmt = $this->db->prepare("INSERT INTO eventify_categories (cat_name,added_by) values(?,?)");
        $stmt->bind_param("si",$cat_name,$user_id);
        return $stmt->execute();
    }

    //function to update category
    public function updateCat($cat_id,$cat_name)
    {
    $stmt = $this->db->prepare("UPDATE eventify_categories SET cat_name = ? WHERE cat_id = ?");
    $stmt->bind_param("si", $cat_name, $cat_id);
    return $stmt->execute();
    }

    //function to delete customer
    public function deleteCat($cat_id)
    {
        $stmt = $this->db->prepare("DELETE FROM eventify_categories WHERE cat_id=?");
        $stmt->bind_param("i",$cat_id);
        return $stmt->execute();
    }

    //function to get categories based on user ID
    public function getCat($user_id)
    {

        $stmt = $this->db->prepare("SELECT * FROM eventify_categories WHERE added_by = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);

        }


    //function to get all categories
    public function getAllCat()
    {
        $stmt = $this->db->prepare("SELECT * FROM eventify_categories");
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);

        }


}