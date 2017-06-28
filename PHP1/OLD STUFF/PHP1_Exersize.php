<?php 
	mb_internal_encoding('UTF-8');

	$names_db = array('Антон', 'Тошко', 'Alexander', 'Никола', 'Петя', 'Иван', 'Mike', 'Chester', 'Brad', 'Pheonix', 'Bill Gates'); // Приятели
	$friends_msg_db = array(123, 12233, 453, 73426, 1762, 07835, 18723, 89172, 87162, 7123, 87124); // брой приятели - трябва да е еднакво с големината на $names_db

	$adjective_db = array('ангелско', 'small', 'буен', 'влажен', 'гол', 'див', 'екзалтиран', 'жълт', 'злобен', 'интелигентен', 'йоден', 'кУрав', 'лигав', 'мъжки', 'назначен', 'отворен', 'прост', 'рязък', 'слаб', 'тиквен', 'унгарски', 'футболен', 'хрисим', 'чоплещ', 'цветен', 'шарен', 'щастлив', 'ь - май няма такова животно ?', 'ъглов', 'южен', 'ясен', 'alive', 'boobles', 'cool', 'danish', 'easy', 'faithful', 'godlike', 'helpful', 'i', 'joke', 'kind', 'little', 'magnetic', 'noob', 'open', 'pink'); // списък с прилагателни (непълен)

	function search_in_adj($person_name) { // TO DO: Трябва да се направи по-голям списък с прилагателни и да вади различни неща ако се повтаря буква в името
		global $adjective_db;
		$adjective_array = array();
		for ($u=0; $u < mb_strlen($person_name); $u++) { 
			for ($i=0; $i < count($adjective_db); $i++) {
				$temp_string = $adjective_db[$i];
				if ( mb_substr($person_name, $u, 1) == mb_substr($temp_string, 0, 1) ) {
					$adjective_array[$u] = $adjective_db[$i];
				}
			}
		}
		return $adjective_array;
	}

	function generate_adj_in_name($input_string,$person_name) { // Генерира подредени прилагателни спрямо името ... TO DO: Различно подреждане спрямо допълнителното условие
		$html_output = '<br />Adjectives in name:<br /><div> ';
		
		for ($i=0; $i < mb_strlen($person_name); $i++) {
			if (array_key_exists($i, $input_string)) {
			    $html_output .= mb_substr($person_name, $i, 1). ' - ' .$input_string[$i].'<br />';
			} else {
				$html_output .= mb_substr($person_name, $i, 1). ' - ' .'<br />';;
			}
		}
		
		$html_output .= '</div>';
		return $html_output;
	}

	function search_by_name($person_name) { // Търси и връща съвпадащи имена
		$html_output = '<br />Friends list: <div> ';
		global $names_db;
		$not_found = true;
		for ($i=0; $i < count($names_db); $i++) {
			if (mb_stripos($names_db[$i], $person_name, 0) !== false) { // mb_stripos - За да се търси без значение главна или малка буква.
				$not_found = false;
				$name_in_db = $names_db[$i];
				$start_char = mb_stripos($names_db[$i], $person_name, 0); 
				$a = mb_substr($name_in_db, 0, $start_char);
				$b = mb_substr($name_in_db, $start_char, mb_strlen($person_name));
				$c = mb_substr($name_in_db, mb_strlen($person_name, "utf-8") + $start_char, mb_strlen($names_db[$i]));
				$html_output .= $a . '<span style="color:red; background-color:yellow ">' . $b . '</span>' . $c . '<br />';
			}
		}
		$html_output .= '</div>';
		if ($not_found) {
			return '<br />No matching names found! <br />';
		} else {
			return $html_output;
		}
	}

	function sort_friends_and_print() { // Сортира и извежда имената на 5-те (или по-малко ако няма данни) приятели с най много връзки
		global $names_db;
		global $friends_msg_db;
		$frd_array = array_combine($friends_msg_db, $names_db);
		krsort($frd_array);
		$count_to_five = 0;
		$key_in_array = array_keys($frd_array);
		echo '<br />I found those friends with max connections:<br />';
		for ($i=0; $i < count($frd_array) && $count_to_five < 5; $i++) {
			$count_to_five++;
			echo 'Friend name: &quot;' . $frd_array[$key_in_array[$i]] . '&quot; Number of connections: ' . $key_in_array[$i] ;
			echo '<br />';
		}
	}

	// TO DO: Последната задача - не записах условието :/ 

?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Document</title>
</head>
<body>

	<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get">
		<input type="text" name="person_name" placeholder="Friend name:" value="<?php echo ( isset($_GET['person_name']) && $_GET['person_name']!='' ? $_GET['person_name'] : '' ); ?>" />
		<input type="submit" value="Submit" />
	</form>

	<?php 
		if (isset($_GET['person_name']) && $_GET['person_name']!='' ) {

			$person_name_str = $_GET['person_name'];
			$name_temp = search_in_adj($person_name_str);

			echo generate_adj_in_name($name_temp,$person_name_str);
			echo search_by_name($person_name_str);
			sort_friends_and_print();

		} else { echo ('<br /> Enter name please :)'); }
	?>

</body>
</html>