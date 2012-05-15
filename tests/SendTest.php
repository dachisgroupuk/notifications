<?php

class SendTest extends PHPUnit_Framework_TestCase {

  // Test callbacks
  protected static $counter = 0;
  function first_callback() {
    self::$counter++;
  } 
  function second_callback() {
    self::$counter++;
  }
  static function third_callback() {
    self::$counter++;
  }

  /**
   * Check if all callbacks are called.
   *
   * @author Maarten Jacobs
   **/
  function testSendMultiple() {

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

    // Check set properties
    $this->assertEquals($origin, $factory->getOrigin());
    $this->assertEquals($type_payload, $factory->getType());
    $this->assertEquals($op_payload, $factory->getOp());
    $this->assertEquals($headers, $factory->getPayload()->headers);
    $this->assertEquals($location, $factory->getPayload()->location);

    // Create a few notifications
    for ($i = 5; $i; $i--) {
      $notification = $factory->generateNotification()->setMessage('This is a test message');
      $notification->callbacks[] = array($this, 'first_callback');
      $notification->callbacks[] = array($this, 'second_callback');
      $notification->callbacks[] = array('SendTest', 'third_callback');
      $notification->store();
    }

    // Send off the notifications
    $factory->send();

    // The counter should now be 15, as 5 notifications with 3 callbacks
    $this->assertEquals(self::$counter, 15);

  }

}