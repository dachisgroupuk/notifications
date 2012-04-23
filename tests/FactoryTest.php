<?php
class FactoryTest extends PHPUnit_Framework_TestCase {
	
	public function testAdd(){
		$var1 = 5;
		$var2 = 8;
		$this->assertEquals($var1 + $var2, 13);
	}
	
	public function testType(){
		$module_implements = 'calendarevents';
		$type = 'node';
		$op = 'node';
		$payload = array('cookies','fruit', array('I' => 'Hate you'));
		$factory = new Notifications_Api_Factory_Queue($module_implements, $type, $op, $payload);
		$notification = $factory->generateNotification();
		
		$this->assertEquals($notification->getType(), $type);
		$this->assertEquals($notification->getOp(), $op);
		$this->assertEquals($notification->getPayload(), $payload);
		$this->assertEquals($notification->getModuleImplements(), $module_implements);
		
	}
	
	public function testUniqid(){
		$module_implements = 'calendarevents';
		$type = 'node';
		$op = 'node';
		$payload = array('cookies','fruit', array('I' => 'Hate you'));
		$factory = new Notifications_Api_Factory_Queue($module_implements, $type, $op, $payload);
		$notification = $factory->generateNotification();
		$notification2 = $factory->generateNotification();
		
		$this->assertFalse($notification->getID() == $notification2->getID());
	}
}