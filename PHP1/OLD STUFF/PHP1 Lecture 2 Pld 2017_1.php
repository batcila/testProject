<?php
	define ('DEFAULT_YEAR', 1987, true);
	$current_year = date('Y');
	$result = '';
	/*
		0. $age is number
		1. $age < $current_year
		2. $age != 'xxxx'
		3. if $age is in short format - +19/+20/200 
	*/
	if (isset($_GET['age'])) {
		$result = $current_year - $_GET['age'];
	}
?>

<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<title>Обработка на форми</title>
</head>

<body>
	<?php echo $result; ?>
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get">
		<input type="number" name="age" value="<?php echo ( isset($_GET['age']) && is_numeric('age') ? $_GET['age'] : DEFAULT_YEAR ); ?>" />
		<input type="submit" value="Submit" />
	</form>

</body>
</html>