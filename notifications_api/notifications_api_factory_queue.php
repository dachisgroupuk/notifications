<?php

class Notifications_Api_Factory_Queue implements Iterator {
 
  /**
   * Object ID
   */
  protected $_id;

  /**
   * Node or comment type notification
   */
  protected $_type;

  /**
   * Nodeapi or Comment operation
   */
  protected $_op;
  
  /**
   * Typically a node or comment object
   */
  protected $_payload;
  
  /**
   * Uids that will receive this notification
   */
  protected $_uids;
  
  /**
   * Notification message
   */
  protected $_message;
  
  /**
   * Uid that initiated this notification
   */
  protected $_initiatorUid;
  
  /**
   * Source of the notification
   *
   * @var string
   */
  protected $_moduleImplements;
  
  /**
   * Array of notification objects
   *
   * @var array
   */
  protected $_notifications;  
 
  function __construct($module_implements, $type, $op, $payload) {
    $this->_moduleImplements = $module_implements;
    $this->_type = $type;
    $this->_op = $op;
    $this->_payload = $payload;
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
    return new Notifications_Api_Notification($this->_moduleImplements, $this->_type, 
      $this->_op, $this->_payload, $this);
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
    return $this->_type;
  }

  public function getOp() {
    return $this->_op;
  }

  public function getPayload() {
    return $this->_payload;
  }
  
  public function getModuleImplements() {
    return $this->_moduleImplements;
  }
  
  public function rewind() {
    reset($this->_notifications);
  }

  public function current() {
    return current($this->_notifications);
  }

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