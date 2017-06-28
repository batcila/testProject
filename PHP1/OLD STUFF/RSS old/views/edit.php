<?php
	Global $_connection;
	
	if(!empty($_POST))
	{
		if (isset($_SESSION['CURRENT_USER']))
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
			
				if(mb_strlen($_POST['username'], 'utf-8') < 4)
				{
					add_error_message('Username must be 4 or more symbols!');
				}
				
				database_open();
				
				$user = exec_query('SELECT * FROM users WHERE username = "'.mysqli_real_escape_string($_connection, $_POST['username']).'"');
				$user = to_assoc_array($user);
				
				
				if(!has_error_messages())
				{
					$user_array = array(
						'username' => $_POST['username'],
						'first_name' => $_POST['first_name'],
						'last_name' => $_POST['last_name']
					);
					
					//pre_print($user_array);
					
					add_success_message('Success editing ('.$user_array['username'].')');
					
					header('location:'.'http://'.$_SERVER['SERVER_NAME'].$_SERVER['PHP_SELF'].'?view=index');
					exit;
				}
			}
			else
			{
				add_error_message('All fields are required!');
			}
		} else {
			add_error_message('You are not logged to edit user!');
			header('location:'.'http://'.$_SERVER['SERVER_NAME'].$_SERVER['PHP_SELF'].'?view=index');
			exit;
		}
	}
?>

	<form method="post">
		<fieldset>
			<legend>Edit</legend>
			<?php //echo md5(generate_pass()); ?>
			<label>Username <input type="text" name="username" value="<?php echo username; ?>" /></label>
			<label>First Name <input type="text" name="first_name" value="" /></label>
			<label>Last Name <input type="text" name="last_name" value="" /></label>
			<input type="reset" value="Clear" />
			<input type="submit" value="Send" />
		</fieldset>
	</form>