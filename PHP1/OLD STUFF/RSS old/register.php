<?php
	function generate_pass($lenght = 6)
	{
		$allow_chars = '0123456789!@#$%^&*()ABCDEF';

		$pass = '';

		for ($i=0; $i < $lenght; $i++) {
			$pass .= substr($allow_chars, rand(0, strlen($allow_chars) - 1), 1);
		}

		return $pass;
	}

	if (!empty($_POST)) {

		//All Fields are required?!
		if (
			isset($_POST['username'])
			&& isset($_POST['first_name'])
			&& isset($_POST['last_name'])
			&& $_POST['username']
			&& $_POST['first_name']
			&& $_POST['first_name']
		) {
			# code...
		}

		//Fields Lenght ... ?

		//Database open
		$_connection = mysqli_connect('localhost', 'student', 'bgosoftware2016', 'wa-706');
		$db = mysqli_select_db($conn, 'wa-706');

		//Username exist

		//Generate_pass();

		//Insert to DB

		//Show Errors
	}

?>
<html>
	<head>
		<meta charset="utf-8" />
		<title>RSS</title>
		<style type="text/css">
			label input {
				display: block;
			}
			fieldset {
				width: 20%;
			}
		</style>
	</head>
	<body>
		<form method="post">
			<fieldset>
				<legend>Register</legend>
				<?php //echo md5(generate_pass()); ?>
				<label>Username: <input type="text" name="username" value="" /></label>
				<label>First Name: <input type="text" name="first_name" value="" /></label>
				<label>Last Name: <input type="text" name="last_name" value="" /></label>
				<input type="submit" value="Send" /><input type="reset" vlaue="Clear" />
			</fieldset>
		</form>
	</body>
</html>