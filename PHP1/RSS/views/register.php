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
		if(
			isset($_POST['username']) 
			&& isset($_POST['first_name']) 
			&& isset($_POST['last_name'])
			&& $_POST['username'] != ''
			&& $_POST['first_name'] != ''
			&& $_POST['last_name'] != ''
		)
		{
			// Fields Length .... ?
			if(mb_strlen($_POST['username'], 'utf-8') < 4)
			{
				add_error_message('Username must be 4 or more symbols!');
			}
			
			// Database open
			database_open();
			
			// Username exists?!
			$user = exec_query('SELECT * FROM users WHERE username = "'.mysqli_real_escape_string($_connection, $_POST['username']).'"');
			$user = to_assoc_array($user);
			
			//pre_print($user);
			if(count($user) > 0)
			{
				add_error_message('This username already exists!');
			}
			
			if(!has_error_messages())
			{
				$user_array = array(
					'username' => $_POST['username'],
					'password' => md5(generate_pass()),
					'first_name' => $_POST['first_name'],
					'last_name' => $_POST['last_name'],
					'created_on' => strtotime('now')
				);
				
				//pre_print($user_array);
				$new_user_id = insert_query('users', $user_array); // Insert To DB
				add_success_message('Success #'.$new_user_id.' ('.$user_array['username'].', '.$user_array['password'].')');
				
				header('location:'.'http://'.$_SERVER['SERVER_NAME'].$_SERVER['PHP_SELF'].'?view=login');
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
			<legend>Register</legend>
			<?php //echo md5(generate_pass()); ?>
			<label>Username <input type="text" name="username" value="" /></label>
			<label>First Name <input type="text" name="first_name" value="" /></label>
			<label>Last Name <input type="text" name="last_name" value="" /></label>
			<input type="reset" value="Clear" />
			<input type="submit" value="Send" />
		</fieldset>
	</form>