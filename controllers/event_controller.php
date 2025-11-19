<?php

require_once '../classes/event_class.php';

// Add event controller
function add_event_ctr($eventCat, $eventTitle, $eventDescription, $eventKeywords, $eventImage, $eventDate, $eventLocation, $user_id)
{
    $event = new Event();
    return $event->addEvent($eventCat, $eventTitle, $eventDescription, $eventKeywords, $eventImage, $eventDate, $eventLocation, $user_id);
}

// Update event controller
function update_event_ctr($eventCat, $eventTitle, $eventDescription, $eventKeywords, $eventImage, $eventDate, $eventLocation, $event_id)
{
    $event = new Event();
    // Ensure argument order matches Event::updateEvent()
    return $event->updateEvent($event_id, $eventCat, $eventTitle, $eventDescription, $eventKeywords, $eventImage, $eventDate, $eventLocation);
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

