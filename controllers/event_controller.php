<?php

require_once '../classes/event_class.php';

//add event controller
function add_event_ctr($eventCat, $eventDes, $eventPrice, $eventLocation, $eventDate, $eventStart, $eventEnd,  $flyer, $eventKey, $user_id)
{
    $event = new Event();
    return $event->addEvent($eventCat, $eventDes, $eventPrice, $eventLocation, $eventDate, $eventStart, $eventEnd,  $flyer, $eventKey, $user_id);
}

//update event controller
function update_event_ctr($event_id, $eventCat, $eventDes, $eventPrice, $eventLocation, $eventDate, $eventStart, $eventEnd,  $flyer, $eventKey)
{
    $event = new Event();
    return $event->updateEvent($event_id, $eventCat, $eventDes, $eventPrice, $eventLocation, $eventDate, $eventStart, $eventEnd,  $flyer, $eventKey);
}

//delete event controller
function delete_event_ctr($event_id)
{
    $event = new Event();
    return $event->deleteEvent($event_id);
}

//get event controller
function get_event_ctr($user_id)
{
    $event = new Event();
    return $event->getEvent($user_id);
}

//view all events controller
function view_all_event_ctr(){
    $event = new Event();
    return $event->viewAllEvent();
}

//filter by category controller
function filter_by_cat_ctr($cat_id){
    $event = new Event();
    return $event->filterByCat($cat_id);
}   

//view single event controller 
function view_single_event_ctr($event_id){
    $event = new Event();
    return $event->viewSingleEvent($event_id);
}

