<?php

require_once '../settings/db_class.php';

class Event extends db_connection
{

    public function __construct()
    {
        parent::db_connect();
    }



    //function to add events
    public function addEvent($eventCat, $eventDes, $eventPrice, $eventLocation, $eventStart, $eventEnd,  $flyer, $eventKey, $user_id)
    {
        // Align with DB schema: eventify_products columns are
        // event_cat, event_desc, event_price, event_location, event_start, event_end, flyer, event_keywords, added_by
        $stmt = $this->db->prepare("INSERT INTO eventify_products(event_cat, event_desc, event_price, event_location, event_start, event_end, flyer, event_keywords, added_by) VALUES (?,?,?,?,?,?,?,?,?)");
        $stmt->bind_param("isdsssssi", $eventCat, $eventDes, $eventPrice, $eventLocation, $eventStart, $eventEnd, $flyer, $eventKey, $user_id);
        return $stmt->execute();
    }

    //function to update events
    public function updateEvent($event_id, $eventCat, $eventDes, $eventPrice, $eventLocation, $eventStart, $eventEnd,  $flyer, $eventKey)
    {
        if (!empty($flyer)) {
            $stmt = $this->db->prepare("UPDATE eventify_products SET event_cat = ?, event_desc = ?, event_price = ?, event_location = ?, event_start = ?, event_end = ?, flyer = ?, event_keywords = ? WHERE event_id = ?");
            $stmt->bind_param("isdsssssi", $eventCat, $eventDes, $eventPrice, $eventLocation, $eventStart, $eventEnd, $flyer, $eventKey, $event_id);
        } else {
            $stmt = $this->db->prepare("UPDATE eventify_products SET event_cat = ?, event_desc = ?, event_price = ?, event_location = ?, event_start = ?, event_end = ?, event_keywords = ? WHERE event_id = ?");
            $stmt->bind_param("isdssssi", $eventCat, $eventDes, $eventPrice, $eventLocation, $eventStart, $eventEnd, $eventKey, $event_id);
        }
        return $stmt->execute();
    }

    //function to delete event
    public function deleteEvent($event_id)
    {
        $stmt = $this->db->prepare("DELETE FROM eventify_products WHERE event_id=?");
        $stmt->bind_param("i",$event_id);
        return $stmt->execute();
    }

    //function to get event based on user ID
    public function getEvent($user_id)
    {
        // Return event rows including both the category/brand names and their IDs
        $stmt = $this->db->prepare("SELECT 
            p.event_id,  p.event_cat, 
            c.cat_name AS category,
            p.event_desc, p.event_price, p.event_location, p.event_start, p.event_end, p.flyer, p.event_keywords, p.added_by
        FROM eventify_products p
        JOIN eventify_categories c ON p.event_cat = c.cat_id
        WHERE p.added_by = ?");

        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }


    //view all products function
    public function viewAllEvent(){
        $stmt = $this->db->prepare("SELECT 
            p.event_id, p.event_cat, 
            c.cat_name AS category, 
            p.event_desc, p.event_price, p.event_location, p.event_start, p.event_end, p.flyer, p.event_keywords, p.added_by FROM eventify_products p
        JOIN eventify_categories c ON p.event_cat = c.cat_id");

        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    //search products function
    public function search($query){
        $like = "%{$query}%";
        $stmt = $this->db->prepare("SELECT 
            p.event_id, p.event_cat, 
            c.cat_name AS category, 
            p.event_desc, p.event_price, p.event_location, p.event_start, p.event_end, p.flyer, p.event_keywords, p.added_by
        FROM eventify_products p
        JOIN eventify_categories c ON p.event_cat = c.cat_id
        WHERE p.event_desc LIKE ? OR p.event_keywords LIKE ?");

        $stmt->bind_param("ss", $like, $like);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }


    //filter events function by category function
    public function filterByCat($cat_id){
        $stmt = $this->db->prepare("SELECT 
            p.event_id, p.event_cat, 
            c.cat_name AS category,
                p.event_desc, p.event_price, p.event_location, p.event_start, p.event_end, p.flyer, p.event_keywords, p.added_by
        FROM eventify_products p
        JOIN eventify_categories c ON p.event_cat = c.cat_id
        WHERE p.event_cat = ?");

        $stmt->bind_param("i", $cat_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    //view single event function
    public function viewSingleEvent($event_id){
        $stmt = $this->db->prepare("SELECT 
            p.event_id, p.event_cat,
            c.cat_name AS category,
            p.event_desc, p.event_price, p.event_location, p.event_start, p.event_end, p.flyer, p.event_keywords, p.added_by
        FROM eventify_products p
        JOIN eventify_categories c ON p.event_cat = c.cat_id
        WHERE p.event_id = ?");

        $stmt->bind_param("i", $event_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }
}