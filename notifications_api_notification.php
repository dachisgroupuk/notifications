<?php

/**
 * Represents a single notification in the system.
 *
 * @package notifications_api
 * @author Maarten Jacobs
 * @author Rachel Graves
 **/
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
 	* Internal array for dynamic properties
 	*
 	* @var array
 	*/
	protected $_data;
	
  /**
   * Array of callback functions for this notification
   *
   * @var array
   */
  public $callbacks;
  
  /**
   * Assign properties to this object
   * 
   * @param string $origin The module that was passed a Factory, which subsequently generated this  notification.
   * @param string $type_payload Type of the content that has been passed.
   * @param string $op_payload The operation that has been executed (or will be executed) on the payload, which is the trigger for the notification process. 
   * @param mixed $payload The piece of content which has acted upon.
   * @param Notifications_Api_Factory_Queue $factory The Factory that was used to generate this notification object.
   */
  public function __construct($origin, $type_payload, $op_payload, $payload, Notifications_Api_Factory_Queue &$factory) {
    $this->_data = array();
    $this->_origin = $origin;
    $this->_type_payload = $type_payload;
    $this->_op_payload = $op_payload;
    $this->_payload = $payload;
    $this->_id = uniqid('notifications_api_notification');
    $this->_factory = $factory;

    // Get default values from the factory
    $this->setMessage($factory->message);
		if($factory->sender instanceof Sender){
    	$this->setSender($factory->sender);
		}
		
		if(is_array($factory->recipients)){
			foreach ($factory->recipients as $recipient) {
				if($recipient instanceof Recipient){
					$this->_addRecipient($recipient);
				}	      
	    }
		}    
    // Initilaise callbacks as an array and set the default callback function
    $this->callbacks = array();
    $this->callbacks[] = $origin . '_notifications_api_send';
  }
  
  /**
   * Returns the value of a dynamic property of this object.
   *
   * @param string $name Name of the dynamic property
   * @return mixed
   * @author Pavlos Syngelakis
   **/
  public function __get($name) {
    if(isset($this->_data[$name])){
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
   * Returns the generated ID of this object
   *
   * @return string
   **/
  public function getId() {
    return $this->_id;
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
   * Return a string of the module that implemented this factory
   *
   * @return string
   * @author Rachel Graves 
   * @author Maarten Jacobs 
   */
  public function getOrigin() {
    return $this->_origin;
  }
  
  /**
   * Returns the list of recipients this notifications is destined to be sent to.
   *
   * @return array
   **/
  public function getTos() {
    return $this->_recipients;
  }
  
  /**
   * Returns the message of the notification.
   *
   * @return string
   **/
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
   * @param Sender $sender 
   * @return Notifications_Api_Notification
   * @author Rachel Graves
   */
  public function setSender(Sender $sender) {
    $this->_initiator = $sender;
    return $this;
  }

  /**
   * Send this notification
   * 
   * @return void
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
   * @param Recipient $recipient 
   * @return Notifications_Api_Notification
   */
  protected function _addRecipient(Recipient $recipient) {
    $this->_recipients[] = $recipient;
    return $this;
  }

}