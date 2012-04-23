<?php

class Notifications_Api_Factory_Queue implements Iterator {
 
  /**
   * Object ID
   */
  protected $_id;

  /**
   * Arbitrary string that describes the type of the payload.
   */
  protected $_type_payload;

  /**
   * Arbitrary string that describes the operation that has been executed (or has been or will be executed) on the payload.
   */
  protected $_op_payload;
  
  /**
   * Arbitrary payload that has been altered
   */
  protected $_payload;
  
  /**
   * Origin of the notification. This is typically a plugin or module.
   *
   * @var string
   */
  protected $_origin;

  /**
   * Default message for new notifications.
   *
   * @var string
   */
  public $message;

  /**
   * Default sender for new Notifications.
   *
   * @var Sender
   */
  public $sender;

  /**
   * Default recipients for new Notifications.
   * All recipients should be of type Recipient.
   *
   * @var array
   */
  public $recipients;
  
  /**
   * Array of notification objects
   *
   * @var array
   */
  protected $_notifications;  
 
  function __construct($origin, $type_payload, $op_payload, $payload, $default_sender=null, $default_recipients=null, $default_message=null) {
    $this->_origin = $origin;
    $this->_type_payload = $type_payload;
    $this->_op_payload = $op_payload;
    $this->_payload = $payload;
    $this->message = $default_message;
    $this->sender = $default_sender;
    $this->recipients = $default_recipients;
    $this->_id = uniqid('notifications_api_notification');
    
    // Initialise as an array
    $this->_notifications = array();
  }
  
  /**
   * undocumented function
   *
   * @return void
   * @author Rachel Graves
   */
  public function generateNotification() {
    return new Notifications_Api_Notification(
      $this->getOrigin(), 
      $this->getType(), 
      $this->getOp(), 
      $this->getPayload(),
      $this
    );
  }
  
  /**
   * Store a notification back in the bender factory
   *
   * @param Notifications_Api_Notification $notification 
   * @return void
   * @author Rachel Graves
   */
  public function storeNotification(Notifications_Api_Notification $notification) {
    $this->_notifications[$notification->getId()] = $notification;
  }
  
  /**
   * Instruct notifications to send
   *
   * @return void
   * @author Rachel Graves
   */
  public function send() {
    foreach($this->_notifications as $notification) {
      $notification->send();
    }
  }
  
  public function getType() {
    return $this->_type_payload;
  }

  public function getOp() {
    return $this->_op_payload;
  }

  public function getPayload() {
    return $this->_payload;
  }
  
  /**
   * Return a string of the module that implemented this
   * factory
   *
   * @return string
   */
  public function getOrigin() {
    return $this->_origin;
  }
  
  /**
   * Rewind the list of notifications to the beginning
   *
   * @return void
   */
  public function rewind() {
    reset($this->_notifications);
  }

  /**
   * Return the current notification
   *
   * @return Notifications_Api_Notification
   * @author Rachel Graves
   */
  public function current() {
    return current($this->_notifications);
  }

  /**
   * Return the current notification being pointed at
   * by the array's internal pointer
   *
   * @return Notifications_Api_Notification
   * @author Rachel Graves
   */
  public function key() {
    return key($this->_notifications);
  }

  public function next() {
    return next($this->_notifications);
  }

  public function valid() {
    $key = key($this->_notifications);
    return ($key !== NULL && $key !== FALSE);
  }
  
  /**
   * Updates a given notification by key, first checking if it's the same notification.
   *
   * @param string $key 
   * @param Notifications_Api_Notification $notification 
   * @return mixed
   * @author Maarten Jacobs
   */
  public function updateNotification($key, Notifications_Api_Notification $notification) {
    if (!array_key_exists($key, $this->_notifications) || $this->_notifications[$key]->getId() !== $notification->getId()) {
      return FALSE;
    }
    $this->_notifications[$key] = $notification;
    return $this;
  }
  
  /**
   * Test function
   * TODO: REMOVE ME
   *
   * @return void
   * @author Maarten Jacobs
   */
  public function getNotifications() {
    return $this->_notifications;
  }
  
}