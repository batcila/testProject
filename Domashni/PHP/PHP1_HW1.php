<?php
	define('ERROR_MSG', 0, true);
	define('GOOD_MGS', 1, true);
	define('JUST_MGS', 2, true);
	
	function is_negative($number_param) {
		if ($number_param < 0) {
			show_message('Това е негативно число! Трябва да бъде позитивно!',ERROR_MSG);
			return true;
		} else { return false; }
	}

	function show_message($msg_text, $msg_type = JUST_MGS) {
		$style = '';
		switch ($msg_type) {
			case ERROR_MSG:
				$style = 'color:red';
				break;
			case GOOD_MGS:
				$style = 'color:green';
				break;
			case JUST_MGS:
				$style = 'color:blue';
				break;
		}
		$mgs = '<span style='.$style.'>'.$msg_text.'</span>';
		if ($_GET['add_years'] == 1 ) { $mgs = '<span style='.$style.'>'.$msg_text.' <sup>*добавени 1900 към годината ви преди изчислението </sup></span>';} // допълнително показва дали са добавени години при < 100
		echo $mgs;
	}

	function calc_age() {
		$current_year = date('Y');
		$calc_year = $_GET['birth_year'];
		$result = '';
		if ($calc_year < 100) { $calc_year += 1900 ; $_GET['add_years'] = 1 ;} else { $_GET['add_years'] = 0;}; 
		if ($calc_year > $current_year ) {
			show_message('Явно сте родени в бъдещето ?', JUST_MGS);
			return false;
		}
		$result = $current_year - $calc_year;
		if ($result == 1) { // за да покаже годинА а не годинИ
			show_message('Вие сте на '.$result. ' годинa !', GOOD_MGS); 
		} else {
			show_message('Вие сте на '.$result. ' години !', GOOD_MGS);
		}
	}

	function check_user_input($user_input) {
		switch (true) {
			case (is_numeric($user_input)):
				if (intval($user_input) != $user_input) { // проверка за цяло число
					show_message('Моля въведете цяло число!',ERROR_MSG);
					return false;
				}
				return true;
				break;
			case (is_string($user_input)):
				$_GET['add_years'] = 0; // за да не показва съобщението за добавени години 
				show_message('Това е текст! Трябва да въведете число!',ERROR_MSG);
				return false;
				break;
			default:
				show_message('Нещо се счупи ?',ERROR_MSG); // за всеки случай :)
				return false;
				break;
		}
	}

?>

<!DOCTYPE html>
<html lang="bg_BG">
<head>
	<meta charset="UTF-8" />
	<title>PHP 1: Homework 1</title>
	<style type="text/css">

	</style>
</head>

<body>

	<?php 
		if (isset($_GET['birth_year']) && $_GET['birth_year']!='' ) {
			if (!is_negative($_GET['birth_year']) && check_user_input($_GET['birth_year'])) { //викам функцията с ! защото условието и името са и такива (ако не е изпълнена всичко е наред)
				calc_age();
			}
		} 
	?>

	<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get">
		<input type="text" name="birth_year" placeholder="Въведете година:" value="<?php echo ( isset($_GET['birth_year']) && $_GET['birth_year']!='' ? $_GET['birth_year'] : '' ); ?>" />
		<input type="hidden" name="add_years" value="<?php echo ( isset($_GET['add_years']) && $_GET['add_years']!='' ? $_GET['add_years'] : 0 ); ?>" /> <!-- за допълнителното показване дали са добавени години -->
		<input type="submit" value="Calculate" />
	</form>

</body>
</html>