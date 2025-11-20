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

    // Fetch events; if $user_id provided, only that user's events
    public function getEvent($user_id = null)
    {
        $sql = "SELECT e.*, c.cat_name AS category FROM events e LEFT JOIN categories c ON e.event_cat = c.cat_id";
        if ($user_id) {
            $sql .= " WHERE e.added_by = " . intval($user_id);
        }
        $sql .= " ORDER BY e.event_id DESC";
        $rows = $this->db_fetch_all($sql);
        return $rows ?: [];
    }

    public function viewAllEvents()
    {
        return $this->getEvent(null);
    }

    public function viewSingleEvent($event_id)
    {
        $sql = "SELECT e.*, c.cat_name AS category FROM events e LEFT JOIN categories c ON e.event_cat = c.cat_id WHERE e.event_id = " . intval($event_id) . " LIMIT 1";
        return $this->db_fetch_one($sql);
    }

    public function filterByCategory($cat_id)
    {
        $sql = "SELECT e.*, c.cat_name AS category FROM events e LEFT JOIN categories c ON e.event_cat = c.cat_id WHERE e.event_cat = " . intval($cat_id) . " ORDER BY e.event_id DESC";
        $rows = $this->db_fetch_all($sql);
        return $rows ?: [];
    }

    public function filterByDate($date)
    {
        $sql = "SELECT e.*, c.cat_name AS category FROM events e LEFT JOIN categories c ON e.event_cat = c.cat_id WHERE e.event_date = '" . mysqli_real_escape_string($this->db, $date) . "' ORDER BY e.event_id DESC";
        $rows = $this->db_fetch_all($sql);
        return $rows ?: [];
    }

    public function updateEvent($event_id, $event_name, $event_desc, $event_location, $event_date, $event_start, $event_end, $flyer, $event_cat)
    {
        if (!$this->db) $this->db_connect();
        $stmt = $this->db->prepare("UPDATE events SET event_name = ?, event_desc = ?, event_location = ?, event_date = ?, event_start = ?, event_end = ?, flyer = ?, event_cat = ? WHERE event_id = ?");
        if (!$stmt) return false;
        $stmt->bind_param('sssssssii', $event_name, $event_desc, $event_location, $event_date, $event_start, $event_end, $flyer, $event_cat, $event_id);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    public function deleteEvent($event_id)
    {
        if (!$this->db) $this->db_connect();
        $stmt = $this->db->prepare("DELETE FROM events WHERE event_id = ?");
        if (!$stmt) return false;
        $stmt->bind_param('i', $event_id);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    /**
     * Search events by text across multiple fields
     */
    public function searchEvents($query)
    {
        $query = trim($query);
        if (empty($query)) {
            return $this->viewAllEvents();
        }
        
        $search_term = "%" . mysqli_real_escape_string($this->db, $query) . "%";
        
        $sql = "SELECT e.*, c.cat_name AS category 
                FROM events e 
                LEFT JOIN categories c ON e.event_cat = c.cat_id 
                WHERE e.event_name LIKE '$search_term' 
                   OR e.event_desc LIKE '$search_term' 
                   OR e.event_location LIKE '$search_term' 
                   OR c.cat_name LIKE '$search_term'
                ORDER BY e.event_date DESC, e.event_id DESC";
        
        $rows = $this->db_fetch_all($sql);
        return $rows ?: [];
    }

    /**
     * Search events with category filter
     */
    public function searchEventsWithCategory($query, $category_id)
    {
        $query = trim($query);
        $category_id = intval($category_id);
        
        if (empty($query)) {
            return $this->filterByCategory($category_id);
        }
        
        $search_term = "%" . mysqli_real_escape_string($this->db, $query) . "%";
        
        $sql = "SELECT e.*, c.cat_name AS category 
                FROM events e 
                LEFT JOIN categories c ON e.event_cat = c.cat_id 
                WHERE (e.event_name LIKE '$search_term' 
                       OR e.event_desc LIKE '$search_term' 
                       OR e.event_location LIKE '$search_term') 
                   AND e.event_cat = $category_id
                ORDER BY e.event_date DESC, e.event_id DESC";
        
        $rows = $this->db_fetch_all($sql);
        return $rows ?: [];
    }

    /**
     * Get upcoming events (future dates)
     */
    public function getUpcomingEvents($limit = null)
    {
        $today = date('Y-m-d');
        $sql = "SELECT e.*, c.cat_name AS category 
                FROM events e 
                LEFT JOIN categories c ON e.event_cat = c.cat_id 
                WHERE e.event_date >= '$today' 
                ORDER BY e.event_date ASC, e.event_start ASC";
        
        if ($limit) {
            $sql .= " LIMIT " . intval($limit);
        }
        
        $rows = $this->db_fetch_all($sql);
        return $rows ?: [];
    }

    /**
     * Get events by location
     */
    public function getEventsByLocation($location)
    {
        $location = mysqli_real_escape_string($this->db, $location);
        $sql = "SELECT e.*, c.cat_name AS category 
                FROM events e 
                LEFT JOIN categories c ON e.event_cat = c.cat_id 
                WHERE e.event_location LIKE '%$location%' 
                ORDER BY e.event_date DESC, e.event_id DESC";
        
        $rows = $this->db_fetch_all($sql);
        return $rows ?: [];
    }
}
