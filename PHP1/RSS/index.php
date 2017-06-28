<?php
	session_start();
	error_reporting(E_ALL ^ E_NOTICE);
	
	require('helpers/functions.php');
	require('helpers/messages.php');
	require('helpers/database.php');
	require('helpers/users.php');
	require('config.php');
	
	construct_messages();
	
	require('design.php');
?>