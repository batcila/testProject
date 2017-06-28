<?php
	Global $_connection;
	
	// Database open
	database_open();
			
	if(!empty($_POST))
	{
		$user = $_POST;
		// All Fields are required?!
		if(
		isset($_POST['first_name']) 
			&& isset($_POST['last_name'])
			&& $_POST['first_name'] != ''
			&& $_POST['last_name'] != ''
		)
		{
			//add_error_message('Try again later');
			if(!has_error_messages())
			{
				$user_array = array(
					//'username' => $_POST['username'],
					//'password' => md5(generate_pass()),
					'first_name' => $_POST['first_name'],
					'last_name' => $_POST['last_name'],
					//'created_on' => strtotime('now')
				);
				
				//pre_print($user_array);
				update_query('users', $user_array, 'user_id='.get_current_user_id()); // Insert To DB
				add_success_message('Edit with Success ');
				
				header('location:'.'http://'.$_SERVER['SERVER_NAME'].$_SERVER['PHP_SELF'].'?view=edit_user');
				exit;
			}
		}
		else
		{
			add_error_message('All fields are required!');
		}
	}
	else
	{
		// Load User Data
		$user = get_user_by_id(get_current_user_id());

		// pre_print($user);

		if(count($user) == 1)
		{
			$user = reset($user);
		}
		else
		{
			add_error_message('User not found!');
		}
	}
?>

	<form method="post">
		<fieldset>
			<legend>Update User</legend>
			<?php //echo md5(generate_pass()); ?>
			<label>Username <input type="text" name="username" value="<?php echo $user['username']; ?>" readonly="readonly" /></label>
			<label>First Name <input type="text" name="first_name" value="<?php echo $user['first_name']; ?>" /></label>
			<label>Last Name <input type="text" name="last_name" value="<?php echo $user['last_name']; ?>" /></label>
			<input type="reset" value="Clear" />
			<input type="submit" value="Send" />
		</fieldset>
	</form>