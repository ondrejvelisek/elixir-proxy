<?php
/* 
 * Configuration for the Cron module.
 */

require '/etc/elixir-proxy/module_cron.php';

$config = array (

	'key' => SECRET_KEY,
	'allowed_tags' => array('daily', 'hourly', 'frequent'),
	'debug_message' => FALSE,
	'sendemail' => FALSE,

);
