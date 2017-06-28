<?php
	function pre_print($something)
	{
		echo '<pre>'.print_r($something, true).'</pre>';
	}
?>