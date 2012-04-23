<?php
class FactoryTest extends PHPUnit_Framework_TestCase {
	
	public function testAdd(){
		$var1 = 5;
		$var2 = 8;
		$this->assertEquals($var1 + $var2, 13);
	}
	
	public function testType(){
		$origin = 'calendarevent';
		$type_payload = 'node';
		$op_payload = 'create';
		$payload = array('cookies','fruit', array('I' => 'Hate you'));
		$factory = new Notifications_Api_Factory_Queue($origin, $type_payload, $op_payload, $payload);
		$notification = $factory->generateNotification();
		
		$this->assertEquals($notification->getType(), $type_payload);
		$this->assertEquals($notification->getOp(), $op_payload);
		$this->assertEquals($notification->getPayload(), $payload);
		$this->assertEquals($notification->getOrigin(), $origin);
		
	}
	
	public function testUniqid(){
		$origin = 'calendarevents';
		$type = 'node';
		$op = 'remove';
		$payload = array('cookies','fruit', array('I' => 'Hate you'));
		$factory = new Notifications_Api_Factory_Queue($origin, $type, $op, $payload);
		$notification = $factory->generateNotification();
		$notification2 = $factory->generateNotification();
		
		$this->assertFalse($notification->getID() == $notification2->getID());
	}
}