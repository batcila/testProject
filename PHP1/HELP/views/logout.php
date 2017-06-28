<?php
	if(isset($_SESSION['CURRENT_USER']))
	{
		$_SESSION['CURRENT_USER'] = false;	
		unset($_SESSION['CURRENT_USER']);
		//session_destroy();
		
		add_success_message('Logout ok!');
		header('location:'.'http://'.$_SERVER['SERVER_NAME'].$_SERVER['PHP_SELF'].'?view=index');
		exit;
	}
	else
	{
		header('location:'.'http://'.$_SERVER['SERVER_NAME'].$_SERVER['PHP_SELF'].'?view=login');
		exit;
	}
?>