<?php

	$_connection = null;

	function database_open($database = DB_NAME, $host = DB_HOST, $user = DB_USER, $pass = DB_PASS)
	{
		Global $_connection;

		$_connection = mysqli_connect($host , $user, $pass, $database)or die('Application can\'t access to DataBase Server! <br /> Connect Error (' . mysqli_connect_errno() . ') '.mysqli_connect_error());
		mysqli_query($_connection, 'set names utf8');
	}

	function database_close()
	{
		Global $_connection;

		if(mysqli_close($_connection))
		{
			$_connection = null;
		}
	}

	function SQL_DEBUG( $query )
	{
		Global $_connection;

			if( $query == '' ) return 0;

			global $SQL_INT;
			if( !isset($SQL_INT) ) $SQL_INT = 0;

			$query = htmlspecialchars($query);

			//[dv] this has to come first or you will have goofy results later.
			$query = preg_replace("/['\"]([^'\"]*)['\"]/i", "'<span style='color:#FF6600;'>$1</span>'", $query, -1);

			$query = str_ireplace(
						array (
							'*',
							'SELECT ',
							'UPDATE ',
							'DELETE ',
							'INSERT ',
							' INTO ',
							' VALUES ',
							' FROM ',
							' LEFT JOIN ',
							' RIGHT JOIN ',
							' WHERE ',
							' LIMIT ',
							' ORDER BY ',
							' AND ',
							' OR ', //[dv] note the space. otherwise you match to 'COLOR' ;-)
							' DESC ',
							' ASC ',
							' ON ',
							' AS '
						),
						array (
							'<span style="color:#FF6600; font-weight:bold;">*</span>',
							'<span style="color:#00AA00; font-weight:bold;">SELECT</span> ',
							'<span style="color:#00AA00; font-weight:bold;">UPDATE</span> ',
							'<span style="color:#00AA00; font-weight:bold;">DELETE</span> ',
							'<span style="color:#00AA00; font-weight:bold;">INSERT</span> ',
							' <span style="color:#00AA00; font-weight:bold;">INTO</span> ',
							' <span style="color:#00AA00; font-weight:bold;">VALUES</span> ',
							' <span style="color:#00AA00; font-weight:bold;">FROM</span> ',
							'<br /><span style="color:#00CC00; font-weight:bold;">LEFT JOIN</span> ',
							'<br /><span style="color:#00CC00; font-weight:bold;">RIGHT JOIN</span> ',
							'<br /><span style="color:#00AA00; font-weight:bold;">WHERE</span> ',
							' <span style="color:#AA0000; font-weight:bold;">LIMIT</span> ',
							' <span style="color:#00AA00; font-weight:bold;">ORDER BY</span> ',
							' <span style="color:#0000AA; font-weight:bold;">AND</span> ',
							' <span style="color:#0000AA; font-weight:bold;">OR</span> ',
							' <span style="color:#0000AA; font-weight:bold;">DESC</span> ',
							' <span style="color:#0000AA; font-weight:bold;">ASC</span> ',
							' <span style="color:#00DD00; font-weight:bold;">ON</span> ',
							' <span style="color:#00DD00; font-weight:bold;">AS</span> '
						),
						$query
					);

			echo '<span style="color:#0000FF;"><!-- <b>SQL['.$SQL_INT.']:</b> -->'.PHP_EOL.$query.'<span style="color:#FF0000;">;</span></span><br />'.PHP_EOL;
			echo mysqli_error($_connection);
			$SQL_INT++;

	} //SQL_DEBUG

	function show_database_error($query_string)
	{
		Global $_connection;

		if(DISPLAY_DB_ERRORS)
		{
			echo SQL_DEBUG($query_string);
		}
		else
		{
			// Make Log Functionality here...
		}

		if(DIE_ON_DATABASE_ERROR)
		{
			die();
		}
	}

	function last_query()
	{
		return $_SESSION['LAST_QUERY'];
	}

	function exec_query($query_string)
	{
		Global $_connection;

		if(!is_null($_connection))
		{
			$_SESSION['LAST_QUERY'] = $query_string;
			$sql_result = mysqli_query($_connection, $query_string);

			if(!$sql_result)
			{
				show_database_error($query_string);
			}

			return $sql_result;
		}
		else
		{
			show_database_error('You must open database connection first!');
		}
	}

	/*
	Тази функция може да работи с 2 формата:
	- 2 параметъра - първия е име на таблицата втория е array($field_name => $field_value)
	- 3 параметъра - първия е име на таблицата, втория е array($field_name1, $field_name2...), третият е array($field_value1, $field value2...)
	*/
	function insert_query($table, $data_array, $data_values = null)
	{
		Global $_connection;

		if(is_null($data_values))
		{
			$data_keys = array();
			$data_values = array();

			foreach($data_array as $key => $value)
			{
				$data_keys[] = '`'.$key.'`';
				$data_values[] = '"'.mysqli_real_escape_string($_connection, $value).'"';
			}
		}
		else
		{
			$data_keys = $data_array;

			foreach($data_keys as $k => $v)
			{
				$data_keys[$k] = '`'.$v.'`';
			}

			foreach($data_values as $k => $v)
			{
				$data_values[$k] = '"'.mysqli_real_escape_string($_connection, $v).'"';
			}
		}

		$QText = 'INSERT INTO '.$table.'('.implode(',',$data_keys).') VALUES('.implode(',',$data_values).')';

		//echo $QText; exit;

		exec_query($QText);
		
		return mysqli_insert_id($_connection);
	}

	function update_query($table, $columns, $where)
	{
		Global $_connection;

		if(is_array($columns))
		{
			foreach($columns as $k => $v)
			{
				$columns[$k] = '`'.$k.'` = "'.mysqli_real_escape_string($_connection, $v).'"';
			}

			$columns = implode(', ', $columns);
		}

		if(is_array($where))
		{
			foreach($where as $k => $v)
			{
				$where[$k] = set_where($k, '=', $v);
			}

			$where = implode(' AND ', $where);
		}

		// if it is string we use it, witout any changes
		if($where != ''){ $where = ' WHERE '.$where; }

		$QText = 'UPDATE '.$table.' SET '.$columns.$where;
		return exec_query($QText);
	}

	function delete_query($table, $where, $limit = 1)
	{
		return exec_query('DELETE FROM '.$table.' WHERE '.$where.($limit > 0 ? ' LIMIT '.$limit : ''));
	}

	function set_where($field, $operator, $value)
	{
		Global $_connection;
		return '`'.$field.'` '.$operator.' '.mysqli_real_escape_string($_connection, $value);
	}

	function select_query($table, $where = null, $columns = null)
	{
		if(is_null($columns))
		{
			$columns = '*';
		}
		else
		{
			if(is_array($columns))
			{
				$columns = implode(', ', $columns);
			}
			else
			{
				if(!is_string($columns))
				{
					$columns = '*';
					trigger_error('$Columns must be string, or array on line '.__LINE__, E_USER_NOTICE);
				}
			}
		}

		if(!is_null($where))
		{
			if(is_array($where))
			{
				foreach($where as $k => $v)
				{
					$where[$k] = set_where($k, '=', $v);
				}

				$where = implode(' AND ', $where);
			}

			// if it is string we use it, witout any changes
			if($where != ''){ $where = ' WHERE '.$where; }
		}
		else
		{
			$where = '';
		}

		$QText = 'SELECT '.$columns.' FROM '.$table.$where;
		//echo $QText; exit;

		return exec_query($QText);
	}

	// Backup the table and save it to a sql file
	function backup_tables($tables)
	{
		Global $_connection;
		$return = "";

		// Set the suffix of the backup filename
		if ($tables == '*') {
			$extname = 'all';
		}else{
			$extname = str_replace(",", "_", $tables);
			$extname = str_replace(" ", "_", $extname);
		}

		// Generate the filename for the backup file
		$filess = LOCAL_PATH.'backup/backup_' . $extname . '_' . date("Y-m-d_H-i-s") ;

		// Get all of the tables
		if($tables == '*')
		{
			$tables = array();
			$result = mysqli_query($_connection, 'SHOW TABLES');
			while($row = mysqli_fetch_row($result))
			{
				$tables[] = $row[0];
			}
		}
		else
		{
			if(!is_array($tables))
			{
				$tables = explode(',', $tables);
			}
		}

		// Cycle through each provided table
		foreach($tables as $table)
		{
			$result = mysqli_query($_connection, 'SELECT * FROM '.$table);
			$num_fields = mysqli_num_fields($result);

			// First part of the output - remove the table
			$return .= 'DROP TABLE ' . $table . ';<|||||||>';

			// Second part of the output - create table
			$row2 = mysqli_fetch_row(mysqli_query($_connection, 'SHOW CREATE TABLE '.$table));
			$return .= "\n\n" . $row2[1] . ";<|||||||>\n\n";

			// Third part of the output - insert values into new table
			for ($i = 0; $i < $num_fields; $i++)
			{
				while($row = mysqli_fetch_row($result))
				{
					$return.= 'INSERT INTO '.$table.' VALUES(';
					for($j=0; $j<$num_fields; $j++)
					{
						$row[$j] = addslashes($row[$j]);
						$row[$j] = ereg_replace("\n","\\n",$row[$j]);
						if(isset($row[$j]))
						{
							$return .= '"' . $row[$j] . '"';
						}
						else
						{
							$return .= '""';
						}

						if ($j<($num_fields-1))
						{
							$return.= ',';
						}
					}
					$return.= ");<|||||||>\n";
				}
			}
			$return.="\n\n\n";
		}

		// Save the sql file
		$handle = fopen($filess.'.sql','w+');
		fwrite($handle,$return);
		fclose($handle);

		//return '<a href="'.$filess.'.sql">here</a>';
	}

	function like_trans($columns, $cyr_text)
	{
		Global $_connection;
		$tmp = array();


		// Разцепваме по ',' за изрази като "Петър Иванов, Пет, Ива", т.е. Ако търсим %Петър Иванов%, или всички с %Пет%, или всички с %Ива%
		$cyr_text = explode(',', $cyr_text);
		foreach($cyr_text as $cyr_word)
		{
			$cyr_word = trim($cyr_word);

			if(strpos($cyr_word, ' ') === false) // Ако в израза няма празен интервал търсим във всяко поле за тази дума
			{
				// Third Way
				$fields = $columns;
			}
			else // Ако в израза има празен интервал следпаме всички полета и търсим за целия израз
			{
				$fields = array('CONCAT('.implode(', " ", ', $columns).')');
			}

			foreach($fields as $field)
			{
				$tmp[] = $field.' LIKE "%'.mysqli_real_escape_string($_connection, $cyr_word).'%"';
				$tmp[] = $field.' LIKE "%'.mysqli_real_escape_string($_connection, transliterate($cyr_word)).'%"';
			}
		}

		return '('.implode(' OR ', $tmp).')';

/*
		// First Way
		foreach($columns as $field)
		{
			$tmp[] = $field.' LIKE "%'.mysqli_real_escape_string($_connection, $cyr_text).'%"';
			$tmp[] = $field.' LIKE "%'.mysqli_real_escape_string($_connection, transliterate($cyr_text)).'%"';
		}

		return '('.implode(' OR ', $tmp).')';

		// Second Way
		$field = 'CONCAT('.implode(', " ", ', $columns).')';

		$tmp[] = $field.' LIKE "%'.mysqli_real_escape_string($_connection, $cyr_text).'%"';
		$tmp[] = $field.' LIKE "%'.mysqli_real_escape_string($_connection, transliterate($cyr_text)).'%"';

		return '('.implode(' OR ', $tmp).')';
 */
	}
?>