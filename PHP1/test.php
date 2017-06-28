<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>test</title>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script>
	<script>
	$(document).ready(function(){	

	$("#click").click(function(){
        $.post("http://localhost/~wa-160511-614/PHP1/echo.php",
        {
          name: "Donald Duck",
          city: "Duckburg"
        },
        function(data,status){
            var parseData = JSON.parse(data);
            console.log(parseData.somethuing);
        });
    });

		
	});
	</script>
</head>
<body>
	<div id="result"></div>
	<button id="click" type="button">AJAX</button>
</body>
</html>