<?php
	define('ERROR_MSG', 0, true);
	define('GOOD_MGS', 1, true);
	define('JUST_MGS', 2, true);

	if (!function_exists('cappitalize_srt')) {
		function cappitalize_srt($input_srt) {
			// convert string to array
			$input_srt = explode(' ', $input_srt);
			$names_count = count($input_srt);

			for ($i=0; $i < $names_count ; $i++) { 
				
				$str_chars = str_split($input_srt[$i]);
				$first_key = reset(array_keys($str_chars));
				$str_chars[$first_key] = '<span class="big_char">'.$str_chars[$first_key].'</span>';
				$input_srt[$i] = implode('', $str_chars);

			}

				$input_srt = implode(' ', $input_srt);
				$first_key = 0;
				
			return $input_srt;
		}
	}
	
	function show_msg($msg_text, $msg_type = JUST_MGS) {
		$style = '';
		switch ($msg_type) {
			case ERROR_MSG:
				$style = 'color:red';
				break;
			case GOOD_MGS:
				$style = 'color:blue';
				break;
			case JUST_MGS:
				$style = 'color:green';
				break;
			
			default:
				$style = 'color:gray';
				break;
		}
		echo '<span style='.$style.'>'.$msg_text.'</span>';
	}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8" />
	<title>PHP 3: Array and strings</title>
	<style type="text/css">
		.big_char {
			background-color: yellow;
			color:red;
		}
	</style>
</head>

<body>
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get">
		<input type="text" name="person_name" placeholder="Enter name:" value="<?php echo ( isset($_GET['person_name']) && $_GET['person_name']!='' ? $_GET['person_name'] : '' ); ?>" />
		<input type="submit" value="Submit" />
	</form>

	<?php 
		if (isset($_GET['person_name']) && $_GET['person_name']!='' ) {
			echo cappitalize_srt($_GET['person_name']);
		} else { show_msg('Get variable not set!',ERROR_MSG); }
		
	?>
</body>
</html>