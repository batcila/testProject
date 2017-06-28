<?php
session_start();
mb_internal_encoding("UTF-8");
$msg = 'It is ALIVE !!!';

function output_func() { // main function whitch is called when you have Get parameter with name 'input_string'.
	$input_str = $_GET['input_string'];
	$mid_string = trim($input_str, '"'); 
	$output_str = '';
	for ($i=0; $i < mb_strlen($mid_string) ; $i++) { 
		if ($i % 2 == 0) {
			$output_str .= '<span class="' . ($i == 0 ? 'first' : 'red') . '">' . mb_strtoupper(mb_substr($mid_string, $i, 1)) . '</span>';
		} else {
			$output_str .= mb_substr($mid_string, $i, 1);
		}
	}
	save_to_session(); 
	save_to_file(); 
	if (check_incl()) { // check if there are letteres in the input field and if there are calls save to cokie 
		save_to_cookie();
	}
	return $output_str; // return output string for user 
}

function check_incl() {
	$search_array = array('Л', 'М', 'Р', 'Н'); //array with search letters (case sensitive !)
	foreach ($search_array as $key => $value) {
		if (mb_strpos($_GET['input_string'], $value) !== false) {
    		return true;
		}
	}
	return false; // if not found
}

function save_to_session() {
	$_SESSION['user_input'] = htmlspecialchars($_GET['input_string']);
}

function save_to_file() {
	$file_data = array();
	if (is_file('user_data.txt')) { // check if file exist
		$file_input_data = unserialize(file_get_contents('user_data.txt'));
		$file_data[] = htmlspecialchars($_GET['input_string']);
		foreach ($file_input_data as $key => $value) {
			$file_data[] = $value ;
		} 
	} else {
		$file_data[] = htmlspecialchars($_GET['input_string']);
	}
	file_put_contents('user_data.txt',  serialize($file_data));
}

function save_to_cookie() {
	setcookie('user_input',htmlspecialchars($_GET['input_string']),time()+60*60);
}

function alert_session_var() {
	return 'Session Variable is: ' . $_SESSION['user_input'];
}

function alert_cookie_var() {
	if (isset($_COOKIE['user_input'])) { return 'Cookie Variable is: ' . $_COOKIE['user_input']; } else { return 'Cookie Variable is not set'; };
}

function alert_file_data() {
	if (is_file('user_data.txt')) {
		$file_input_data = unserialize(file_get_contents('user_data.txt'));
		$file_input_data = implode(", ",$file_input_data);
	} else { $file_input_data = 'Missing !!!'; }

	return 'File data: ' . $file_input_data;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>PHP 1 Pre exam</title>
	<style type="text/css">
	.red {
		color: red;
	}
	.first {
		color: blue;
		text-decoration: underline;
	}
	</style>
</head>
<body>
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get">
		<input type="text" name="input_string" placeholder="Enter Input" value="<?php echo (isset($_GET['input_string']) && $_GET['input_string']!= '' ? htmlspecialchars($_GET['input_string']) : '' ); ?>" />
		<input type="submit" value="Submit" />
		<br /><br />
	</form>

	<?php if (isset($_GET['input_string'])) { ?>
		<div>Output: <?php echo(output_func()); ?></div><br />
		<script type="text/javascript"> alert("<?php echo $msg; ?>");</script>
	<?php } ?>

	<input type="button" value="Alert Session Variable" onclick="alertSessionVar()" /><br /><br />
	<input type="button" value="Alert Cookie Variable" onclick="alertCookieVar()" /><br /><br />
	<input type="button" value="Alert File Data" onclick="alertFileData()" />

</body>
<script type="text/javascript">
	function alertSessionVar() {
		alert("<?php echo(alert_session_var()); ?>");
	}
	function alertCookieVar() {
		alert("<?php echo(alert_cookie_var());  ?>");
	}
	function alertFileData() {
		alert("<?php echo(alert_file_data()); ?>");
	}
</script>
</html>