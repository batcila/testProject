<?php
	function return_table($original_data, $columns = null, $table_attr = '', $use_in = '')
	{
		Global $is_admin_user;
		//var_dump($is_admin_user); exit;

		if(true)
		{
			      $tmp = getPaginator($original_data);
			$rows_data = $tmp['resource'];
			$paginator = $tmp['paginator'];
			     $from = $tmp['start_item'];
			       $to = $tmp['end_item'];

			unset($tmp);
		}
		else
		{
			$rows_data = $original_data;
		}

		//Set default value for $table_attr
		if($table_attr == '')
		{
			$table_attr = 'border="1"';
		}

		$table_string = '';
		$table_string .= '<table'.($table_attr != '' ? ' '.$table_attr : '').'>'; // class="table table-striped"
		if(!is_null($columns))
		{
			if(!is_array($columns))
			{
				trigger_error('Columns must be array!', E_USER_NOTICE);
			}
			else
			{
				$order_map = array();

				$table_string .= '<thead>';
				$table_string .= '<tr>';
				foreach($columns as $column)
				{
					if(is_array($column))
					{
						// Column may be array with keys: style, text, field ...
						if(isset($column['style']) && $column['style'] != '')
						{
							$column_style = $column['style'];
						}
						else
						{
							//Set Default TH style
							$column_style = ' class="bgo_archive_th"';
						}

						if(isset($column['text']) && $column['text'] != '')
						{
							$column_data = $column['text'];
						}
						else
						{
							$column_data = '&nbsp;';
						}

						if(isset($column['field']) && $column['field'] != '')
						{
							$column_field = $column['field'];
							$column_data = '<a href="?'.($_SERVER['QUERY_STRING'] != '' ? remove_url_element(ORDER_BY, $_SERVER['QUERY_STRING']).'&' : '').ORDER_BY.'='.$column_field.'">'.$column_data.'</a>';
						}

						if(isset($column['key']) && $column['key'] != '')
						{
							$order_map[] = $column['key'];
						}
						else
						{
							if(isset($column['field']) && $column['field'] != '')
							{
								$order_map[] = $column['field'];
							}
							else
							{
								$order_map[] = '';
							}
						}
					}
					else
					{
						$column_style = ' class="bgo_archive_th"';
						$column_data = $column;
						$order_map[] = '';
					}

					$table_string .= '<th'.($column_style != '' ? ' '.$column_style : '').'>'.$column_data.'</th>';
				}
				$table_string .= '</tr>';
				$table_string .= '</thead>';

				foreach($order_map as $k => $v)
				{
					if($v == '')
					{
						unset($order_map[$k]);
					}

					$dot_pos = strpos($v, '.');
					if($dot_pos == true)
					{
						$v = explode('.', $v);
						$order_map[$k] = end($v);
					}
				}

				//Pre_Print($order_map);
			}
		}

		if(!is_array($rows_data))
		{
			trigger_error('Rows_Data must be array!', E_USER_NOTICE);
		}
		else
		{
			if(!is_null($columns) && is_array($columns) && count($columns))
			{
				$colspan = ' colspan="'.count($columns).'"';
			}
			else
			{
				$colspan = '';
			}

			$table_string .= '<tbody>';
			if(count($rows_data) == 0)
			{
				$table_string .= '<tr><th'.$colspan.' class="no_records_found">Няма записи!</th></tr>';
			}
			else
			{

				foreach($rows_data as $row)
				{
					$table_string .= '<tr>';
					if(!is_array($row))
					{
						$table_string .= '<td>'.$row.'</td>';
					}
					else
					{
						$column_index = 0;
						$columns_count = count($row);

						foreach($row as $row_column)
						{
							$td_style = '';

							if($use_in == 'mycourses')
							{
								$td_style = ' style="vertical-align:middle !important; padding-top:30px !important;; padding-bottom:30px !important;"';
								if($column_index >= $columns_count - 2)
								{
									$td_style .= ' class="hidden-xs hidden-sm"';
								}
							}
							else

							if($use_in == 'homeworks')
							{
								$td_style = ' style="vertical-align:middle !important; padding-top:45px !important; padding-bottom:45px !important;"';
							}

							if($use_in == 'question_edit')
							{
								$td_style = ' style="text-align:center !important;"';
							}

							if(isset($order_map[$column_index]))
							{
								$table_string .= '<td'.$td_style.'>'.$row[$order_map[$column_index]].'</td>';
							}
							else
							{
								$table_string .= '<td'.$td_style.'>'.$row_column.'</td>';
							}

							$column_index++;
						}
					}
					$table_string .= '</tr>';
				}
			}

			$table_string .= '</tbody>';

			if($colspan != '' && $is_admin_user)
			{
				//$table_string .= '<tfoot><tr><td'.$colspan.'>'.($is_admin_user || is_assistant() ? 'Общ брой: '.count($rows_data) : '&nbsp;').'</td></tr></tfoot>';
				$table_string .= '<tfoot><tr><td'.$colspan.'>'.($is_admin_user || is_assistant() ? '<span class="items_count">Общ брой: '.count($original_data).(count($original_data) != count($rows_data) ? ' | Записи: '.($from + 1).' - '.$to.'' : '').'</span>' : '&nbsp;').$paginator.'</td></tr></tfoot>';
				//$table_string .= '<tfoot><tr><td'.$colspan.'>'.(!$is_admin_user || count($rows_data) == 0  ? '&nbsp;' : 'Общ брой: '.count($rows_data)).'</td></tr></tfoot>';
			}

		}

		$table_string .= '</table>';

		return $table_string;
	}

	function render_table($rows_data, $columns = null, $table_attr = '', $use_in = '')
	{
		echo return_table($rows_data, $columns, $table_attr, $use_in);
	}
?>