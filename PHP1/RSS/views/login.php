<?php
	Global $_connection;
	
	if(isset($_SESSION['CURRENT_USER']))
	{
		header('location:'.'http://'.$_SERVER['SERVER_NAME'].$_SERVER['PHP_SELF'].'?view=index');
		exit;
	}
	
	if(!empty($_POST))
	{
		// All Fields are required?!
		if(	isset($_POST['username']) && isset($_POST['password']) )
		{
			// Database open
			database_open();
			
			// Username exists?!
			$query = 'SELECT * FROM users WHERE username = "'.mysqli_real_escape_string($_connection, $_POST['username']).'"';
			$query .= ' AND (password = "'.mysqli_real_escape_string($_connection, $_POST['password']).'" OR password = MD5("'.mysqli_real_escape_string($_connection, $_POST['password']).'"))';
			//echo SQL_DEBUG($query);
			
			$user = exec_query($query);
			$user = to_assoc_array($user);
			
			//pre_print($user);
			if(count($user) == 0)
			{
				add_error_message('Wrong User or Pass!');

				header('location:'.'http://'.$_SERVER['SERVER_NAME'].$_SERVER['PHP_SELF'].'?view=login');
				exit;
			}
			else
			{
				//pre_print($user);
				$_SESSION['CURRENT_USER'] = reset($user);
				add_success_message('Success Login!');
				
				header('location:'.'http://'.$_SERVER['SERVER_NAME'].$_SERVER['PHP_SELF'].'?view=index');
				exit;
			}
		}
		else
		{
			add_error_message('All fields are required!');
		}
	}
?>

	<form method="post">
		<fieldset>
			<legend>Login</legend>
			<label>Username <input type="text" name="username" value="" /></label>
			<label>Password <input type="password" name="password" value="" /></label>
			<input type="reset" value="Clear" />
			<input type="submit" value="Send" />
		</fieldset>
	</form>