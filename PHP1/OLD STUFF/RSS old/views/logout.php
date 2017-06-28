<?php
	Global $_connection;
	
	if(isset($_SESSION['CURRENT_USER'])){
		$_SESSION['CURRENT_USER'] = false;
		unset($_SESSION['CURRENT_USER']);
		add_success_message('Success Logged Out!');
	} else {
		add_error_message('You are not logged in!');
	}
	header('location:'.'http://'.$_SERVER['SERVER_NAME'].$_SERVER['PHP_SELF'].'?view=index');
	exit;

?>

	<form method="post">
		<fieldset>
			<legend>Logout</legend>
		</fieldset>
	</form>