<?php 
	mb_internal_encoding('UTF-8');
	$adjective_db = array('ангелско', 'topless', 'small', 'буен', 'влажен', 'гол', 'див', 'екзалтиран', 'жълт', 'злобен', 'интелигентен', 'йоден', 'кУрав', 'лигав', 'мъжки', 'назначен', 'отворен', 'прост', 'рязък', 'слаб', 'тиквен', 'унгарски', 'футболен', 'хрисим', 'чоплещ', 'цветен', 'шарен', 'щастлив', 'ь - май няма такова животно ?', 'ъглов', 'южен', 'ясен', 'alive', 'boobles', 'cool', 'danish', 'easy', 'faithful', 'godlike', 'helpful', 'i', 'joke', 'kind', 'little', 'magnetic', 'noob', 'open', 'pink'); // списък с прилагателни (непълен)


	function diagonal_type($name_input) {
		$name_no_spaces = str_replace(' ', '', $name_input);
		$name_size = mb_strlen($name_no_spaces);
		$space = '+';
		$output_var = '';
		for ($i=0; $i < $name_size; $i++) { 
			for ($u=0; $u < $i; $u++) { 
				$output_var .= $space;
			}
			$output_var .= mb_substr($name_no_spaces, $i, 1) . '<br />' . PHP_EOL;
		}
		return $output_var;
	}

	function table_diag_type($name_input) { // Шахматна 
		$name_no_spaces = str_replace(' ', '', $name_input);
		$name_size = mb_strlen($name_no_spaces);
		$table_html = '<table>';
		$class_code = '';
		for ($i=0; $i < $name_size; $i++) {
			$table_html .= '<tr>';
			for ($u=0; $u < $name_size; $u++) { 
				if ( ( ($i % 2) == 0 && ($u % 2) == 0) || ( ($i % 2) !== 0 && ($u % 2) !== 0) ) {
					$class_code = ' class="black"';
				} else {
					$class_code = ' class="white"';
				}
				if ($i == $u) {
					$table_html .= '<td' . $class_code . '>' . mb_substr($name_no_spaces, $i, 1) . '</td>' . PHP_EOL;
				} else {
					$table_html .= '<td' . $class_code . '>&nbsp;</td>' . PHP_EOL;
				}
			}

			$table_html .= '</tr>';
		}
		$table_html .= '</table><br />';
		return $table_html;
	}

	function check_for_adj_print($name_input_with_spaces) { // Проверява и извежда прилагателни в вертикален ред
		$name_input = str_replace(' ', '', $name_input_with_spaces);
		global $adjective_db;
		$adjective_array = array(); //празен масив с намерените прилагателни
		$max_lenght_adj = 0;
		for ($u=0; $u < mb_strlen($name_input); $u++) { 
			for ($i=0; $i < count($adjective_db); $i++) {
				$temp_string = $adjective_db[$i];
				if ( mb_substr($name_input, $u, 1) == mb_substr($temp_string, 0, 1) ) {
					$adjective_array[$u] = $adjective_db[$i];
					if ( mb_strlen($adjective_db[$i]) > $max_lenght_adj ) { // проверка за максимална дължина на прилагателно, за да се зададе на цикъла за извеждането
						$max_lenght_adj = mb_strlen($adjective_db[$i]);
					}
				}
			}
		}
		// генерира езхода от функцията
		$output_var = '<code>';
		for ($u=0; $u < $max_lenght_adj; $u++) { 
			for ($i=0; $i < mb_strlen($name_input); $i++) {
				if ( isset($adjective_array[$i]) && mb_substr($adjective_array[$i], $u, 1) !== '') {
					$output_var .= mb_substr($adjective_array[$i], $u, 1);
				} else { 
					$output_var .= '&nbsp;';
				}
			}
			$output_var .= '<br />' . PHP_EOL;
		}
		$output_var .= '</code><br />';
		return $output_var;

	}

	function check_even_odd($name_input) { // проверява колко от символите са в стринг $even_str и колко са в $odd_str и извежда отговор
		$odd_count = 0;
		$even_count = 0;
		$even_str = 'аъоуеиaouey';
		$odd_str = 'бвгджзклмнпрстфхцчшщюяqwrtpsdfghjklzxcvbnm';
		for ($i=0; $i < mb_strlen($name_input); $i++) { 
				if ( mb_stripos($odd_str, mb_substr($name_input, $i, 1)) !== false) {
					$odd_count++;
				}
				if ( mb_stripos($even_str, mb_substr($name_input, $i, 1)) !== false) {
					$even_count++;
				}
		}
		$output_var = 'Нечетни:'. $odd_count. ' , Четни:'. $even_count;
		return $output_var;
	}
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8" />
	<title>PHP Exersize</title>
	<style type="text/css">
		table, td {
			border: 1px solid gray;
		}

		td {
			width: 15px;
		}

		.white {
			color: black;
			background-color: white;
		}

		.black {
			color:white;
			background-color: black;
		}
	</style>
</head>
<body>

	<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get">
		<input type="text" name="person_name" placeholder="Enter something:" value="<?php echo ( isset($_GET['person_name']) && $_GET['person_name']!='' ? $_GET['person_name'] : '' ); ?>" />
		<input type="submit" value="Submit" />
	</form>

	<?php 
		if (isset($_GET['person_name']) && $_GET['person_name']!='' ) {

			$person_name = $_GET['person_name'];
			echo '1: <br />'.diagonal_type($person_name).'<br />' . PHP_EOL; // first
			echo '2: <br />'.table_diag_type($person_name); // second
			echo '3: <br />'.check_for_adj_print($person_name); // third
			echo '4: <br />'.check_even_odd($person_name); // forth

		} else { echo ('<br /> Enter name string :)'); }
	?>

</body>
</html>