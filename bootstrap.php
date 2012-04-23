<?php
foreach( glob(__DIR__ . '/src/notifications_api/*.php') as $file ){
	require_once $file;
}