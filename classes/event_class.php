<?php

require_once '../settings/db_class.php';

class Event extends db_connection
{
    public function __construct()
    {
        parent::db_connect();
    }

    /*===========================================
        ADD EVENT
    =============================================*/
    public function addEvent($event_name, $event_desc, $event_location, $event_date, $event_start, $event_end, $flyer, $event_cat, $user_id)
    {
        $stmt = $this->db->prepare("
            INSERT INTO events 
            (event_name, event_desc, event_location, event_date, event_start, event_end, flyer, event_cat, added_by) 
            VALUES (?,?,?,?,?,?,?,?,?)
        ");

        // Bind parameters: all strings except event_cat and user_id (integers)
        $stmt->bind_param(
            "sssssssii",
            $event_name,
            $event_desc,
            $event_location,
            $event_date,
            $event_start,
            $event_end,
            $flyer,
            $event_cat,
            $user_id
        );

        return $stmt->execute();
    }
}
