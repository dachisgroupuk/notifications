# Notifications API (aka Notifications Core)

This library defines a simple interface to creating notifications through a factory.
Because all notifications are created this way, you can alter them through the factory, as opposed to having a flat list of notifications without source or type of given content.

## Notification creation process

This API is built for, but does enforce the following creation process, which we believe to be most straightforward, but easily adjustable workflow:

## Object types

Here is a quick summary of the types of objects we use internally, and are exposed (or passed around to your system).

### Notification Factory

A notification factory takes collects common data for it's notifications, namely:

* _origin_: in most cases, this is the plugin (or module) that may create notifications on this library.
* _payload type_: an arbitrary description of the type of content given to the notification. This is useful for systems that don't use classes very heavily (i.e. Drupal and WordPress), but also for debugging and general ease of filtering.
* _payload_: the content that actually triggered the notification. This is again arbitrary, so anything in your system can trigger a notification creation process.
* _operation of payload_: the operation that has been executed (or will or is being executed) on the payload. This is again arbitrary, so a simple viewing of content can trigger the notification creation process.

### Nofication Object

### Recipient

### Sender