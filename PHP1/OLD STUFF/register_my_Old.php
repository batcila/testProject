<?php
	function generate_md5_pass($lenght = 8) {
		$allowed_chars = '0123456789!@#$%^&*()ABCDEF';
		$pass = '';
		for ($i=0; $i < $lenght; $i++) { 
			$pass .= substr($allowed_chars, rand(0, strlen($allowed_chars) -1), 1);
		}
		return md5($pass);
	}

	if (!empty($_POST)) {
		// All fields are required ?

		// Field Lenght .... ?

		// DB open
		$_connection = mysql_connect('localhost', 'student', 'bgosoftware2016', 'wa-614');
		
		// Username exist ? 

		// generate_md5_pass();

		// Insert to DB

		// Show errors
	}


?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8" />
	<title>RSS System - Register account</title>
	<style type="text/css">
		label input {
			display: block;
		}
	</style>
</head>
<body>
	<form method="post" >
		<?php // echo generate_md5_pass()?>
		<fieldset>
			<legend>Register</legend>
			<label>Username:<input type="text" name="username" value="" /><label>
			<label>First Name:<input type="text" name="first_name" value="" /><label>
			<label>Last Name:<input type="text" name="last_name" value="" /><label>
			<label>E-mail:<input type="text" name="e_mail" value="" /><label>
			<br />
			<input type="reset" value="Clear" />
			<input type="submit" value="Send" />
		</fieldset>
	</form>
</body>
</html>