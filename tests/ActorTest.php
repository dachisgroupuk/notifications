<?php

class ActorTest extends PHPUnit_Framework_TestCase {

  /**
   * Tests if custom Recipients can be used
   *
   * @author Maarten Jacobs
   **/
  function testCustomRecipient() {
    
    // Create a factory
    $origin = 'spam_me_a_river';
    $type_payload = 'comment'; 
    $op_payload = 'liked'; 
    
    $payload = new StdClass();
    $payload->location = $location = 'Austin';
    $payload->headers = $headers = array(
      'Content-Type' => 'text/html'
    );

    $default_sender = new Notifications_Api_Sender('from', 'Example System');
    $default_recipients = array(
      new SystemRecipient(5), // the actual user
      new SystemRecipient(1), // admin, cuz' he's a sneaky git
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
      $this->assertEquals($default_sender, $notification->getSender());
    }

  }

  /**
   * Tests if customs Senders can be used
   *
   * @author Maarten Jacobs
   **/
  function testCustomSenders() {

    // Create a factory
    $origin = 'spam_me_a_river';
    $type_payload = 'comment'; 
    $op_payload = 'liked'; 
    
    $payload = new StdClass();
    $payload->location = $location = 'Austin';
    $payload->headers = $headers = array(
      'Content-Type' => 'text/html'
    );

    $default_sender = new SystemSender('2p83sndo93m2[ls2ndp32usw2pd93');
    $default_recipients = array(
      new SystemRecipient(5), // the actual user
      new SystemRecipient(1), // admin, cuz' he's a sneaky git
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
      $this->assertEquals($default_sender, $notification->getSender());
    }

  }

}

/**
 * Test class for the custom recipient tests.
 * Custom recipients can be used to collect endpoints, specific to the system.
 *
 **/
class SystemRecipient extends Notifications_Api_Recipient {

  protected $uid;

  function __construct($uid) {
    $this->uid = $uid;
  }

  function get_email() {
    // Returns the full email of the user
  }

  function get_twitter_handle() {
    // Returns the twitter name of the user, so they can be mentioned in a Tweet or DM
  }

  function get_facebook_uid() {
    // Returns a Facebook identifier
  }

}

/**
 * Test class for the custom sender tests.
 * Custom senders can be used to collect authentication, to use for the delivery method.
 * But it can also be used for simple lazy loading of user-specific data.
 *
 * This particular example would use OAuth2 for Twitter for instance.
 * 
 **/
class SystemSender extends Notifications_Api_Sender {

  protected $token;

  function __construct($auth_token) {
    $this->token = $auth_token;
  }

  function get_twitter_handle() {
    // Returns the twitter name of the user, so they can used as a conduit for the message
  }

  function tweet($message) {
    // In this case, we're using the sender as a conduit to tweet.
    // So our actual send method would use, for every recipient, this method to send it's notification.
  }

}