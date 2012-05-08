<?php

/**
 * Representation of the Actor of a notification
 *
 * @package notifications_api
 * @author Maarten Jacobs
 */
class Actor {
  
  protected $_type;
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
  
  public function __construct($type, $data) {
    $this->_data = $data;
    $this->_type = '' . $type;
  }
  
}