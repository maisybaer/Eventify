<?php

require_once '../classes/event_class.php';

// Add event controller
function add_event_ctr($event_name, $event_desc, $event_location, $event_date, $event_start, $event_end, $flyer, $event_cat, $user_id)
{
    $event = new Event();
    return $event->addEvent($event_name, $event_desc, $event_location, $event_date, $event_start, $event_end, $flyer, $event_cat, $user_id);
}

// Update event controller
function update_event_ctr($event_id, $event_name, $event_desc, $event_location, $event_date, $event_start, $event_end, $flyer, $event_cat)
{
    $event = new Event();
    return $event->updateEvent($event_id, $event_name, $event_desc, $event_location, $event_date, $event_start, $event_end, $flyer, $event_cat);
}

// Delete event controller
function delete_event_ctr($event_id)
{
    $event = new Event();
    return $event->deleteEvent($event_id);
}

// Get events created by a specific user
function get_event_ctr($user_id)
{
    $event = new Event();
    return $event->getEvent($user_id);
}

// View all events controller
function view_all_events_ctr()
{
    $event = new Event();
    return $event->viewAllEvents();
}

// Filter events by category
function filter_by_category_ctr($cat_id)
{
    $event = new Event();
    return $event->filterByCategory($cat_id);
}

// Filter events by date
function filter_by_date_ctr($date)
{
    $event = new Event();
    return $event->filterByDate($date);
}

// View single event
function view_single_event_ctr($event_id)
{
    $event = new Event();
    return $event->viewSingleEvent($event_id);
}

