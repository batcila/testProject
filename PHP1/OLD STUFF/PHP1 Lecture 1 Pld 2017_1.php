<?php 
	function pre_print($variables) {
		echo '<pre>'.print_r($variables,true).'</pre>';
	}
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8" />
	<title>Lecture 1</title>
</head>
<body>

	<?php
		$test = 'Testing...';
		echo pre_print($test);

		$i = 0;
		do {
    		echo $i++;
		} while ($i < 3);

	?>

	<details open="open">
		<summary>Reference</summary>
		<?php
			$arr = array(1,2,3,4);
			pre_print($arr).'<br />';
			foreach ($arr as &$value) {
				$value *= 2;
			}
			pre_print($arr);
			define('PI', 3.14, true);
			if (PI > 2) {
				echo PI. ' is bigger than 2';
			} else {
				echo PI. ' is not bigger than 2';
			}
			echo '<br />';
			$booleans = true;
			var_dump($booleans);
			
			// for ($i=1001; $i < 1005; $i++) { 
			// 	for ($s=171001; $s < 171015; $s++) { 
			// 		echo '<br />wa-'.$s.'-'.$i;
			// 	}
			// }

			echo '<br />'.date('l jS \of F Y h:i:s A');

		?>


	</details>

	<details open="open">
		<summary>Switch</summary>
		<?php 

			$some_number = '1';
			switch (true) {
				case ($some_number === 1 && ):
					echo 'Number is one';
					break;

				case ($some_number === 2):
					echo 'Number is 101 ;)';
					break;
				
				default:
					echo 'Number is not in switch case';
					break;
			}
		?>
	</details>
	
</body>
</html>