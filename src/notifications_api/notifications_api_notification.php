<?php

class Notifications_Api_Notification {

  /**
   * Object ID
   */
  protected $_id;

  /**
   * Node or comment type notification
   */
  protected $_type_payload;

  /**
   * Nodeapi or Comment operation
   */
  protected $_op_payload;
  
  /**
   * Typically a node or comment object
   */
  protected $_payload;
  
  /**
   * Recipients that will receive this notification
   */
  protected $_recipients;
  
  /**
   * Notification message
   */
  protected $_message;
  
  /**
   * Source of the notification
   *
   * @var string
   */
  protected $_origin;
  
  /**
   * Array of callback functions for this notification
   *
   * @var array
   */
  public $callbacks;
  
  /**
   * Assign properties to this object
   */
  public function __construct($origin, $type_payload, $op_payload, $payload, Notifications_Api_Factory_Queue &$factory) {
    $this->_origin = $origin;
    $this->_type = $type_payload;
    $this->_op = $op_payload;
    $this->_payload = $payload;
    $this->_id = uniqid('notifications_api_notification');
    $this->_factory = $factory;

    // Get default values from the factory
    $this->setMessage($factory->message);
    $this->setSender($factory->sender);
    foreach ($factory->recipients as $recipient) {
      $this->_addRecipient($recipient);
    }
    
    // Initilaise callbacks as an array and set the default callback function
    $this->callbacks = array();
    $this->callbacks[] = $origin . '_notifications_api_send';
  }
  
  public function getId() {
    return $this->_id;
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
  
  public function getOrigin() {
    return $this->_origin;
  }
  
  public function getTos() {
    return $this->_recipients;
  }
  
  public function getMessage() {
    return $this->_message;
  }
  
  /**
   * Set this Notification's message
   *
   * @param string $message 
   * @return Notifications_Api_Notification
   * @author Rachel Graves
   */
  public function setMessage($message) {
    $this->_message = $message;
    return $this;
  }

  /**
   * Set the notification's sender
   *
   * @param string $sender 
   * @return Notifications_Api_Notification
   * @author Rachel Graves
   */
  public function setSender(Sender $sender) {
    $this->_initiator = $sender;
    return $this;
  }

  /**
   * Send this notification
   */
  public function send() {
    $callable_callbacks = array_filter($this->callbacks, 'is_callable');
    foreach ($callable_callbacks as $callback) {
      call_user_func($callback, $this);
    }
  }

  /**
   * Store a notification back in the PG tips factory
   *
   * @return void
   * @author Rachel Graves
   */
  public function store() {
    $this->_factory->storeNotification($this);
  }
  
  /**
   * Adds a recipient to the list of recipients
   *
   * @param string $type 
   * @param mixed $data 
   * @return Notifications_Api_Notification
   * @author Maarten Jacobs
   */
  public function addTo($type, $data) {
    
    if ($type instanceof Recipient) {
      $recipient = $type;
    } else {
      $recipient = new Recipient($type, $data);      
    }  

    $this->_addRecipient($recipient);
    return $this;
  }
  
  /**
   * Set the recipients list to a single recipient,
   * effectively erasing the list of recipients.
   *
   * @param string $type 
   * @param mixed $data 
   * @return Notifications_Api_Notification
   * @author Maarten Jacobs
   */
  public function setTo($type, $data) {
    
    if ($type instanceof Recipient) {
      $recipient = $type;
    } else {
      $recipient = new Recipient($type, $data);      
    }  
    
    $this->_recipients = array($recipient);
    return $this;
  }
  
  /**
   * Add a recipient to the notification
   *
   * @param IRecipient $recipient 
   * @return Notifications_Api_Notification
   */
  protected function _addRecipient(Recipient $recipient) {
    $this->_recipients[] = $recipient;
    return $this;
  }

}