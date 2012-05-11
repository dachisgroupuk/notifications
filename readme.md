# Notifications API (aka Notifications Core)

This library defines a __convention__ (and a design pattern) for generating notifications, allowing alterations and sending the result. This library contains functions and classes you can use to implement that convention.

## Use cases

We created this convention because, well, we had a few failed attempts at notification system which 

## Terms

To ease the reading of this document, please keep in mind the following terms:

* `framework`: a full-blown CMS, or a framework like Zend, FuelPHP or Symfony. We view this as a framework because they have the facilities for events.
* `module`: a pluggable library specific to the framework you're using. In WordPress, this would be a Plugin, as in Drupal it would be a module.
* `callback`: a callback is a legitimate PHP callback. Any syntax like `array($this, 'generate_notifications')` to `'generate_notifications'` counts a valid callback, as defined in the [PHP documentation of Callbacks](http://uk3.php.net/manual/en/language.types.callable.php).
* `base module`: the result of following this convention, a base module is an adapter (or Mediator) between the framework of your project and the notifications system. This eases development for future modules that use the notifications system.

## Notification creation process

This API is built for, but does not enforce the following creation process, which we believe to be most straightforward workflow.
Important to note is the following: all frameworks should have an adapter between the framework system and the notifications system. This is defined as the `base module` in the above terms.

1. 	__Gather listeners__

	The base module, depending on the framework you use, needs to be aware of its listeners, i.e the other modules that listen to the adapted events of the framework.
	For some frameworks the gathering isn't actually required, because CMSs like Drupal use a hook system.

	Nonetheless, it is important to know which modules support the notifications, because they are the `origin` of our Notification object. This is necessary for the alter phase, for easy filtering.

2.	__Generate Notifications__

	The base module fires events similar to the ones of the framework (hopefully only when these events occur, or when appropriate). It's up to the base module to decide how it will translate the framework's events to events that can be used by listening modules.

	These base module fired events call our listening modules and passes each one of them a unique factory. The factory contains default properties for generating notifications, as well as the important `origin` property.

	When a listening module receives this factory, it will use that factory to generate notifications based on the event for which it was created. The notifications are created with all the default properties of the factory, such as `origin`, `payload`, `operation of payload`, etc.

	*Note*: this factory is commonly passed to the listening module by reference (which PHP does by default for objects). It's up to the base module implementation to follow this convention instead of returning the factory.

3. 	__Altering notifications__

	You may have noted that Notification objects are passed a lot of properties that are specific to the event for which it was created. In the alter phase, this becomes most useful.

	In essence: after the generation (or if you prefer, _during_) of the Notification objects, you can start passing the factories to modules that wish to alter notifications. This means all listening modules get passed the factories and can change them as they please.

	As each listening module gets the factories, they can filter them by the properties they have been handed down by their factory. 

4. 	__Sending notifications__

	Now that all Notification objects have been generated and altered, so that recipients and message are complete, we can send them off.

	The method by which Notification objects are sent is by their public `callbacks` property. By default this contains `[origin]_notifications_api_send`, so the origin module can create the function and never have to add another callback if it doesn't need multiple send callbacks.
	All of these callbacks must be valid PHP callbacks. If they are not, they will be skipped (i.e. silent fail).

	Per notification, every callback is called for that notification and is passed the notification. So by default, without alteration, every notification gets passed to a single callback as defined in its default callback.

	Note that you cannot count on the order of execution. In some systems the modules order might be based on weight, whilst others might be based on alphabetic order.

## Object types

Here is a quick summary of the types of objects we use internally, and are exposed (or passed around to your system).

### Notification Factory

A notification factory collects common data for its notifications, namely:

* `origin`: in most cases, this would be the `module` that is 
* `payload type`: an arbitrary description of the type of content given to the notification. This is useful for systems that don't use classes very heavily (i.e. Drupal and WordPress), but also for debugging and general ease of filtering.
* `payload`: the content that actually triggered the notification. This is again arbitrary, so anything in your system can trigger a notification creation process.
* `operation of payload`: the operation that has been executed (or will or is being executed) on the payload. This is again arbitrary, so a simple viewing of content can trigger the notification creation process.
* `default message` (optional): you can specify a message which will be passed to any generated notification automatically. This is a completely public property, so you can alter before and after object creation.
* `default sender` (optional): like the default message, you can set this property whenever you want. The only requirement is that it must be an instance of the Sender class (or one of its children classes).
* `default recipients` (optional): like the other default properties, you can set this property whenever you want. The two requirements are as follows:
    * it must be an array, and,
    * all objects inside the array must be an instance of the Recipient class (or one of its children classes).

### Nofication Object

A notification object is created by the notification factory, and is augmented with the default data given to the Factory.

#### Callbacks

The most important property is the `callbacks` property, which contains the callbacks fired in the send phase of the notification process. This is the property that allows notifications to be reused without creating duplicates of it. 

For instance: imagine a logging module. It would generate notifications accordingly for every exception in your system. By default, the logging module would simply output to [error_log](http://uk3.php.net/manual/en/function.error-log.php). But another module, for instance a Twitter module, can handle Tweets and Direct Messages. And so this module can alter those error notifications and add its own callback, which will send you a lovely DM on Twitter every time a major exception happens in your system.

### Actor

The Actor class is a simple base class that can be inherited from, to have system-specific participants for your notifications. You can also use the provided example classes (`Recipient` and `Sender`) and save arbitrary data on those objects.

#### Recipient

A recipient is a sample child class of Actor, which can be used to represent the recipient (but also recipient**s**) of a notification. Like Actor, it accepts any type of data you would like to assign.

#### Sender

A sender is a sample child class of Actor, which can be used to represent the sender of a notification. Like Actor, it accepts any type of data you would like to assign.