<html>
	<head>
		<meta charset="utf-8" />
		<style>
			.error_message{color: red;}
			.success_message{color: green;}
			.info_message{color: blue;}
			.alert_message{color: yellow;}
			
			label input{ display: block; }
		</style>
	</head>
	<body>
		<?php
			if(have_messages())
			{
				echo show_all_messages();
				clear_all_messages();
			}
			
			if(!isset($_GET['view']))
			{
				$_GET['view'] = 'index';
			}
			
			$view_file = 'views/'.strtolower($_GET['view']).'.php';
			
			if(!file_exists($view_file))
			{
				$view_file = 'views/404.php';
			}
			
			if(!isset($_SESSION['CURRENT_USER'])) {
				echo '<a href="' . 'http://'.$_SERVER['SERVER_NAME'].$_SERVER['PHP_SELF'].'?view=register' . '">Register</a>';
				echo '<a href="' . 'http://'.$_SERVER['SERVER_NAME'].$_SERVER['PHP_SELF'].'?view=login' . '">Login</a>';
			} else {
				echo '<a href="' . 'http://'.$_SERVER['SERVER_NAME'].$_SERVER['PHP_SELF'].'?view=logout' . '">Logout</a>';
			}

			include($view_file);
			//echo $view_file.' NOT FOUND!';
			//echo 'http://'.$_SERVER['SERVER_NAME'].$_SERVER['PHP_SELF'];
		?>
	</body>
</html>