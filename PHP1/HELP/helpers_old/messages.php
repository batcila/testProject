<?php
	define('MSG', 'SYSTEM_MESSAGES', true);
	$msg_types = array('error', 'success', 'info', 'alert');
	
	function construct_messages()
	{
		Global $msg_types;
		
		if(isset($_SESSION[MSG]) && is_array($_SESSION[MSG]))
		{
			// nothing ...
		}
		else
		{
			$_SESSION[MSG] = array();
		}
		
		// Празен масив за временни данни
		$tmp_array = array();
		
		// $k е от 0..N-1, т.е. ще го използваме като степен на 2
		foreach($msg_types as $k => $v)
		{
			$tmp_array[$v] = pow(2, $k); 
		}
		$tmp_array['all'] = array_sum($tmp_array); 
		
		$msg_types = $tmp_array;
		//pre_print($msg_types);
	}
	
	function add_message($message, $message_type = null)
	{
		Global $msg_types;
		
		if(is_null($message_type))
		{
			$message_type = $msg_types['info'];
		}
		$_SESSION[MSG][] = array('type' => $message_type, 'message' => $message);
	}
	
	function add_error_message($message)
	{
		Global $msg_types;
		
		add_message($message, $msg_types['error']);
	}
	
	function add_success_message($message)
	{
		Global $msg_types;
		
		add_message($message, $msg_types['success']);
	}
	
	function add_info_message($message)
	{
		Global $msg_types;
		
		add_message($message, $msg_types['info']);
	}
	
	function add_alert_message($message)
	{
		Global $msg_types;
		
		add_message($message, $msg_types['alert']);
	}
	
	function has_messages($message_type = null)
	{
		Global $msg_types;
		
		if(is_null($message_type))
		{
			$message_type = $msg_types['all'];
		}
		
		$messages_found = 0;
		/*
		foreach($_SESSION[MSG] as $this_message)
		{
			
		}
		*/
		
		$messages_found = count($_SESSION[MSG]);
		return $messages_found;
	}
	
	function clear_messages()
	{
		Global $msg_types;
		
		$_SESSION[MSG] = array();
	}
	
	function show_messages()
	{	
		Global $msg_types;
		
		$out_string = '';
			
		if(count($_SESSION[MSG]) > 0)
		{
			$flipped_array = array_flip($msg_types);
			foreach($_SESSION[MSG] as $this_message)
			{
				$out_string .= '<div class="'.$flipped_array[$this_message['type']].'_message">'.$this_message['message'].'</div>'; 
			}
		}
		
		return $out_string;
	}
?>