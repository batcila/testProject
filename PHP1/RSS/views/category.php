<?php
	Global $_connection;
	
	database_open();

	$user = exec_query('SELECT category_id,category_name FROM categories');
			
	if(mysqli_num_rows($user)){

		$select = '<select name="select">';
		while($rs = mysqli_fetch_assoc($user)){
    		$select .= '<option value="'.$rs['category_id'].'">'.$rs['category_name'].'</option>';
  		}
  	}
	$select.='</select>';
	echo $select;

?>


