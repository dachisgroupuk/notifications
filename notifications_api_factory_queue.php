<?php

/**
 * Factory that can generate and hold notifications.
 *
 * @package notifications_api
 * @author Rachel Graves 
 * @author Maarten Jacobs 
 **/
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
  
  /**
 	* Internal array for dynamic properties
 	*
 	* @var array
 	*/
	protected $_data;  
 
  /**
   * Initialise the factory, setting the default properties which will be forwarded to the created notifications.
   *
   * @return void
   * @param string $origin Name of the module that will receive this factory for the generate phase.
   * @param string $type_payload Type of the content that has been passed.
   * @param string $op_payload The operation that has been executed (or will be executed) on the payload, which is the trigger for the notification process. 
   * @param mixed $payload The piece of content which has acted upon.
   * @param mixed $default_sender The sender object which will be passed to the generated notification objects.
   * @param mixed $default_recipients The recipient object which will be passed to the generated notification objects.
   * @param string $default_message The message that will be passed to the generated notification objects.
   * @author Rachel Graves 
   * @author Maarten Jacobs 
   **/
  function __construct($origin, $type_payload, $op_payload, $payload, $default_sender = null, $default_recipients = null, $default_message = null) {
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
   * Generates a notification, setting the properties to the default properties of the factory.
   * Adds a reference to the factory (this object) that has created the notification.  
   *
   * @return Notifications_Api_Notification
   * @author Rachel Graves 
   * @author Maarten Jacobs 
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
   * @author Maarten Jacobs 
   */
  public function storeNotification(Notifications_Api_Notification $notification) {
    $this->_notifications[$notification->getId()] = $notification;
  }
  
  /**
   * Instruct notifications to send
   *
   * @return void
   * @author Rachel Graves 
   * @author Maarten Jacobs 
   */
  public function send() {
    foreach($this->_notifications as $notification) {
      $notification->send();
    }
  }
  
  /**
   * Return the type of the payload, which triggered this notification process.
   *
   * @example
   *  'post' -> The payload corresponds to what the system understands as a post type.
   * @return string
   * @author Rachel Graves 
   * @author Maarten Jacobs 
   */
  public function getType() {
    return $this->_type_payload;
  }

  /**
   * Return the operation that was executed on the payload, which is the trigger to the notification process.
   *
   * @example
   *  'created' -> Can mean that the payload has just been created in the system.
   *  'like' -> Can mean that the payload has just been tagged as liked by a user. 
   * @return string
   * @author Rachel Graves 
   * @author Maarten Jacobs 
   **/
  public function getOp() {
    return $this->_op_payload;
  }

  /**
   * Return the payload that is associated with the operation.
   * This can be any type of content in the system.
   *
   * @return mixed
   * @author Rachel Graves 
   * @author Maarten Jacobs 
   **/
  public function getPayload() {
    return $this->_payload;
  }
  
  /**
   * Returns the value of a dynamic property of this object.
   *
   * @param string $name Name of the dynamic property
   * @return mixed
   * @author Pavlos Syngelakis
   **/
  public function __get($name){
    if(isset($this->_data[$name])) {
      return $this->_data[$name];
    }
  }
  
  /**
   * Sets the value of a dynamic property of this object.
   *
   * @param string $name Name of the dynamic property
   * @param mixed $value New value for the dynamic property
   * @return mixed
   * @author Pavlos Syngelakis
   **/
  public function __set($name, $value) {
    $this->_data[$name] = $value;
  }
  
  /**
   * Return a string of the module that implemented this
   * factory
   *
   * @return string
   * @author Rachel Graves 
   * @author Maarten Jacobs 
   */
  public function getOrigin() {
    return $this->_origin;
  }
  
  /**
   * Rewind the list of notifications to the beginning
   *
   * @return void
   * @author Rachel Graves 
   * @author Maarten Jacobs 
   */
  public function rewind() {
    reset($this->_notifications);
  }

  /**
   * Return the current notification
   *
   * @return Notifications_Api_Notification
   * @author Rachel Graves 
   * @author Maarten Jacobs 
   */
  public function current() {
    return current($this->_notifications);
  }

  /**
   * Return the current notification being pointed at by the array's internal pointer
   * Required for Iterator class
   *
   * @return Notifications_Api_Notification
   * @author Rachel Graves 
   * @author Maarten Jacobs 
   */
  public function key() {
    return key($this->_notifications);
  }

  /**
   * Returns the next notification.
   * Required for Iterator class
   *
   * @return Notifications_Api_Notification
   * @author Rachel Graves 
   * @author Maarten Jacobs 
   **/
  public function next() {
    return next($this->_notifications);
  }

  /**
   * Checks if position on our internal array hasn't run out on us.
   * Required for Iterator class
   *
   * @return bool
   * @author Rachel Graves 
   * @author Maarten Jacobs 
   **/
  public function valid() {
    $key = key($this->_notifications);
    return ($key !== NULL && $key !== FALSE);
  }
  
  /**
   * Updates a given notification by key, first checking if it's the same notification.
   *
   * @param string $key The id that was generated by the notification on generation. Which is used by the factory as a key to point to the notification.
   * @param Notifications_Api_Notification $notification The notification which has been updated.
   * @return mixed
   * @author Rachel Graves 
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
   * @return array
   * @author Rachel Graves 
   * @author Maarten Jacobs 
   */
  public function getNotifications() {
    return $this->_notifications;
  }
  
}