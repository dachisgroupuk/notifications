<?php

class Recipient implements IRecipient {
  
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