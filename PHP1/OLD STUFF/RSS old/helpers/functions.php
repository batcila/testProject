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

	function to_dropdown_array($query_result, $use_key = null, $use_text = null)
	{
		$tmp_array = array();
		while($row = mysqli_fetch_assoc($query_result))
		{
			if(!is_null($use_key) && is_string($use_key) && isset($row[$use_key]))
			{
				$tmp_array[ $row[$use_key] ] = (!is_null($use_text) && is_string($use_text) && isset($row[$use_text]) ? $row[$use_text] : $row);
			}
			else
			{
				$tmp_array[] = (!is_null($use_text) && is_string($use_text) && isset($row[$use_text]) ? $row[$use_text] : $row);
			}
		}

		return $tmp_array;
	}

	function showFieldValue($loaded_variable, $default_value = '')
    { 	//Maybe we will need from "&& $loaded_variable" in conditions list
    	return (isset($loaded_variable) ? $loaded_variable : $default_value) ;
    }

	function getAllQuestionsForExam($test_id)
	{
		return get_all_questions('is_active = 1 AND test_questions.test_id = '.(int)$test_id); // .' LIMIT 5'
	}

	function getAnswersPerQuestion($question_id, $answer_type = null, $order_by_field = null)
	{
		$QText = 'SELECT * FROM test_question_answers WHERE question_id = '.(int) $question_id;

		if(!is_null($answer_type))
		{
			if(is_numeric($answer_type))
			{
				$QText .= ' AND answer_type = '.$answer_type;
			}
			else
			{
				trigger_error('Function "getAnswersPerQuestion" expect, second parameter "$answer_type" to be integer!', E_USER_NOTICE);
			}
		}

		if(!is_null($order_by_field))
		{
			/*
			if(is_array($order_by_field))
			{
				foreach($order_by_field as $key => $value)
				{
					$answers_array = $answers_array->order_by($key, $value);
				}
			}
			elseif (is_string($order_by_field))
			*/
			if (is_string($order_by_field))
			{
				$QText .= ' ORDER BY '.$order_by_field.' ASC';
			}
			else
			{
				trigger_error('Wrong parameter type for $order_by_field. We expecting $order_by_field to be array or string!', E_USER_NOTICE);
			}
		}

		$answers_array = to_assoc_array(exec_query($QText), 'answer_id');
		//Pre_Print($answers_array,true);
		return $answers_array;
	}

	/*
     * This function Randomize questions order & answers order. by this way will be hard 2 student to have identical exams, even if they have identical questions
	 * $Exam_Questions must be array. For example: $exam_questions = Model_Exam::getExamQuestions($exam_id);
     */

    function shuffle_exam_components($exam_questions, $shuffle_sub_item = null)
    {
        $array_keys = array_keys($exam_questions);
        shuffle($array_keys);

        $array_shiffle = array();
        foreach($array_keys as $question_id)
        {
            if( !is_null($shuffle_sub_item) && isset($exam_questions[$question_id][$shuffle_sub_item]) )
            {
                //echo $question_id;Model_HelperFunctions::Pre_Print($exam_questions, true);
                $exam_questions[$question_id][$shuffle_sub_item] = shuffle_exam_components($exam_questions[$question_id][$shuffle_sub_item]);
            }

            $array_shiffle[$question_id] = $exam_questions[$question_id];
        }

        //Pre_Print($array_shiffle, true);
        return $array_shiffle;
    }

	function get_random_questions($count_of_questions, $test_id)
	{
		$random_questions = array();

        $course_exam_questions = getAllQuestionsForExam($test_id);
		//Pre_Print($course_exam_questions, true);

		if(is_array($count_of_questions))
		{
			$grouped_exam_questions = array();

			// Създаваме $grouped_exam)questions, т.е. преобразуваме масива от линеен в йерархичен
			foreach($course_exam_questions as $question)
			{
				$grouped_exam_questions[$question['question_group']][] = $question;
			}

			//Pre_Print($grouped_exam_questions, true);

			foreach($count_of_questions as $group => $count_for_group)
			{
				for($iterator = 0; $iterator < $count_for_group; $iterator++)
				{
					if(!isset($grouped_exam_questions[$group]) || count($grouped_exam_questions[$group]) <= 0)
					{
						break;
					}

					$random_item_key = rand(0, count($grouped_exam_questions[$group])-1);

					$random_questions[] = $grouped_exam_questions[$group][$random_item_key];
					unset($grouped_exam_questions[$group][$random_item_key]);
					$grouped_exam_questions[$group] = array_values($grouped_exam_questions[$group]);
				}
			}
		}
		else
		{
			for($iterator = 0; $iterator < $count_of_questions; $iterator++)
			{
				if(count($course_exam_questions) <= 0)
				{
					break;
				}

				$random_item_key = rand(0, count($course_exam_questions)-1);

				$random_questions[] = $course_exam_questions[$random_item_key];
				unset($course_exam_questions[$random_item_key]);
				$course_exam_questions = array_values($course_exam_questions);
			}
		}

        //Pre_Print($random_questions, true);
        foreach($random_questions as $key => $value)
		{
            $random_questions[$key]['question_title'] = str_replace("'","`",$value['question_title']);
			$random_questions[$key]['answers'] = getAnswersPerQuestion($value['question_id'], $value['question_type']);
 		}
		//Pre_Print($random_questions, true);

        return $random_questions;
	}

	function return_correct_answers($question_answers)
	{
		$correct_answers = array();
		$max_points = 0;

		foreach($question_answers as $answer)
		{
			if($answer['is_correct'] == 1)
			{
				$correct_answers[$answer['answer_id']] = $answer['answer_title'];
				$max_points += $answer['answer_value'];
			}
		}

		return array('array' => $correct_answers, 'max_question_points' => $max_points);
	}

	function return_correct_answers_per_poll($question_answers)
	{
		$correct_answers = array();
		$max_points = 0;

		foreach($question_answers as $answer)
		{
			if(true)
			{
				$correct_answers[$answer['answer_id']] = $answer['answer_title'];
				$max_points += 6; //$answer['answer_value'];
			}
		}

		return array('array' => $correct_answers, 'max_question_points' => $max_points);
	}

	function Evaluate_Exam($exam_id, $input_data)
	{
		if(!isset($_SESSION['exam'][$exam_id]['questions']))
		{
			add_error_message('Missing Exam_Questions Session...');
			trigger_error('Missing Exam_Questions Session...');

			return false;
		}

		$_SESSION['exam'][$exam_id]['input_data'] = $input_data;
		$exam_questions = $_SESSION['exam'][$exam_id]['questions'];
		$groups_scores = array(
							0 => array('points' => 0, 'max_points' => 0),
							1 => array('points' => 0, 'max_points' => 0),
							2 => array('points' => 0, 'max_points' => 0),
							3 => array('points' => 0, 'max_points' => 0),
							4 => array('points' => 0, 'max_points' => 0),
							5 => array('points' => 0, 'max_points' => 0)
						);

		//Pre_Print($input_data);
		//Pre_Print($exam_questions);

		$total_points = 0;
		$exam_points = 0;
		$percent = 0; // Define this variable because it will throw notice, if it is not defined.

		//$percent_required = reset($exam_questions);
		//$percent_required = $percent_required['percent_required'];

		$percent_required = 0;

		foreach($exam_questions as $key => $question)
		{
			// Set default data or replace them
			$evaluation = 3; // Default value - If $question['question_type'] is strange or we have unexpected condition $evaluation will be equal to 3 (i.e. Unknown question type)

			$evaluated_data = array(
					0 => array( // Data for wrong answer
							'color' => 'btn-danger',
							//'message' => 'Incorrect' // This value will be rewritten from $question['feedback_error']
					),

					1 => array( // Data for correct answer
							'color' => 'btn-success',
							//'message' => 'Correct'	 // This value will be rewritten from $question['feedback_correct']
					),

					2 => array( // Data for multy choise question type when student is selected many correct and incorrect answers
							'color' => 'btn-danger', // btn-warning
							//'message' => 'Selecting all checkboxes is not good solution'
					),

					3 => array( // Data for unknown question type
							'color' => 'btn-default', // btn-info
							//'message' => 'This question will be manually evaluated'
					),
			);

			//$evaluated_data[0]['message']  = $question['feedback_error'];
			//$evaluated_data[1]['message']  = $question['feedback_correct'];
			//$evaluated_data[2]['message'] .= ' '.$question['feedback_error'];

			//Model_HelperFunctions::Pre_Print($question['answers']);
			//Model_HelperFunctions::Pre_Print($_POST, false);
			//Model_HelperFunctions::Pre_Print($question, true);

			$correct_answers = return_correct_answers($question['answers']);

			$total_points += $correct_answers['max_question_points'];
			$groups_scores[$question['question_group']]['max_points'] += $correct_answers['max_question_points'];

			if(true) // If we want to show correct  answers only for checked questions, use this boolean condition: if(isset($input_data['questions'][$question['question_id']]))
			{
				if($question['question_type'] == 1) // If we have "multi choise"
				{
					//Model_HelperFunctions::Pre_Print($question);
					//Model_HelperFunctions::Pre_Print($correct_answers['array'], true);

					$number_of_correct_answers = 0;

					if(count($correct_answers['array']) > 1)
					{
						foreach($question['answers'] as $k => $answer)
						{
							/*
							if($answer['is_correct'] == 1)
							{
								if(isset($input_data['questions'][$question['question_id']][$k]))
								{
									$exam_questions[$key]['answers'][$k]['is_checked'] = 1;
									$exam_points += $answer['answer_value'];
									$number_of_correct_answers++;
								}

								$exam_questions[$key]['answers'][$k]['evaluated_class'] = 'answer_success';
							}
							else
							{
								if(isset($input_data['questions'][$question['question_id']][$k]))
								{
									$exam_questions[$key]['answers'][$k]['is_checked'] = 1;
									$exam_questions[$key]['answers'][$k]['evaluated_class'] = 'answer_error';
								}

								// if you want to add color to every option (not only selected ) then you must uncomment this row
								//$exam_questions[$key]['answers'][$k]['evaluated_class'] = 'answer_error';
							}
							*/

							if(isset($input_data['questions'][$question['question_id']][$k]))
							{
								$exam_questions[$key]['answers'][$k]['is_checked'] = 1;

								if($answer['is_correct'] == 1)
								{
									$number_of_correct_answers++;
									$exam_points += $answer['answer_value'];
									$exam_questions[$key]['answers'][$k]['evaluated_class'] = 'answer_success';
								}
								else
								{
									$exam_points += Model_HelperFunctions::Anti_Abs($answer['answer_value']);
									$exam_questions[$key]['answers'][$k]['evaluated_class'] = 'answer_error';
								}
							}
						}

						//$exam_points -= count($input_data['questions'][$question['question_id']]) - $number_of_correct_answers;

						if($number_of_correct_answers == count($correct_answers['array']) && $number_of_correct_answers == count($input_data['questions'][$question['question_id']]))
						{
							$evaluation = 1;
						}
						else
						{
							$evaluation = 0;
						}
					}
					else
					{
						//Model_HelperFunctions::Pre_Print($input_data);
						//Model_HelperFunctions::Pre_Print($question, true);

						foreach($question['answers'] as $k => $answer)
						{
							if($answer['is_correct'] == 1)
							{
								$exam_questions[$key]['answers'][$k]['evaluated_class'] = 'answer_success';

								if(isset($input_data['questions'][$question['question_id']]) && $input_data['questions'][$question['question_id']] == $answer['id']) // If this option is selected from user
								{
									$evaluation = 1;
									$exam_points += $answer['answer_value']; //$correct_answers['max_question_points'];
									$exam_questions[$key]['answers'][$input_data['questions'][$question['question_id']]]['is_checked'] = 1;
								}
							}
							else
							{
								if(isset($input_data['questions'][$question['question_id']]) && $input_data['questions'][$question['question_id']] == $answer['id']) // If this option is selected from user
								{
									$evaluation = 0;
									$exam_points += Anti_Abs($answer['answer_value']); // If answer is incorrect and student must lose 1 point, you must use this: $exam_points -= 1;
									$exam_questions[$key]['answers'][$k]['evaluated_class'] = 'answer_error';

									$exam_questions[$key]['answers'][$input_data['questions'][$question['question_id']]]['is_checked'] = 1;
								}
							}
						}
					}
				}

				if($question['question_type'] == 2) // If we have "multi choise" or "True/False" question
				{
					foreach($question['answers'] as $k => $answer)
					{
						if($answer['is_correct'] == 1)
						{
							// Маркираме верния отговор
							$exam_questions[$key]['answers'][$k]['evaluated_class'] = 'answer_success';

							if(isset($input_data['questions'][$question['question_id']]) && $input_data['questions'][$question['question_id']] == $answer['answer_id']) // If this option is selected from user
							{
								$evaluation = 1;
								$exam_points += $correct_answers['max_question_points'];
								$groups_scores[$question['question_group']]['points'] += $correct_answers['max_question_points'];
								$exam_questions[$key]['answers'][$input_data['questions'][$question['question_id']]]['is_checked'] = 1;
							}
						}
						else
						{
							if(isset($input_data['questions'][$question['question_id']]) && $input_data['questions'][$question['question_id']] == $answer['answer_id']) // If this option is selected from user
							{
								$evaluation = 0;
								$exam_points += Anti_Abs($answer['answer_value']); // If answer is incorrect student lose 1 point use this:  $exam_points -= 1;
								$groups_scores[$question['question_group']]['points'] += Anti_Abs($answer['answer_value']);
								$exam_questions[$key]['answers'][$k]['evaluated_class'] = 'answer_error';

								$exam_questions[$key]['answers'][$input_data['questions'][$question['question_id']]]['is_checked'] = 1;
							}
						}
					}
				}

				if($question['question_type'] == 3) // If we have "fill in" question
				{
					$question['answers'] = reset($question['answers']);
					$question['answers']['is_checked'] = $input_data['questions'][$question['question_id']];

					if(trim($input_data['questions'][$question['question_id']]) != '')
					{
						if( strtolower(trim($question['answers']['answer'])) == strtolower(trim($input_data['questions'][$question['question_id']])) )
						{
							$evaluation = 1;
							$exam_points += $correct_answers['max_question_points'];
							$question['answers']['evaluated_class'] = 'answer_success';
						}
						else
						{
							$evaluation = 0;
							$exam_points += Anti_Abs($answer['answer_value']);  // If answer is incorrect student lose 1 point use this: $exam_points -= 1;
							$question['answers']['evaluated_class'] = 'answer_error';
						}
					}

					$exam_questions[$key]['answers'] = array($question['answers']['id'] => $question['answers']);
				}

				if($question['question_type'] == 4) // If we have textarea poll question
				{
					//$question['answers'] = reset($question['answers']);
					$question['answers']['is_checked'] = $input_data['questions'][$question['question_id']];

					if(trim($input_data['questions'][$question['question_id']]) != '')
					{
						if( strtolower(trim($question['answers']['answer'])) == strtolower(trim($input_data['questions'][$question['question_id']])) )
						{
							$evaluation = 1;
							//$exam_points += $correct_answers['max_question_points'];
							$question['answers']['evaluated_class'] = 'answer_success';
						}
						else
						{
							$evaluation = 0;
							//$exam_points += Anti_Abs($answer['answer_value']);  // If answer is incorrect student lose 1 point use this: $exam_points -= 1;
							$question['answers']['evaluated_class'] = 'answer_error';
						}
					}

					$exam_questions[$key]['answers'] = array($question['answers']['id'] => $question['answers']);
				}

				if($question['question_type'] == 5) // If we have "File Upload" field
				{

				}

				if($question['question_type'] >= 6) // If we have unknown question type
				{
					$evaluation = 3;
				}

				if(isset($evaluation))
				{
					$exam_questions[$key]['evaluated_data'] = $evaluated_data[$evaluation];
				}

				if($exam_points <= 0)
				{
					$percent = 0;
				}
				else
				{
					$percent = $exam_points / $total_points * 100;
					$percent = number_format($percent, 2);
				}
			}
		}

		return array('exam_questions' => $exam_questions, 'percent' => $percent, 'is_pass' => ($percent >= $percent_required ? 1 : 0), 'groups_scores' => $groups_scores);
	}

	function ReadTakenExam($exam_history_id)
    {
        $taken_exam_data = select_query('exams_history', set_where('history_id', '=', $exam_history_id));
		$taken_exam_data = to_assoc_array($taken_exam_data);

        return $taken_exam_data;
    }

	function getTestsPerCourse($course_id, $exam_type = null)
	{
		$required_tests = exec_query('SELECT rt.*, t.* FROM required_tests AS rt LEFT JOIN tests as t ON rt.test_id = t.test_id  WHERE rt.course_id = '. (int) $course_id.(!is_null($exam_type) ? ' AND rt.exam_type = "'.$exam_type.'"' : '') );
		return to_assoc_array($required_tests, 'exam_id');
	}

	function getTestsPerUser($course_id, $user_id = null, $exam_type = null)
	{
		$required_tests = exec_query('SELECT rt.*, t.*, eh.score, eh.is_pass, eh.history_id FROM required_tests AS rt LEFT JOIN tests as t ON rt.test_id = t.test_id LEFT JOIN exams_history AS eh ON rt.exam_id = eh.exam_id WHERE rt.course_id = '. (int) $course_id.' '.(!is_null($user_id) ? 'AND eh.user_id = '.(int) $user_id.' ' : '').(!is_null($exam_type) ? 'AND rt.exam_type = "'.$exam_type.'" ' : '').'GROUP BY eh.exam_id' );
		return to_assoc_array($required_tests, 'exam_id');
	}

	function getRequiredTestsPerCourse($course_id)
	{
		return getTestsPerCourse($course_id, 'precondition');
	}

	function getCurrentTestsPerCourse($course_id)
	{
		return getTestsPerCourse($course_id, 'current');
	}

	function getFinalTestsPerCourse($course_id)
	{
		return getTestsPerCourse($course_id, 'final');
	}

	function getRequiredTestsPerUser($course_id, $user_id = null) // Get Required test per Course Candidate
	{
		return getTestsPerUser($course_id, $user_id, 'precondition');
	}

	function getCurrentTestsPerUser($course_id, $user_id = null) // Get Required test per Course Candidate
	{
		return getTestsPerUser($course_id, $user_id, 'current');
	}

	function getExamData($exam_id)
	{
		$exam_data = exec_query('SELECT rt.*, t.test_name FROM required_tests AS rt LEFT JOIN tests AS t ON rt.test_id = t.test_id WHERE exam_id = '.(int) $exam_id);
		return to_assoc_array($exam_data);
	}

	function Exam_Exists($exam_id)
	{
		return (count($exam_id) == 1);
	}

	function Test_Exists($test_id)
	{
		$test = get_all_tests('test_id = '.(int) $test_id);
		return Only_One_Exists($test);
	}

	function Course_Exists($course_id)
	{
		$course = get_all_courses('course_id = '.(int) $course_id);
		return Only_One_Exists($course);
	}

	function Only_One_Exists($result_array)
	{
		return (count($result_array) == 1);
	}

	/*
	 * This function return history_id from table exams_history per exam_id and user_id.
	 * Its name is getTakenId, not getHistory (as probably you expect) because I want to be sure, that you make difference between this function and functions which work with History Data (defined into recovery module)
	 */
	function getTakenId($exam_id, $user_id)
	{
		$exam = select_query(
					'exams_history',
					array(
						'exam_id' => $exam_id,
						'user_id' => $user_id,
						'is_pass' => 1
					)
				);

		$exam = to_assoc_array($exam);

		if(Exam_Exists($exam))
		{
			$exam = reset($exam);

			return (int) $exam['history_id'];
		}
		else
		{
			return false;
		}
	}

	function is_already_taken($exam_id, $user_id)
	{
		return (bool) getTakenId($exam_id, $user_id); // (int) $exams['history_id']; // (bool) count($exams);
	}

	function get_all_questions($where = null)
	{
		$QGet = exec_query('SELECT * FROM test_questions LEFT JOIN tests ON test_questions.test_id = tests.test_id'.(!is_null($where) ? ' WHERE '.$where : ''));

		return to_assoc_array($QGet);
	}

	function get_history_exams($where = null)
	{
		$QGet = exec_query('SELECT * FROM exams_history'.(!is_null($where) ? ' WHERE '.$where : ''));

		return to_assoc_array($QGet);
	}

	function get_all_exams($where = null)
	{
		$QGet = exec_query('SELECT * FROM required_tests'.(!is_null($where) ? ' WHERE '.$where : ''));

		return to_assoc_array($QGet);
	}

	// Тази функция може да се използва както с масив така и с $_SERVER['QUERY_STRING'];
	function remove_url_element($element, $query_string = null)
	{
		$element .= '=';
		//echo $element.'<br>';
		if(is_null($query_string))
		{
			$query_string = $_SERVER['QUERY_STRING'];
		}

		if(is_string($query_string))
		{
			$query = explode('&', $query_string);
		}
		elseif(is_array($query_string))
		{
			$query = $query_string;
		}
		else
		{
			trigger_error('Wrong argument for Function Remove_URL_Element!');
		}

		foreach($query as $k => $v)
		{
			if(substr($v, 0, strlen($element)) == $element)
			{
				unset($query[$k]);
			}
			//else{ echo '<font color="red">'.$v.'</font><br>'; }
		}

		return implode('&', $query);
	}

	function remove_url_elements($elements_array, $query_string = null)
	{
		if(is_array($elements_array))
		{
			foreach($elements_array as $element)
			{
				$query_string = remove_url_element($element, $query_string);
				//echo $query_string.'<br>';
			}

			return $query_string;
		}
		else
		{
			trigger_error('Wrong First argument for function Remove_URL_ELements on line '.__LINE__);
			return false;
		}
	}

	function getQuestionData($question_id)
	{
		return get_all_questions('question_id = '.$question_id);
	}

	function get_all_answers($where)
	{
		$QGet = exec_query('SELECT * FROM test_question_answers '.(is_string($where) ? ' WHERE '.$where : ''));

		return to_assoc_array($QGet);
	}

	function getCourseMember($course_id, $user_id, $with_role = '')
	{
		$where = array(
						'course_id' => $course_id,
						'user_id' => $user_id,
				 );

		if($with_role != '')
		{
			$where['role'] = $with_role;
		}

		$record = select_query('course_members', $where);
		$record = to_assoc_array($record);

		return $record;
	}

	function getCourseMemberID($course_id, $user_id, $with_role = '')
	{
		$record = getCourseMember($course_id, $user_id, $with_role);

		if(Only_One_Exists($record))
		{
			$record = reset($record);

			return (int) $record['id'];
		}
		else
		{
			return false;
		}
	}

	function refreshUserScore($course_id, $user_id)
	{
		Global $is_admin_user;

		if(!$is_admin_user)
		{
			echo 'You are not authorized!';
			exit;
		}

		$new_final_score = 0;

		$course_required_exams = getRequiredTestsPerCourse($course_id);
		//Pre_Print($course_required_exams);

		foreach($course_required_exams as $required_exam)
		{
			$taken_id = getTakenId($required_exam['exam_id'], $user_id);

			if($taken_id)
			{
				$taken_exam_data = ReadTakenExam($taken_id);
				//Pre_Print($taken_exam_data);

				if(count($taken_exam_data) == 1)
				{
					$taken_exam_data = reset($taken_exam_data);

					$new_final_score += $taken_exam_data['score'] * $required_exam['weight'];
				}
			}
		}

		$member_id = getCourseMemberID($course_id, $user_id);
		$result = update_query('course_members', array('score' => $new_final_score), 'id = '.$member_id);

		return $result;
	}

	function getCourseCandidates($course_id, $order_by = '')
	{
		//$candidates = to_assoc_array(exec_query('SELECT cm.*, u.*, history.score FROM course_members AS cm LEFT JOIN users AS u ON cm.user_id = u.user_id INNER JOIN (SELECT SUM(score) score, COUNT(*) exams_count, user_id FROM exams_history eh WHERE eh.score > 0 GROUP BY eh.user_id HAVING COUNT(exams_count) = 3) AS history ON u.user_id=history.user_id WHERE cm.role = "student" AND cm.form_id = 1 AND course_id = '.(int) $course_id.($order_by != '' ? ' ORDER BY '.$order_by : '')));
		//$candidates = to_assoc_array(exec_query('SELECT cm.*, u.*, history.score FROM course_members AS cm LEFT JOIN users AS u ON cm.user_id = u.user_id INNER JOIN (SELECT SUM(score) score, COUNT(*) exams_count, user_id FROM exams_history eh WHERE eh.score > 0 GROUP BY eh.user_id HAVING COUNT(exams_count) = 3) AS history ON u.user_id=history.user_id WHERE cm.role = "student" AND cm.form_id = 2 AND course_id = '.(int) $course_id.($order_by != '' ? ' ORDER BY '.$order_by : '')));
		//$candidates = to_assoc_array(exec_query('SELECT cm.*, u.*, history.score FROM course_members AS cm LEFT JOIN users AS u ON cm.user_id = u.user_id INNER JOIN (SELECT SUM(score) score, COUNT(*) exams_count, user_id FROM exams_history eh WHERE eh.score > 0 AND eh.exam_id IN (11,12,13) GROUP BY eh.user_id HAVING COUNT(exams_count) = 3) AS history ON u.user_id=history.user_id WHERE cm.role = "candidate" AND course_id = '.(int) $course_id.($order_by != '' ? ' ORDER BY '.$order_by : '')));
		//$candidates = to_assoc_array(exec_query('SELECT cm.*, u.*, history.score FROM course_members AS cm LEFT JOIN users AS u ON cm.user_id = u.user_id INNER JOIN (SELECT SUM(score) score, COUNT(*) exams_count, user_id FROM exams_history eh WHERE eh.score > 0 AND eh.exam_id = 10 GROUP BY eh.user_id HAVING COUNT(exams_count) = 1) AS history ON u.user_id=history.user_id WHERE cm.role = "candidate" AND course_id = '.(int) $course_id.($order_by != '' ? ' ORDER BY '.$order_by : '')));
		//$candidates = to_assoc_array(exec_query('SELECT cm.*, u.*, history.score FROM course_members AS cm LEFT JOIN users AS u ON cm.user_id = u.user_id INNER JOIN (SELECT SUM(score) score, COUNT(*) exams_count, user_id FROM exams_history eh WHERE eh.score > 0 AND eh.exam_id = 10 GROUP BY eh.user_id HAVING COUNT(exams_count) = 1) AS history ON u.user_id=history.user_id WHERE cm.role = "student" AND form_id = 2 course_id = '.(int) $course_id.($order_by != '' ? ' ORDER BY '.$order_by : '')));

		$required_tests = getTestsPerCourse($course_id, 'precondition');

		$candidates = to_assoc_array(exec_query('SELECT cm.*, u.*, history.score FROM course_members AS cm LEFT JOIN users AS u ON cm.user_id = u.user_id INNER JOIN (SELECT SUM(score) score, COUNT(*) exams_count, user_id FROM exams_history eh WHERE eh.score > 0 AND eh.exam_id IN ('.implode(',', array_keys($required_tests)).') GROUP BY eh.user_id HAVING COUNT(exams_count) = '.count($required_tests).') AS history ON u.user_id=history.user_id WHERE cm.role = "candidate" AND cm.course_id = '.(int) $course_id.($order_by != '' ? ' ORDER BY '.$order_by : '')));

		//pre_print($candidates);
		return $candidates;
	}

	function get_user_courses($user_id, $optional_where = null)
	{
		$my_courses = to_assoc_array(exec_query('SELECT * FROM course_members AS cm LEFT JOIN courses AS c ON cm.course_id = c.course_id LEFT JOIN courses_ancestors AS ca ON c.descendant_of = ca.ancestor_id WHERE cm.user_id = '.$user_id.(!is_null($optional_where) ? ' AND '.$optional_where : '')));

		return $my_courses;

	}

	/* This Function help us to Convert stdClass Objects to Multidimensional Arrays */
    function objectToArray($d) {
		if (is_object($d)) {
			// Gets the properties of the given object
			// with get_object_vars function
			$d = get_object_vars($d);
		}

		if (is_array($d)) {
			/*
			* Return array converted to object
			* Using __FUNCTION__ (Magic constant)
			* for recursive call
			*/
			return array_map(__METHOD__, $d);
		}
		else {
			// Return array
			return $d;
		}
	}

	/* Check user have uploaded homeworks */
	function haveHomeworks($user_id, $homework_id) {
		if(!empty($user_id) AND !empty($homework_id)) {
			$result = to_assoc_array(exec_query('SELECT `user_id` FROM user_homeworks WHERE user_id=' . (int) $user_id . ' AND homework_id=' . (int) $homework_id));

			if(count($result) == 0) {
				return false;
			} else {
				return true;
			}
		}
	}

	function getHomeworksPerUser($user_id) {
		if(!empty($user_id)) {
			$result = to_assoc_array(exec_query('SELECT * FROM user_homeworks WHERE id IN (SELECT Max(id) FROM user_homeworks WHERE user_id=' . (int) $user_id . ' GROUP BY homework_id)') , 'homework_id');

			return $result;
		}
	}

	function is_assistant($look_for_user = null)
	{
		Global $is_admin_user, $user_id, $assistants_array;

		if(!isset($user_id) || !isset($is_admin_user))
		{
			return false;
		}
		else
		{
			if(is_null($look_for_user))
			{
				$look_for_user = $user_id;
			}

			return (in_array($look_for_user, $assistants_array)); //$is_admin_user ||
		}
	}

	function is_tester($look_for_user = null)
	{
		Global $is_admin_user, $user_id;

		if(!isset($user_id) || !isset($is_admin_user))
		{
			return false;
		}
		else
		{
			if(is_null($look_for_user))
			{
				$look_for_user = $user_id;
			}

			$testers_array = array(1, 15, 247);

			return (in_array($look_for_user, $testers_array));
		}
	}

	function is_admin_user($look_for_user = null)
	{
		Global $is_admin_user, $user_id, $admins_array;

		if(!isset($user_id) || !isset($is_admin_user))
		{
			return false;
		}
		else
		{
			if(is_null($look_for_user))
			{
				$look_for_user = $user_id;
			}

			return (in_array($look_for_user, $admins_array));
		}
	}

	function getOfficeAddr()
	{
		return array(
			//'78.130.225.87',
			//'78.130.188.236',
			//'78.130.165.238',
			//'78.130.149.46',
			//'78.130.192.6',
			//'78.130.187.154',
			//'78.130.174.163',
			//'78.130.162.104',
			//'78.130.216.110',
			//'78.130.172.203',
			//'78.130.192.66',
			//'78.130.199.170',
			//'78.130.188.163',
			//'78.130.164.72',

			'78.130.225.68', // Офис Пловдив, ул. Найчо Цанов №8
			'79.100.199.223' // Офис Стара Загора, бул. Цар Симеон Велики №168
		);
	}

	function is_office_ip($ip = null)
	{
		if(is_null($ip))
		{
			$ip = $_SERVER['REMOTE_ADDR'];
		}

		$office_ip = getOfficeAddr();
		return in_array($ip, $office_ip);
	}

	function have_permissions($user_id, $exam_id)
	{
		$print_line = false && is_assistant();

		$exam = to_assoc_array(exec_query('SELECT * FROM required_tests WHERE exam_id = '. (int) $exam_id));

		if(count($exam)) // Добре Имаме такъв изпит. Вземаме курса с който е свързан.
		{
			$exam = reset($exam);
			//pre_print($exam, true);
			$member = to_assoc_array(exec_query('SELECT * FROM course_members WHERE course_id = '.$exam['course_id'].' AND user_id = '. (int) $user_id));

			if(count($member))
			{
				$member = reset($member);
				//pre_print($member, true);
				$visibility = to_assoc_array(exec_query('SELECT * FROM objects_visibility WHERE object_type = "exam" AND object_id = '.(int) $exam_id.' AND group_number = '. (int) $member['group_number']));

				if(count($visibility))
				{
					$visibility = reset($visibility);

					$now = strtotime('now');

					if(strtotime($visibility['start_date']) <= $now && $now <= strtotime($visibility['end_date']))
					{
						$bool_flag = false;
						if($member['role'] == 'candidate' && $exam['exam_type'] == 'precondition')
						{
							$bool_flag = true;
						}

						if( $member['role'] == 'student' && ($exam['exam_type'] == 'current' || $exam['exam_type'] == 'final') )
						{
							$bool_flag = true;
						}

						if($bool_flag)
						{
							return true;
						}
						else
						{
							if($print_line){ echo __LINE__.'<br>'; }
							return false;
						}
					}
					else
					{
						// Времето е изтекло
						if($print_line){ echo date('Y-m-d H:i:s', $now); }
						if($print_line){ echo __LINE__.'<br>'; }
						return false;
					}
				}
				else
				{
					// Няма зададена видимост
					if($print_line){ echo __LINE__.'<br>'; }
					return false;
				}
			}
			else
			{
				// Няма такъв потребител свързан с този курс
				if($print_line){ echo __LINE__.'<br>'; }
				return false;
			}
		}
		else
		{
			if($print_line){ echo __LINE__.'<br>'; }
			return false;
		}

		// Не би трябвало да стигне до тук изобщо, ама знае ли човек...
		return true;
	}

	function Evaluate_Poll($exam_id, $input_data, $course_id = 0)
	{
		$exam_questions = $_SESSION['exam'][$exam_id]['questions'];

		//Pre_Print($input_data);
		//Pre_Print($exam_questions, true);

		$total_points = 0;
		$exam_points = 0;
		$percent = 0; // Define this variable because it will throw notice, if it is not defined.

		$percent_required = reset($exam_questions);
		$percent_required = $percent_required['percent_required'];

		foreach($exam_questions as $key => $question)
		{
			// Set default data or replace them
			$evaluation = 3; // Default value - If $question['question_type'] is strange or we have unexpected condition $evaluation will be equal to 3 (i.e. Unknown question type)
			$answer_id = 0;
			$evaluated_data = array(
					0 => array(
							'color' => 'poll_answer_terrible',
							'message' => 'Terrible' // This value can be rewritten from $question['feedback_error']
					),

					1 => array(
							'color' => 'poll_answer_bad',
							'message' => 'Bad' // This value can be rewritten from $question['feedback_error']
					),

					2 => array(
							'color' => 'poll_answer_neutral',
							'message' => 'Neutral' // This value can be rewritten from $question['feedback_error']
					),

					3 => array(
							'color' => 'poll_answer_good',
							'message' => 'Good' // This value can be rewritten from $question['feedback_error']
					),

					4 => array(
							'color' => 'poll_answer_best',
							'message' => 'Best' // This value can be rewritten from $question['feedback_error']
					),

					5 => array(
							'color' => 'poll_answer_best',
							'message' => 'Best' // This value can be rewritten from $question['feedback_error']
					),
			);

			$correct_answers = return_correct_answers_per_poll($question['answers']);

			$total_points += $correct_answers['max_question_points'];

			if(isset($input_data['questions'][$question['question_id']])) // If we want to show correct  answers only for checked questions, use this boolean condition: if(isset($input_data['questions'][$question['question_id']]))
			{
				$current_question_points = 0;
				$answer_id = $input_data['questions'][$question['question_id']];
				if($question['question_type'] == 1) // If we have "multi choise"
				{
					foreach($question['answers'] as $k => $answer)
					{
						if($answer['is_correct'] == 1) //
						{
							if(isset($input_data['questions'][$question['question_id']][$k]))
							{
								$current_question_points += $answer['answer_value'];
								$exam_points += $answer['answer_value'];
							}
							//$exam_questions[$key]['answers'][$k]['evaluated_class'] = 'answer_success';
						}
						$exam_questions[$key]['answers'][$k]['is_checked'] = 1;
					}
				}

				if($question['question_type'] == 2) // If we have "True/False" question
				{
					foreach($question['answers'] as $k => $answer)
					{

						if(true) // $answer['is_correct'] == 1
						{
							if(isset($input_data['questions'][$question['question_id']]) && $input_data['questions'][$question['question_id']] == $answer['id']) // If this option is selected from user
							{
								$exam_points += $answer['answer_value'];
								$current_question_points += $answer['answer_value'];
							}
						}
						$exam_questions[$key]['answers'][$input_data['questions'][$question['question_id']]]['is_checked'] = 1;
					}
				}


				if($question['question_type'] == 3) // If we have "fill in" question
				{
					$question['answers'] = reset($question['answers']);
					$question['answers']['is_checked'] = $input_data['questions'][$question['question_id']];

					if(trim($input_data['questions'][$question['question_id']]) != '')
					{
						if(true) //  strtolower(trim($question['answers']['answer'])) == strtolower(trim($input_data['questions'][$question['question_id']]))
						{
							$evaluation = 1;
							//$exam_points += $correct_answers['max_question_points'];
							$question['answers']['evaluated_class'] = 'answer_success';
						}
						else
						{
							$evaluation = 0;
							//$exam_points -= 1;  // If answer is incorrect student lose 1 point
							//$question['answers']['evaluated_class'] = 'answer_error';
						}
					}

					$exam_questions[$key]['answers'] = array($question['answers']['id'] => $question['answers']);
				}

				if($question['question_type'] >= 4) // If we have unknown question type
				{
					$evaluation = 3;
				}

				if(isset($evaluation)) //isset($evaluation)
				{


					$evaluation = (int) ($current_question_points / $correct_answers['possible_points'] * 100 / 20);
					$exam_questions[$key]['evaluated_data'] = $evaluated_data[$evaluation];

					if(SHOW_DEBUG_INFO)
					{

					}
				}



				if($exam_points <= 0)
				{
					$percent = 0;
				}
				else
				{
					$percent = $exam_points / $total_points * 100;
					$percent = number_format($percent, 2);
				}
/*
				//Add for statistics
				if($answer_id > 0 OR $question['question_type'] == 3)
				{
					$text_value = '';
					if(isset($exam_questions[$key]['evaluated_data']['message']))
					{
						$text_value = $exam_questions[$key]['evaluated_data']['message'];
					}
					$add = Model_Evaluation::addEvaluationResult($school_id, $course_id, $exam_id,$question['question_id'], Auth::instance()->get_user()->id, $answer_id, $current_question_points, $text_value);

				}
*/
			}
		}
//pre_print($correct_answers);
//pre_print($total_points);
//exit;
		//if(SHOW_DEBUG_INFO){ echo '<span class="debug display_block">Exam Points: '.$exam_points.' / Total Points: '.$total_points.'</span>'; }
		return array('exam_questions' => $exam_questions, 'percent' => $percent, 'is_pass' => ($percent >= $percent_required ? 1 : 0));
	}

	function CopyTest($test_id, $new_test_id = 0)
	{
		$test_data = to_assoc_array(exec_query('SELECT * FROM tests WHERE test_id = '.(int) $test_id));
//pre_print($test_data);
		if(Only_One_Exists($test_data))
		{
			$test_data = reset($test_data);
			if($new_test_id == 0)
			{
				$new_test_id = insert_query('tests', array('test_name' => $test_data['test_name'].' - Copy'));
				//Pre_Print($test_data);
			}

			// Get All Questions
			$questions = getAllQuestionsForExam($test_data['test_id']);
			//Pre_Print($questions);

			foreach($questions as $k => $question)
			{
				$question['answers'] = getAnswersPerQuestion($question['question_id']);

				$new_question_array = array(
					'test_id' => $new_test_id,
					'question_title' => $question['question_title'],
					'question_type' => $question['question_type'],
					'question_group' => $question['question_group'],
					'is_active' => $question['is_active'],
					'is_correct' => $question['is_correct'],
				);

				$new_question_id = insert_query('test_questions', $new_question_array);

				foreach($question['answers'] as $answer)
				{
					$new_answer_array = array(
						'question_id' => $new_question_id,
						'answer_title' => $answer['answer_title'],
						'is_correct' => $answer['is_correct'],
						'answer_value' => $answer['answer_value'],
						'answer_type' => $answer['answer_type'],
					);

					$new_answer_id = insert_query('test_question_answers', $new_answer_array);
				}
			}
		}
		else
		{
			if(SHOW_DEBUG_INFO)
			{
				echo 'Wrong Test_ID!';
			}
		}
	}

	function get_all_ancestors($where = null, $columns = '*')
	{
		$QGet = exec_query('SELECT '.$columns.' FROM courses_ancestors '.(!is_null($where) ? ' WHERE '.$where : ''));

		return to_assoc_array($QGet, 'ancestor_id');
	}

	function get_all_courses($where = null, $columns = '*')
	{
		$QGet = exec_query('SELECT '.$columns.' FROM courses '.(!is_null($where) ? ' WHERE '.$where : ''));

		return to_assoc_array($QGet, 'course_id');
	}

	function get_all_homeworks($where = null)
	{
		$QGet = exec_query('SELECT * FROM course_homeworks '.(!is_null($where) ? ' WHERE '.$where : ''));

		return to_assoc_array($QGet);
	}

	function array_sort($array, $on, $order=SORT_ASC)
	{
		$new_array = array();
		$sortable_array = array();

		if (count($array) > 0) {
			foreach ($array as $k => $v) {
				if (is_array($v)) {
					foreach ($v as $k2 => $v2) {
						if ($k2 == $on) {
							$sortable_array[$k] = $v2;
						}
					}
				} else {
					$sortable_array[$k] = $v;
				}
			}

			switch ($order) {
				case SORT_ASC:
					asort($sortable_array);
				break;
				case SORT_DESC:
					arsort($sortable_array);
				break;
			}

			foreach ($sortable_array as $k => $v) {
				$new_array[$k] = $array[$k];
			}
		}

		return $new_array;
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

	function get_all_certificates($where = null, $use_key = null)
	{
		$query = 'SELECT *, CONCAT(u.first_name, " ", u.last_name) AS full_name FROM user_certificates AS uc LEFT JOIN courses AS c ON uc.course_id = c.course_id LEFT JOIN courses_ancestors AS ca ON c.descendant_of = ca.ancestor_id LEFT JOIN users AS u ON u.user_id = uc.user_id LEFT JOIN (SELECT user_id, COUNT(*) AS certificates_count FROM user_certificates GROUP BY user_id) AS certificates ON certificates.user_id = uc.user_id'.(!is_null($where) ? ' WHERE '.$where : '');

		return to_assoc_array(exec_query($query), $use_key);
	}

	function getCertificates($per_course = null, $per_user = null, $use_key = null)
	{
		$query = '1=1';

		if(!is_null($per_course) && 0 < (int) $per_course)
		{
			$query .= ' AND uc.course_id = '.(int) $per_course;
		}

		if(!is_null($per_user) && 0 < (int) $per_user)
		{
			$query .= ' AND uc.user_id = '.(int) $per_user;
		}

		return get_all_certificates($query, $use_key);
	}

	function get_all_tests($where = null)
	{
		$QGet = exec_query('SELECT t.* FROM tests AS t'.(!is_null($where) ? ' WHERE '.$where : ''));

		return to_assoc_array($QGet, 'test_id');
	}

	function get_all_logs($where = null)
	{
		$QGet = exec_query('SELECT ul.* FROM user_logs AS ul'.(!is_null($where) ? ' WHERE '.$where : ''));

		return to_assoc_array($QGet, 'log_id');
	}

	function get_all_events($where = null)
	{
		$QGet = exec_query('SELECT * FROM events AS e'.(!is_null($where) ? ' WHERE '.$where : ''));

		return to_assoc_array($QGet);
	}

	function get_all_tags($where = null)
	{
		$QGet = exec_query('SELECT * FROM course_homeworks_tags AS cht'.(!is_null($where) ? ' WHERE '.$where : '').' ORDER BY tag_name');

		return to_assoc_array($QGet, 'tag_id');
	}

	function getTagData($tag_id, $column = null)
	{
		$tag_data = get_all_tags('tag_id = '.(int) $tag_id);

		if(Only_One_Exists($tag_data))
		{
			$tag_data = reset($tag_data);

			if(is_null($column))
			{
				return $tag_data;
			}
			else
			{
				if(isset($tag_data[$column]))
				{
					return $tag_data[$column];
				}
				else
				{
					trigger_error('Tag Column "'.$column.'" NOT exists!', E_USER_WARNING);
					return $tag_data;
				}
			}
		}
		else
		{
			trigger_error('Tag #'.$tag_id.' NOT Found!', E_USER_ERROR);
			return false;
		}
	}

	function ReturnDropDown($array, $default_value = null)
	{
		if(is_array($array))
		{
			$options = '';
			foreach($array as $option_value => $option_text)
			{
				$options .= '<option value="'.$option_value.'"'.($option_value == $default_value ? ' selected="selected"' : '' ).'>'.(is_array($option_text) ? end($option_text) : $option_text).'</option>';
			}

			return $options;
		}
		else
		{
			trigger_error('First param must be an array! Function '.__FUNCTION__.', line '.__LINE__.', file '.__FILE__, E_USER_ERROR);
		}
	}

	function RenderDropDown($array, $default_value = null)
	{
		echo ReturnDropDown($array, $default_value);
	}

	function excerpt_text($text, $max_chars_on_row, $return_both = false)
	{
		$excerpt_array = array('summary' => mb_substr($text, 0, $max_chars_on_row), 'details' => mb_substr($text, $max_chars_on_row));
		if($return_both)
		{
			return $excerpt_array;
		}
		else
		{
			return $excerpt_array['summary'];
		}
	}

	function excerpt_details_summary($text, $max_chars_on_row, $after_details = '', $details_attr = '')
	{
		$excerpt_text = excerpt_text($text, $max_chars_on_row, true);
		$details_attr = ($details_attr != '' && substr($details_attr,0,1) != ' ' ? ' '.$details_attr : $details_attr);
		return '<details'.$details_attr.'><summary>'.$excerpt_text['summary'].'</summary>'.$excerpt_text['details'].$after_details.'</details>';
	}

	function getLogData($log_id)
	{
		$log_data = exec_query('SELECT ul.*, u.first_name, u.last_name FROM user_logs AS ul LEFT JOIN users AS u ON ul.user_id = u.user_id WHERE log_id = '.(int) $log_id);
		return to_assoc_array($log_data);
	}

	function getUserLastOnline($user)
	{
		$last_log = get_all_logs('user_id = '.(int) $user.' ORDER BY log_time DESC LIMIT 1');
		if( Only_One_Exists($last_log) )
		{
			$last_log = reset($last_log);
			return $last_log['log_time'];
		}
		else
		{
			return 0;
		}
	}

	function show_events($today = null)
	{
		if(is_null($today))
		{
			$today = date('Y-m-d');
		}

		if(is_string($today))
		{
			$events = get_all_events('start_date <= "'.$today.'" AND end_date >= "'.$today.'" AND is_active = 1');

			if(count($events) > 0)
			{
				foreach($events as $event)
				{
					$event['content'] = trim($event['content']);

					$event['content'] = str_ireplace('{EVENT_IMAGE}', $event['image'], $event['content']);
					$event['content'] = str_ireplace('{EVENT_TITLE}', $event['title'], $event['content']);

					// Ако е линк към някакъв друг документ или е линк, използван за връзка с JQuery, например: /courses/ или само #
					$link_attr = 'href="'.$event['href'].'" target="_self"';

					if(mb_substr($event['href'], 0, 4) == 'http') // Ако е външен линк например: http://google.com/
					{
						$link_attr = 'href="'.$event['href'].'" target="_blank"';
					}

					if(mb_substr($event['href'], 0, 1, 'UTF-8') == '#' && $event['content'] != '#') // Например ако е: #EventModal
					{
						$event['href'] = mb_substr($event['href'], 1, null, 'UTF-8');
						$link_attr = 'href="#" data-toggle="modal" data-target="#'.$event['href'].'"';

						$event['content'] = explode('|', $event['content']);

						if(isset($event['content'][1]) && $event['content'][1] != '')
						{
							$event['content'][1] = '<a href="'.$event['content'][1].'">Още по темата</a>';
						}
						else
						{
							$event['content'][1] = '';
						}

						if(!isset($event['content'][2]))
						{
							$event['content'][2] = 'Честит Празник';
						}

						echo '<div class="modal fade" id="'.$event['href'].'" tabindex="-1" role="dialog" aria-labelledby="'.$event['href'].'lLabel" aria-hidden="true">
								<div class="modal-dialog" style="width:900px;">
									<div class="modal-content">
										<div class="modal-header">
											<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
											<h4 class="modal-title" id="'.$event['href'].'Label">'.$event['content'][2].'</h4>
										</div>
										<div class="modal-body">'.$event['content'][0].'</div>
										<div class="modal-footer">'.$event['content'][1].'</div>
									</div>
								</div>
							</div>';
					}

					echo '<a '.$link_attr.'><img src="'.$event['image'].'" title="'.$event['title'].'" alt="'.$event['title'].'" onerror="this.style.display = \'none\';" style="'.$event['style'].'" /></a>';
				}
			}
		}
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

	function get_user_homeworks($where = null)
	{
		$query_string = 'SELECT * FROM user_homeworks AS uh LEFT JOIN course_homeworks AS ch ON uh.homework_id = ch.homework_id'.(!is_null($where) ? ' WHERE '.$where : '');

		return to_assoc_array(exec_query($query_string));
	}

	function get_user_settings($users  = null)
	{
		if(is_null($users))
		{
			$where = null;
		}
		else
		{
			if(is_numeric($users))
			{
				// Convert it to array
				$users = array($users);
			}

			if(is_array($users))
			{
				$where = 'user_id IN ('.implode(',', $users).')';
			}
			else
			{
				$where = $users; // Maybe It is string... we use it
			}
		}

		$query_string = 'SELECT * FROM user_settings'.(!is_null($where) ? ' WHERE '.$where : '');

		$settings = to_assoc_array(exec_query($query_string), 'user_id');

		if(is_string($users) || (count($users) == count($settings)))
		{
			return $settings;
		}
		else
		{
			// Settings not found - insert new record with default settings
			if(is_array($users) && (count($users) != count($settings)) )
			{
				foreach($users as $user)
				{
					if(!isset($settings[$user]))
					{
						insert_query('user_settings', array('user_id' => $user));
					}
				}
			}

			// Get Settings again
			return get_user_settings($users);
		}
	}

	function get_registration_info($entries_id)
	{
		return to_assoc_array(exec_query('SELECT * FROM wp_visual_form_builder_entries WHERE entries_id = '.$entries_id));
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

	function getMondaysInRange($dateFromString, $dateToString)
	{
		$dateFrom = new \DateTime($dateFromString);
		$dateTo = new \DateTime($dateToString);
		$dates = [];

		if ($dateFrom > $dateTo) {
			return $dates;
		}

		if (1 != $dateFrom->format('N')) {
			$dateFrom->modify('next monday');
		}

		while ($dateFrom <= $dateTo) {
			$dates[] = $dateFrom->format('Y-m-d');
			$dateFrom->modify('+1 week');
		}

		return $dates;
	}

	function showActivityTable($user, $course)
	{
		$activity_array = to_assoc_array(exec_query('SELECT * FROM course_dates WHERE course_id = '.$course.' ORDER BY lecture_date ASC'));
		//pre_print($activity_array);

		if(count($activity_array))
		{
			$str = '<table border="1"><tr>';

			foreach($activity_array as $day_activity)
			{
				// Set default cell text
				$text = '&nbsp;';

				// If it is future date
				if(date('Y-m-d') < $day_activity['lecture_date'])
				{
					$color = 'silver';
				}
				else
				{
					if($day_activity['users_count'] > 0)
					{
						$day_activity['present_users'] = explode(',', $day_activity['present_users']);

						if(in_array($user, $day_activity['present_users']))
						{
							$color = 'lime';
						}
						else
						{
							$color = 'red';
						}
					}
					else
					{
						$day_activity['lecture_date'] = 'Обновете присъствията за ' . $day_activity['lecture_date'];
						$color = 'darkblue; padding:2px 0 0 2px'; // gray
						$text = '<span class="glyphicon glyphicon-refresh refresh_visibility" data-id="'.$day_activity['id'].'"></span>';
					}
				}

				$str .= '<td style="width:20px; background-color: '.$color.'; color:white;" title="'.$day_activity['lecture_date'].'">'.$text.'</td>';
			}

			$str .= '</tr></table>';
		}
		else
		{
			$str  = '<i>Няма дефинирани дати!</i>';
			$str .= '<!-- showActivityTable: $activity_array is empty :( -->';
		}

		return $str;
	}

	function my_phone_format($phone_string, $phone_format = null)
	{
		/*
		 * ===========================
		 * Available Phone Formats:
		 * 0 - 0894 48 11 00 (Default)
		 * 1 - 089 448 1100
		 * ===========================
		 */

		Global $user_id;

		$phone_string = str_ireplace(' ', '', $phone_string);
		$phone_string = str_ireplace('+359', '0', $phone_string);

		if(is_null($phone_format)) // Use default date format by user
		{
			$phone_format = 0; // Set Default format

			if($user_id == 9) // Чанита Попова
			{
				$phone_format = 1;
			}
		}

		// 0894481100 - phone number
		// 0123456789 - string position

		if(0 == $phone_format)
		{
			$phone_string = substr($phone_string,0,4) . ' ' . substr($phone_string,4,2) . ' ' . substr($phone_string,6,2) . ' ' . substr($phone_string,8,2);
		}

		if(1 == $phone_format)
		{
			$phone_string = substr($phone_string,0,3) . ' ' . substr($phone_string,3,3) . ' ' . substr($phone_string,6,4);
		}

		return $phone_string;
	}

	function get_all_mails($where = null)
	{
		$QGet = exec_query('SELECT * FROM mails AS m LEFT JOIN users AS u ON m.created_from = u.user_id'.(!is_null($where) ? ' WHERE '.$where : ''));

		return to_assoc_array($QGet);
	}

	function getMailData($mail_id)
	{
		$mail = get_all_mails('mail_id = '.(int) $mail_id);
		if(Only_One_Exists($mail))
		{
			$key = reset(array_keys($mail));

			$mail[$key]['recipients'] = getMailRecipients($mail_id);
			return $mail;
		}
		else
		{
			return array();
		}
	}

	function getMailRecipients($mail_id)
	{
		$QGet = exec_query('SELECT * FROM mail_recipients AS mr LEFT JOIN users AS u ON mr.user_id = u.user_id WHERE mr.mail_id = '.(int) $mail_id);

		return to_assoc_array($QGet);
	}

	function getMailForUser($look_for_user)
	{
		$QGet = exec_query('SELECT m.*, mr.*, u.user_id, u.first_name, u.last_name FROM mail_recipients AS mr INNER JOIN mails AS m ON mr.mail_id = m.mail_id LEFT JOIN users AS u ON m.created_from = u.user_id WHERE mr.user_id = '.(int) $look_for_user);

		return to_assoc_array($QGet);
	}

	function my_date_format($date_timestamp, $date_format = null, $zero_text = 'липсва')
	{
		if(is_null($date_format)) // Use default date format by user
		{
			$date_format = 'Y-m-d H:i:s'; // Set Default format
		}

		if(0 == $date_timestamp)
		{
			return $zero_text;
		}
		else
		{
			if(!is_numeric($date_timestamp))
			{
				$date_timestamp = strtotime($date_timestamp);
			}

			return date($date_format, $date_timestamp);
		}
	}

	/*
	 * First param can be null. In this case it will be current time
	 * By Default return timestamp. If second param is false, return ISO date
	 */
	function get_current_period($return_as_int = true, $time_variant = null)
	{
		if(is_null($time_variant))
		{
			//$time_variant = strtotime('now');
			$y = date('Y'); // A full numeric representation of a year, 4 digits
			$m = date('n'); // Numeric representation of a month, without leading zeros
		}
		else
		{
			if(!is_numeric($time_variant))
			{
				$time_variant = strtotime($time_variant);
			}

			$y = date('Y', $time_variant);
			$m = date('n', $time_variant);
		}

		$return_value = '';

		switch(true)
		{
			case ($m >= 1 && $m <= 4 ): $begin_value = $y.'-01-01'; $end_value = $y.'-04-30'; break;
			case ($m >= 5 && $m <= 8 ): $begin_value = $y.'-05-01'; $end_value = $y.'-08-31'; break;
			case ($m >= 9 && $m <= 12): $begin_value = $y.'-09-01'; $end_value = $y.'-12-31'; break;
		}

		if(!is_null($return_as_int))
		{
			if($return_as_int)
			{
				$return_value = strtotime($begin_value).' AND '.strtotime($end_value);
			}
			else
			{
				$return_value = '"'.$begin_value.'" AND "'.$end_value.'"';
			}
		}
		else
		{
			return array('begin_time' => strtotime($begin_value), 'end_time' => strtotime($end_value), 'begin_date' => $begin_value, 'end_date' => $end_value);
		}

		return $return_value;
	}

	function insert_mail($mail, $recipients)
	{
		Global $user_id;
		
		if(!isset($mail['created_from']))
		{
			$mail['created_from'] = $user_id;
		}

		$mail_id = insert_query('mails', array('mail_subject' => $mail['mail_subject'], 'mail_content' => $mail['mail_content'], 'created_from' => $mail['created_from'], 'created_on' => strtotime('now') + TIME_CORRECTION));

		foreach($recipients as $key => $value)
		{
			$data_array = array(
				'mail_id' => $mail_id,
			);

			if($key < 0)
			{
				$data_array['user_id'] = abs($key);
				$data_array['email'] = $value;
			}
			else
			{
				if(is_numeric($value))
				{
					$data_array['user_id'] = $value;
				}
				else
				{
					$data_array['user_id'] = 0;
					$data_array['email'] = $value;
				}
			}

			insert_query('mail_recipients', $data_array);
		}
	}
?>