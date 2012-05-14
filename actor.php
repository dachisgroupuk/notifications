<?php

/**
 * Representation of the Actor of a notification
 *
 * @package notifications_api
 * @author Maarten Jacobs
 */
class Notifications_Api_Actor {
  
  /**
   * Represents the type of the data the actro holds.
   * 
   * @example
   *  'uid' -> might represent a user id in the system that generates the notifications.
   *  'twitter_handle' -> might represent the Twitter username of the user to notify.
   * @var string
   **/
  protected $_type;

  /**
   * Represents the actual endpoint.
   * 
   * @example
   *  Type could be 'uid', and thus Data should be an integer like 5.
   * @var mixed
   **/
  protected $_data;
  
  /**
   * Returns the type of the recipient, to be set by the recipient creator.
   *
   * @return string
   * @author Maarten Jacobs
   */
  public function getType() {
    return $this->_type;
  }
  
  /**
   * Represents the data representing the recipient, of any arbitrary type.
   *
   * @return mixed
   * @author Maarten Jacobs
   */
  public function getData() {
    return $this->_data;
  }
  
  /**
   * Initialise and set the values of the Actor
   *
   * @param string $type Represents the type of Actor, like 'user_id', 'twitter_handle', etc.
   * @param mixed $data Represents the data required for the Actor to be used as an endpoint.
   * @return void
   * @author Maarten Jacobs
   **/
  public function __construct($type, $data) {
    $this->_data = $data;
    $this->_type = '' . $type;
  }
  
}