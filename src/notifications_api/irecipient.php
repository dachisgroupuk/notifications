<?php

/**
 * Representation of the Recipient of a notification
 *
 * @package notifications_api
 * @author Maarten Jacobs
 */
interface IRecipient {
  
  /**
   * Returns the type of the recipient, to be set by the recipient creator.
   *
   * @return string
   * @author Maarten Jacobs
   */
  public function getType();
  /**
   * Represents the data representing the recipient, of any arbitrary type.
   *
   * @return mixed
   * @author Maarten Jacobs
   */
  public function getData();
  
  public function __construct($type, $data);
  
}