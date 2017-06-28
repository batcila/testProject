<?php
	define('SHOW_DEBUG_INFO', true, true);
	
	function generate_pass($length = 6)
	{
		$chars_allowed = '0123456789!@#$%^&*()ABCDEF';
		$pass = '';
		
		for($i = 0; $i < $length; $i++)
		{
			$pass .= substr($chars_allowed, rand(0, strlen($chars_allowed) - 1), 1);
		}
		
		return $pass;
	}
	
	function define_once($name, $value)
	{
		define($name, $value, true);
	}
	
	function Pre_Print($array_to_print, $print_for_all = false)
	{
		Global $user_id;

		//Configure it here if it is required
		$show_more_info = true;

		if($show_more_info)
		{
			$debug_array = debug_backtrace();

			$more_info = array();
			$more_info[] = 'Line:'.$debug_array[0]['line'];

			if(is_array($array_to_print))
			{
				$more_info[] = 'Count:'.count($array_to_print);
			}

			$more_info[] = 'File:'.$debug_array[0]['file'];
			$more_info = '['.implode(', ', $more_info).'] |> '.PHP_EOL;
		}
		else
		{
			$more_info = '';
		}

		if(is_array($array_to_print))
		{
			$var_info = print_r($array_to_print, true);
		}
		else
		{
			ob_start();
			var_dump($array_to_print);

			$var_info = ob_get_clean();
		}

		if(true)
		{
			if(!$print_for_all)
			{
				echo '<pre>'.PHP_EOL.$more_info.$var_info.'</pre>'.PHP_EOL;
			}
			else
			{
				echo '<pre>'.PHP_EOL.$more_info.$var_info.'</pre>'.PHP_EOL;
			}
		}
	}

	function Anti_Abs($value)
	{
		return -abs($value);
	}

	function getBaseURI()
	{
		/*
		$query_string_pos = mb_strpos($_SERVER['REQUEST_URI'], '?');
		$referer = 'http://'.$_SERVER['SERVER_NAME'].mb_substr($_SERVER['REQUEST_URI'], 0, ($query_string_pos ? $query_string_pos : null));

		//Model_HelperFunctions::Pre_Print($referer, true);
		return $referer;
		*/
	}

	function to_assoc_array($query_result, $use_key = null)
	{
		$tmp_array = array();
		while($row = mysqli_fetch_assoc($query_result))
		{
			if(!is_null($use_key) && is_string($use_key) && isset($row[$use_key]))
			{
				$tmp_array[ $row[$use_key] ] = $row;
			}
			else
			{
				$tmp_array[] = $row;
			}
		}

		return $tmp_array;
	}

	function is_admin_user($look_for_user = null)
	{
		// To Do  ...
	}

	function transliterate($textcyr = null, $textlat = null)
	{
		$cyr = array(
			'ж',  'ч',  'щ',   'ш',  'ю',  'а', 'б', 'в', 'г', 'д', 'е', 'з', 'и', 'й', 'к', 'л', 'м', 'н', 'о', 'п', 'р', 'с', 'т', 'у', 'ф', 'х', 'ц', 'ъ', 'ь', 'я',
			'Ж',  'Ч',  'Щ',   'Ш',  'Ю',  'А', 'Б', 'В', 'Г', 'Д', 'Е', 'З', 'И', 'Й', 'К', 'Л', 'М', 'Н', 'О', 'П', 'Р', 'С', 'Т', 'У', 'Ф', 'Х', 'Ц', 'Ъ', 'Ь', 'Я'
		);

		$lat = array(
			'zh', 'ch', 'sht', 'sh', 'yu', 'a', 'b', 'v', 'g', 'd', 'e', 'z', 'i', 'y', 'k', 'l', 'm', 'n', 'o', 'p', 'r', 's', 't', 'u', 'f', 'h', 'c', 'a', 'y', 'ya',
			'Zh', 'Ch', 'Sht', 'Sh', 'Yu', 'A', 'B', 'V', 'G', 'D', 'E', 'Z', 'I', 'Y', 'K', 'L', 'M', 'N', 'O', 'P', 'R', 'S', 'T', 'U', 'F', 'H', 'c', 'A', 'Y', 'Ya'
		);

		if($textcyr) return str_replace($cyr, $lat, $textcyr);
		else if($textlat) return str_replace($lat, $cyr, $textlat);
		else return null;
	}

	function ReturnDropDown($array, $default_value = null)
	{
		// To Do ...
	}

	function RenderDropDown($array, $default_value = null)
	{
		// To Do ...
	}

	function excerpt_text($text, $max_chars_on_row, $return_both = false)
	{
		// To Do ...
	}

	function excerpt_details_summary($text, $max_chars_on_row, $after_details = '', $details_attr = '')
	{
		// To Do ...
	}

	if(!function_exists('array_column'))
	{
		function array_column($array, $column_key, $index_key = null)
		{
			$tmp_array = array();
			foreach($array as $k => $v)
			{
			   $tmp_array[( !is_null($index_key) && isset($v[ $index_key ]) ? $v[ $index_key ] : $k )] = $v[ $column_key ];
			}

			return $tmp_array;
		}
	}

	function encrypt($pure_string, $encryption_key = CRYPT_PASS)
	{
		$iv = mcrypt_create_iv(
			mcrypt_get_iv_size(CRYPT_ALGO, CRYPT_MODE),
			MCRYPT_DEV_URANDOM
		);

		$encrypted = base64_encode(
			$iv .
			mcrypt_encrypt(
				CRYPT_ALGO,
				hash('sha256', $encryption_key, true),
				$pure_string,
				CRYPT_MODE,
				$iv
			)
		);

		return $encrypted;
	}

	/**
	 * Returns decrypted original string
	 */
	function decrypt($encrypted_string, $encryption_key = CRYPT_PASS) {
		$data = base64_decode($encrypted_string);
		$iv = substr($data, 0, mcrypt_get_iv_size(CRYPT_ALGO, CRYPT_MODE));

		$decrypted = rtrim(
			mcrypt_decrypt(
				CRYPT_ALGO,
				hash('sha256', $encryption_key, true),
				substr($data, mcrypt_get_iv_size(CRYPT_ALGO, CRYPT_MODE)),
				CRYPT_MODE,
				$iv
			),
			"\0"
		);

		return $decrypted;
	}

	function get_micro_time()
    {
        $time = microtime();
        $time = explode(' ', $time);
        $time = $time[1] + $time[0];

        return $time;
    }

	

	function my_phone_format($phone_string, $phone_format = null)
	{
		// To Do ...
	}

	
	function my_date_format($date_timestamp, $date_format = null, $zero_text = 'липсва')
	{
		// To Do ...
	}

?>