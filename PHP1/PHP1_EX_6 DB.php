<?php

$sql_error = false;
$servername = "localhost";
$username = "username";
$password = "password";
// Create connection
$conn = mysqli_connect($servername, $username, $password);
// Check connection
if (!$conn) {
     $sql_error = true;
    // die("Connection failed: " . mysqli_connect_error());
}
// echo "Connected successfully";

?>

<!DOCTYPE html>
<html lang="en">
<head>

	<meta charset="UTF-8" />
	<title>Document</title>

	<script type="text/javascript">
		function readDB() {
			tableID = document.getElementById('tableID');
			console.log('<?php echo $sql_error; ?>');
		}
		readDB();
	</script>

</head>
<body>
	<table id="tableID"></table>
</body>
</html>