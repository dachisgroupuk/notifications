# Notifications API (aka Notifications Core)

This library defines a simple interface for creating notifications through a factory.
In simple terms, it's a convention you can follow and this library contains functions and classes you can use to follow to implement that convention.

## Terms

To ease the reading of this document, please keep in mind the following terms:

* `framework`: a full-blown CMS, or a framework like Zend, FuelPHP or Symfony. We view this as a framework because they have the facilities for Events, and if they don't yet, 
* `module`: a pluggable library specific to the framework you're using. In WordPress, this would be a Plugin, as in Drupal it would be a module.
* `callback`: a callback is a legitimate PHP callback. Any syntax like `array($this, 'generate_notifications')` to `'generate_notifications'` counts a valid callback, as defined in the [PHP documentation of Callbacks](http://uk3.php.net/manual/en/language.types.callable.php).
* `base module`: a base module is an adapter (or Mediator) between the framework of your project and the notifications system. This eases development for future modules that use the notifications system.

## Notification creation process

This API is built for, but does enforce the following creation process, which we believe to be most straightforward workflow.
Important to note is the following: all frameworks should have an adapter between the framework system and the notifications system. This is defined as the `base module` in the above terms.

1. 	Gather listeners

	The base module, depending on the framework you use, needs to be aware of its listeners. i.e the other modules that listen to the adapted events of the framework.
	For some frameworks the gathering isn't actually required, because CMSs like Drupal use a hook system.

	Nonetheless, it is important to know which modules support the notifications, because they are the `origin` of our Notification object. This is necessary for the alter phase, for easy filtering.

2.	Generate Notifications

	The base module fires events similar to the ones of the framework (hopefully only when these events occur, or when appropriate). It's up to the base module to decide how it will translate the framework's events to events that can be used by listening modules.

	These base module fired events call our listening modules and passes each one of them a unique factory. The factory contains default properties for generating notifications, as well as the important `origin` property.

	When a listening module receives this factory, it will use that factory to generate notifications based on the event for which it was created. The notifications are created with all the default properties of the factory, such as `origin`, `payload`, `operation of payload`, etc.

	*Note*: this factory is commonly passed to the listening module by reference (which PHP does by default for objects). It's up to the base module implementation to follow this convention instead of returning the factory.

3. 	Altering notifications

	You may have noted that Notification objects are passed a lot of properties that are specific to the event for which it was created. In the alter phase, this becomes most useful.

	In essence: after the generation (or if you prefer, _during_) of the Notification objects, you can start passing the factories to modules that support or listen to altering of notifications. This means all listening modules get passed the factories and can change them as they please.

	As each listening module gets the factories, they can filter them by the properties they have been handed down by their factory. 

4. 	Sending notifications

	Now that all Notification objects are generated and altered, so that recipients and message are complete, we can send them off.

	The way Notification objects are sent is by their public `callbacks` property. By default this contains `[origin]_notifications_api_send`, so the origin module can create the function and never have to add another callback if it doesn't need multiple send callbacks.
	All of these callbacks must be valid PHP callbacks. If they are not, they will be skipped (i.e. silent fail).

	Each send callback is called for each Notification object and is passed the Notification object. Note that you cannot count on the order of execution. In some systems the modules order might be based on weight, whilst others might be based on alphabetic order.

## Object types

Here is a quick summary of the types of objects we use internally, and are exposed (or passed around to your system).

### Notification Factory

A notification factory takes collects common data for it's notifications, namely:

* _origin_: in most cases, this is the module that may create notifications on this library.
* _payload type_: an arbitrary description of the type of content given to the notification. This is useful for systems that don't use classes very heavily (i.e. Drupal and WordPress), but also for debugging and general ease of filtering.
* _payload_: the content that actually triggered the notification. This is again arbitrary, so anything in your system can trigger a notification creation process.
* _operation of payload_: the operation that has been executed (or will or is being executed) on the payload. This is again arbitrary, so a simple viewing of content can trigger the notification creation process.
* _default message_ (optional): you can specify a message which will be passed to any generated notification automatically. This is a completely public property, so you can alter before and after object creation.
* _default sender_ (optional): like the default message, you can set this property whenever you want. The only requirement is that it must be an instance of the Sender class (or one of its children classes).
* _default recipients_ (optional): like the other default properties, you can set this property whenever you want. The two requirements are as follows:
    * it must be an array, and,
    * all objects inside the array must be an instance of the Recipient class (or one of its children classes).

### Nofication Object

A notification object is created by the notification factory, and augmented whilst being created with the data given to the Factory including the default data.

### Recipient

### Sender