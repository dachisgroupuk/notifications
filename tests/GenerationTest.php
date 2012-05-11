<?php

class GenerationTest extends PHPUnit_Framework_TestCase {

  /**
   * Tests if all properties of factory are set and saved correctly.
   * Then tests if these properties are passed to all notifications.
   *
   * @author Maarten Jacobs
   **/
  function testProperties() {

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
    $factory->generateNotification()->store();
    $factory->generateNotification()->store();
    $factory->generateNotification()->store();
    $factory->generateNotification()->store();
    $factory->generateNotification()->store();

    // Check if the properties are the same
    foreach ($factory as $notification) {    
      $this->assertEquals($origin, $notification->getOrigin());
      $this->assertEquals($type_payload, $notification->getType());
      $this->assertEquals($op_payload, $notification->getOp());
      $this->assertEquals($headers, $notification->getPayload()->headers);
      $this->assertEquals($location, $notification->getPayload()->location);
    }

  }

  /**
   * Tests if generated notifications have unique ids
   *
   * @author Maarten Jacobs 
   **/
  function testUniqId() {

    // Create a factory
    $origin = 'everywhere';
    $type_payload = 'post'; 
    $op_payload = 'created'; 
    
    $payload = new StdClass();
    $payload->location = $location = 'Ostend';
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

    // Now check if all their ids are unique
    $ids = array();
    foreach ($factory as $notification) {    
      $this->assertFalse(array_key_exists($notification->getId(), $ids));

      $ids[$notification->getId()] = 'yup';
    }

  }

  /**
   * Tests if default properties are passed correctly from factory to notifications
   *
   * @author Maarten Jacobs
   **/
  function testDefaultProperties() {

    // Create a factory
    $origin = 'spam_me_a_river';
    $type_payload = 'comment'; 
    $op_payload = 'liked'; 
    
    $payload = new StdClass();
    $payload->location = $location = 'Austin';
    $payload->headers = $headers = array(
      'Content-Type' => 'text/html'
    );

    $default_sender = new Sender('from', 'Example System');
    $default_recipients = array(
      new Recipient('uid', 5), // the actual user
      new Recipient('uid', 1), // admin, cuz' he's a sneaky git
    );
    $default_message = 'Your comment, http://example.com/comment/34 was liked by someone!';

    $factory = new Notifications_Api_Factory_Queue(
      $origin, 
      $type_payload, 
      $op_payload, 
      $payload, 
      $default_sender, 
      $default_recipients,
      $default_message
    );

    // Check set properties
    $this->assertEquals($default_message, $factory->message);
    $this->assertEquals($default_recipients, $factory->recipients);
    $this->assertEquals($default_sender, $factory->sender);

    // Create a few notifications
    $factory->generateNotification()->store();
    $factory->generateNotification()->store();
    $factory->generateNotification()->store();
    $factory->generateNotification()->store();
    $factory->generateNotification()->store();

    // Check if the properties are the same
    foreach ($factory as $notification) {    
      $this->assertEquals($default_message, $notification->getMessage());
      $this->assertEquals($default_recipients, $notification->getTos());

      // Can't test senders at the moment, because the function doesn't exist
      // $this->assertEquals($default_sender, $notification->getSender());
    }

  }

  /**
   * Tests if dynamic properties are saved correctly
   *
   * @author Maarten Jacobs
   **/
  function testDynamicProperties() {

    // Create a factory
    $origin = 'mail_me_a_river';
    $type_payload = 'page'; 
    $op_payload = 'edited'; 
    
    $payload = new StdClass();
    $payload->location = $location = 'Texas';
    $payload->headers = $headers = array(
      'Content-Type' => 'text/html'
    );

    $factory = new Notifications_Api_Factory_Queue(
      $origin, 
      $type_payload, 
      $op_payload, 
      $payload
    );

    // Create a notification with dynamic properties
    $properties = array(
      'previous_editors' => array(
        4, 1, 5
      ),
      'next_page' => new StdClass(),
      'initial_creator' => 2
    );
    $notification = $factory->generateNotification();
    foreach ($properties as $prop_name => $prop_value) {
      $notification->{$prop_name} = $prop_value;
    }
    $notification->store();

    // Retrieve notification from factory and check if the dynamic properties are the same
    $result_notification = $factory->getNotification($notification->getId());
    foreach ($properties as $prop_name => $prop_value) {
      $this->assertEquals($result_notification->{$prop_name}, $notification->{$prop_name});
    }

  }

}