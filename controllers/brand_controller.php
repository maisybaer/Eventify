<?php

require_once '../classes/brand_class.php';

//add brand controller
function add_brand_ctr($brand_name,$user_id)
{
    $brand = new Brand();
    return $brand->addBrand($brand_name,$user_id);
}

//update brand controller
function update_brand_ctr($brand_id,$brand_name)
{
    $brand = new Brand();
    return $brand->updateBrand($brand_id,$brand_name);
}

//delete brand controller
function delete_brand_ctr($brand_id)
{
    $brand = new Brand();
    return $brand->deleteBrand($brand_id);
}

//get brand controller by user id
function get_brand_ctr($user_id)
{
    $cat = new Brand();
    return $cat->getBrand($user_id);
}

//get all brands
function get_all_brands_ctr()
{
    $brand = new Brand();
    return $brand->getAllBrands();
}