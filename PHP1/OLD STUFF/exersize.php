<?php 
	
	$names_db = array('wax', 'blemish', 'possession', 'prayer', 'bench', 'dogtooth', 'cortex', 'worm', 'human', 'collectable');
	$friends_msg_db = array(123, 12233, 453, 7346, 1762, 07835, 89172, 87162, 7123, 87124);
	$adjective_db = array('голям', 'малък', 'бърз', 'open', 'опасен', 'лош', 'рано', 'важен', 'жив', 'alive', 'boob', 'cat');

	$frd_array = array_combine($friends_msg_db, $names_db);

	function search_in_adj($person_name) {
		global $adjective_db;
		$adjective_array = array();
		for ($u=0; $u < mb_strlen($person_name, "utf-8"); $u++) { 
			for ($i=0; $i < count($adjective_db); $i++) {
				$temp_string = $adjective_db[$i];
				if ( mb_substr($person_name, $u, 1, "utf-8") == mb_substr($temp_string, 0, 1, "utf-8") ) {
					$adjective_array[$u] = $adjective_db[$i];
				}
			}
		}
		return $adjective_array;
	}

	function generate_name($input_string,$person_name) {
		$html_output = '<br /><div> ';
		for ($i=0; $i < mb_strlen($person_name, "utf-8"); $i++) {
			if (array_key_exists($i, $input_string)) {
			    $html_output .= mb_substr($person_name, $i, 1, 'utf-8'). ' - ' .$input_string[$i].'<br />';
			} else {
				$html_output .= mb_substr($person_name, $i, 1, 'utf-8'). ' - ' .'<br />';;
			}
		}
		$html_output .= '</div>';
		return $html_output;
	}

	function search_by_name($person_name) {
		$html_output = '<br /><div> ';
		global $names_db;
		for ($i=0; $i < count($names_db); $i++) {
			if (mb_strpos($names_db[$i], $person_name, 0, 'utf-8') !== false) {
				$name_in_db = $names_db[$i];
				$start_char = mb_strpos($names_db[$i], $person_name, 0, 'utf-8');
				$a = substr($name_in_db, 0, $start_char);
				$b = substr($name_in_db, $start_char, mb_strlen($person_name, "utf-8"));
				$c = substr($name_in_db, mb_strlen($person_name, "utf-8") + $start_char, mb_strlen($names_db[$i], "utf-8"));
				$html_output .= $a . '<span style="color:red; background-color:yellow ">' . $b . '</span>' . $c . '<br />';
			} 
		}
		return $html_output;
	}

	function sort_friends() {
		global $frd_array;
		krsort($frd_array);
		echo '<br />';
		print_r($frd_array);
	}
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Document</title>
</head>
<body>

	<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get">
		<input type="text" name="person_name" placeholder="Enter name:" value="<?php echo ( isset($_GET['person_name']) && $_GET['person_name']!='' ? $_GET['person_name'] : '' ); ?>" />
		<input type="submit" value="Submit" />
	</form>

	<?php 
		if (isset($_GET['person_name']) && $_GET['person_name']!='' ) {

			$person_name_str = $_GET['person_name'];

			$name_temp = search_in_adj($person_name_str);

			echo generate_name($name_temp,$person_name_str);
			echo search_by_name($person_name_str);

			sort_friends();

		} else { echo ('Въведи име!'); }
	?>

</body>
</html>