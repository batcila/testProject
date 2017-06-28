<html>
	<head>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
		<meta charset="utf-8" />
		<style>
			.error_message{
				color: white; 
				background-color: red;
				border:2px double maroon;
			}
			.success_message{
				color: white; 
				background-color: lime;
				border:2px double green;
			}
			.info_message{
				color: white; 
				background-color: red;
				border:2px double maroon;
			}
			.alert_message{color: yellow;}
			
			label input{ display: block; }
			a{
				margin:5px; 
				padding:5px;
				border:1px solid silver; 
				border-radius:5px;
				text-decoration:none;
			}

			*[readonly="readonly"] {
				cursor: not-allowed;
			}
		</style>
	</head>
	<body>
		<?php
			if(isset($_SESSION['CURRENT_USER']))
			{
				echo '<a href="./?view=edit_user">Edit User</a>';
				echo '<a href="./?view=edit_contacts">Edit Contacts</a>';
				echo '<a href="./?view=logout">Logout</a>';
			}
			else
			{
				echo '<a href="./?view=register">Register</a>';
				echo '<a href="./?view=login">Login</a>';
			}
			
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
			
			echo '<br /><br />';

			include($view_file);
			//echo $view_file.' NOT FOUND!';
			//echo 'http://'.$_SERVER['SERVER_NAME'].$_SERVER['PHP_SELF'];
		?>
		<script>
			$(document).ready(function(){
				$("button.close").click(function(){
					$(this).parent().remove();
				});
			});
		</script>
	</body>
</html>