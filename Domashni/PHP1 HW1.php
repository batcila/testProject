<?php

	define ('DEFAULT_DATE', '1987-10-29', true);

	if (!function_exists('calc_date')) {
		function calc_date($birth_day) {

			echo '<br />log:'.$birth_day;
			
			$sum = 0;

			for ( $i = 0 ; $i < strlen($birth_day) ; $i++ ) {

				// echo '<br />log2:'.$i.':'.$birth_day[$i];
				// $temp = $birth_day[$i];
				// echo '<br />log3:'.$i.':'.$temp;
				// var_dump($birth_day[$i]);

				if ( is_numeric($birth_day[$i]) ) {

			 		$sum += $birth_day[$i];
				}
			}
			return $sum;
		}
	}

	$result = '';
	$astro_signs = array(
		'козирог', 'водолей', 'риби', 'овен',
		'телец', 'близнаци', 'рак', 'лъв', 
		'дева', 'везни', 'скорпион', 'стрелец'
		);

	if ( isset($_GET['person_date'])) {
		$my_day = (int) substr($_GET['person_date'], 8, 2);
		$my_month = (int) substr($_GET['person_date'], 5, 2);	
		
		$key = $my_month - 1; 
		if ( $my_day >= 23 ) { $key++; }
		$key = $key % 12;
		$result = 'Вашата зодия е: '.$astro_signs[$key];
	}

?>

<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8" />
	<title>Обработка на форми</title>
</head>

<body>
	<h1>Зодий</h1>

	<?php 
	echo $result;
	if ( isset($_GET['person_date'])) {
		echo '<img src="images/'.$key.'.jpg" />';
	}
	?>

	<br />
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get">

		<input type="text" name="person_date" value="<?php echo ( isset($_GET['person_date']) && $_GET['person_date']!= '' ? $_GET['person_date'] : DEFAULT_DATE ); ?>" />

		<input type="submit" value="Submit" />
		<br />

		<?php 
			
			if ( isset($_GET['person_date'])) { 

				$temp_numb = calc_date($_GET['person_date']);

				echo '<br />result1:'.$temp_numb;

				while ($temp_numb > 10) {
					// echo '<br /> temp res:'.$temp_numb;
					calc_date( $temp_numb );
				}

				echo '<br />result:'.$temp_numb;
			}
		?>

	</form>

</body>
</html>