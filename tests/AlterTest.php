<?php

class AlterTest extends PHPUnit_Framework_TestCase {

  /**
   * Tests if a notification's properties can be altered
   *
   * @author Maarten Jacobs
   **/
  function testAlterting() {

    // 1. Generate
    // Create a factory
    $origin = 'xanadu';
    $type_payload = 'tweet'; 
    $op_payload = 'retweet'; 
    
    $payload = new StdClass();
    $payload->location = $location = 'London';
    $payload->headers = $headers = array(
      'Content-Type' => 'text/html'
    );

    $factory = new Notifications_Api_Factory_Queue($origin, $type_payload, $op_payload, $payload);

    // Create a few notifications
    $factory->generateNotification()->store();
    $factory->generateNotification()->store();
    $factory->generateNotification()->store();
    $factory->generateNotification()->store();
    $factory->generateNotification()->store();

    // Alter the notifications
    $new_message = 'This message is obsolete. Please remove this call!';
    foreach ($factory as $notification) {    
      $notification->setMessage($new_message);
      $factory->updateNotification($notification->getId(), $notification);
    }

    // Check if altered
    foreach ($factory as $notification) {    
      $this->assertEquals($new_message, $notification->getMessage());
    }
  }

}