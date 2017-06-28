<?php 

	$something = array(
		'first' => 'Yordan',
		'last' => 'Enev',
		'today' => date('Y-m-d H:i:s'),
		'somethuing' => array(
			'asd' => 'asd',
			'asdasd' => 'avwgf'
			)
		);
	
	echo json_encode($something);

?>