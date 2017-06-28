<?php
	//Global $_MESSAGE_CONSTANT_MASK, $_ClassName, $_MESSAGE_TYPE_CONSTANTS;

	define_once('_APP_', 'YBE_SYSTEM');
	define_once('_MESSAGE_CONSTANT_MASK', '_MESSAGE_TYPE_');
	define_once('_CLASS_MESSENGER', 'MESSAGES');

	function construct_messages()
	{
		$_ALL = 0; // Това е важно! Не променяй!

		$_MESSAGE_TYPE_CONSTANTS = array(
			// Ако се нуждаете от нови константи напишете ги тук по същия начин
            'INFO',   // - 1
            'WARNING',  // - 2
            'ERROR',  // - 4
            'SUCCESS' // - 8
		);

		if(!isset($_SESSION[_APP_][_CLASS_MESSENGER]) || !is_array($_SESSION[_APP_][_CLASS_MESSENGER]))
		{
			$_SESSION[_APP_][_CLASS_MESSENGER] = array();
		}

		if(!defined(_MESSAGE_CONSTANT_MASK.'ALL'))
		{
			foreach($_MESSAGE_TYPE_CONSTANTS as $key => $ConstantName)
			{
				$ConstantName = _MESSAGE_CONSTANT_MASK.strtoupper($ConstantName);
				$unique_value = (int) pow(2, $key);
				if(!defined($ConstantName))
				{
					define_once($ConstantName, $unique_value);
				}
				$_ALL =  $_ALL | $unique_value;
			}

			define_once(_MESSAGE_CONSTANT_MASK.'ALL', $_ALL);
		}
	}

	function add_message($message, $status_of_message)
	{
		$_SESSION[_APP_][_CLASS_MESSENGER][] = array('message' => $message, 'status' => $status_of_message);
	}

	function add_error_message($message)
	{
		add_message($message, constant(_MESSAGE_CONSTANT_MASK.'ERROR'));
	}

	function add_success_message($message)
	{
		add_message($message, constant(_MESSAGE_CONSTANT_MASK.'SUCCESS'));
	}

	function add_info_message($message)
	{
		add_message($message, constant(_MESSAGE_CONSTANT_MASK.'INFO'));
	}

	function add_warning_message($message)
	{
		add_message($message, constant(_MESSAGE_CONSTANT_MASK.'WARNING'));
	}

	function add_alert_message($message)
	{
		add_warning_message($message);
	}

	function have_messages($type_of_message = null)
	{
		if(is_null($type_of_message))
		{
			$type_of_message = constant(_MESSAGE_CONSTANT_MASK.'ALL');
		}

		$flag = false;
		foreach($_SESSION[_APP_][_CLASS_MESSENGER] as $message)
		{
			if($message['status'] & $type_of_message)
			{
				$flag = true;
				break;
			}
		}

		return $flag;
	}

	function has_error_messages()
	{
		return have_messages(constant(_MESSAGE_CONSTANT_MASK.'ERROR'));
	}

	function has_warning_messages()
	{
		return have_messages(constant(_MESSAGE_CONSTANT_MASK.'WARNING'));
	}

	function show_all_messages($type_of_message = null)
	{
		$CSS_CLASSES = array(
			_MESSAGE_TYPE_ERROR => 'info',
			_MESSAGE_TYPE_SUCCESS => 'success',
			_MESSAGE_TYPE_WARNING => 'warning'
		);

		if(is_null($type_of_message))
		{
			$type_of_message = constant(_MESSAGE_CONSTANT_MASK.'ALL');
		}

		foreach($_SESSION[_APP_][_CLASS_MESSENGER] as $message)
		{
			if($message['status'] & $type_of_message)
			{
				echo '<div class="'.$CSS_CLASSES[$message['status']].'_message" style="margin-top:20px;">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true" style="float:right; border-radius:50%;">×</button>
                    <strong>'.$message['message'].'</strong>
                </div>';

				//<i class="fa fa-check"></i>
			}
		}
	}

	function clear_all_messages($type_of_message = null)
	{
		if(is_null($type_of_message))
		{
			$type_of_message = constant(_MESSAGE_CONSTANT_MASK.'ALL');
		}

		foreach($_SESSION[_APP_][_CLASS_MESSENGER] as $key => $message)
		{
			if($message['status'] & $type_of_message)
			{
				$_SESSION[_APP_][_CLASS_MESSENGER][$key] = false;
				unset($_SESSION[_APP_][_CLASS_MESSENGER][$key]);
			}
		}

		if(count($_SESSION[_APP_][_CLASS_MESSENGER]) == 0)
		{
			unset($_SESSION[_APP_][_CLASS_MESSENGER]);
		}
	}
?>