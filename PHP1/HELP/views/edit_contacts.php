<?php
	Global $_connection;
	
	// Database open
	database_open();
			
	if(!empty($_POST))
	{
		$user = $_POST;
		// All Fields are required?!
		if(true)
		{
			//add_error_message('Try again later');
			if(!has_error_messages())
			{
				$user_contact_array = array(
					//'username' => $_POST['username'],
					//'password' => md5(generate_pass()),
					'user_email' => $_POST['email'],
					'user_phone' => $_POST['phone'],
					'user_skype' => $_POST['skype'],
					'user_fb' => $_POST['fb']
					//'created_on' => strtotime('now')
				);
				
				// pre_print($user_contact_array);
				update_query('user_contacts', $user_contact_array, 'user_id = ' . get_current_user_id()); // Insert To DB
				add_success_message('Edit with Success ');
				
				header('location:'.'http://'.$_SERVER['SERVER_NAME'].$_SERVER['PHP_SELF'].'?view=edit_contacts');
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
			<legend>Update User Contacts</legend>
			
			<label>Username <input type="text" name="username" value="<?php echo $user['username']; ?>" readonly="readonly" /></label>
			<label>First Name <input type="text" name="first_name" value="<?php echo $user['first_name']; ?>" readonly="readonly" /></label>
			<label>Last Name <input type="text" name="last_name" value="<?php echo $user['last_name']; ?>" readonly="readonly" /></label>
			<label>Email <input type="text" name="email" value="<?php echo $user['user_email']; ?>" /></label>
			<label>Phone<input type="text" name="phone" value="<?php echo $user['user_phone']; ?>" /></label>
			<label>Skype<input type="text" name="skype" value="<?php echo $user['user_skype']; ?>" /></label>
			<label>Facebook<input type="text" name="fb" value="<?php echo $user['user_fb']; ?>" /></label>

			<input type="reset" value="Clear" />
			<input type="submit" value="Send" />
		</fieldset>
	</form>