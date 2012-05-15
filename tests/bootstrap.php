<?php

/**
 * This file loads all the library files of the notifications API.
 * This is _not_ a file with tests; it only loads the files.
 *
 * @author Maarten Jacobs
 **/

define('BOOTSTRAP_ROOT', __DIR__ . '/../');

require_once BOOTSTRAP_ROOT . 'actor.php';
require_once BOOTSTRAP_ROOT . 'recipient.php';
require_once BOOTSTRAP_ROOT . 'sender.php';
require_once BOOTSTRAP_ROOT . 'exceptions.php';
require_once BOOTSTRAP_ROOT . 'notifications_api_factory_queue.php';
require_once BOOTSTRAP_ROOT . 'notifications_api_notification.php';