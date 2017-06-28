<?php
	session_start();
	error_reporting(E_ALL ^ E_NOTICE);
	
	require('helpers/functions.php');
	require('helpers/messages.php');
	require('helpers/database.php');
	require('helpers/users.php');
	require('config.php');

	construct_messages();

	Global $_connection;

	
	if(!empty($_POST)) {

		if(	isset($_POST['full_name']) && $_POST['full_name'] != '') { // uslovie 1 

			if(	!isset($_POST['gender']) ) { // uslovie 2
				$sex = 2;
			} else {
				$sex = $_POST['gender'];
			}

			if(	isset($_POST['language']) && is_array($_POST['language']) ) { // uslovie 3 

				// Database open 
				database_open();
				// echo 'all seems ok!';
				$proceed = true;

				$data_array = array(
					'full_name' => $_POST['full_name'],
					'gender' => $_POST['gender'],
					'born_date' => $_POST['born_date']
					 );
				
				
			} else {
				add_error_message('Languages are not array!');
			}

		} else {
			add_error_message('Трите имена required!');
		}

	}

?>

<!DOCTYPE xhtml>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<title>Document</title>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.0/jquery.min.js"></script>
	<style type="text/css">

	</style>
</head>

<body>

<?php
if(have_messages())
			{
				echo show_all_messages();
				clear_all_messages();
			}
?>

<form method="post">
	<fieldset>
		<legend>Лични данни</legend>
		<label>Трите имена*:<input type="text" name="full_name" value="<?php echo (isset($_POST['full_name']) && $_POST['full_name']!= '' ? htmlspecialchars($_POST['full_name']) : '' ); ?>" placeholder="Въведете трите си имена"/></label>
		<br />
		Пол:<label><input type="radio" name="gender" value="1" <?php echo ($sex=='1')?'checked':'' ?> > Мъж</label>
		<label><input type="radio" name="gender" value="2" <?php echo ($sex=='2')?'checked':'' ?> > Не казвам</label>
		<label><input type="radio" name="gender" value="3" <?php echo ($sex=='3')?'checked':'' ?>> Жена</label>
		<br />
		<label>Дата на раждане:<input type="date" name="born_date" value="" /></label>
		<br />
		<label>Роден град:
		<select name="home_town">
			<option value="Plovdiv">Майнатаун</option>
			<option value="Sofia">София</option>
			<option value="Mezdra">Мездра</option>
		</select></label>
		<br />
		Говорими езици:
		<label>Български<input type="checkbox" name="language[]" value="1" checked="checked"></label>
		<label>Английски<input type="checkbox" name="language[]" value="2"></label>
		<label>Хинди<input type="checkbox" name="language[]" value="3"></label>
		<br />
		<label>Телефон за контакт:<input type="phone" name="phone_numb" value="" /></label>
		<br />
		<textarea name="some_text" placeholder="Моля, напишете ни нещо интерено! Нека споделим мига. ;-)"></textarea>
		<br />
		<input type="submit" value="Submit"></button>
		<input type="reset" value="Reset"></button>
	</fieldset>
</form>

<?php 

if ( isset($proceed) && $proceed == true ) { 
	?>

	<div>
		Form data is ok!

		<button type="button">Show Data</button>
		<button type="button">Add to database</button>
		<button type="button">Edit database</button>
		<button type="button">Show all users</button>
		<button type="button">Show all (speaking Eng and German)</button>
		<button type="button">Show all (with astral signs)</button>
		<button type="button">Show all last bullet</button>

	</div>

	<?php
}
?>
</body>
</html>