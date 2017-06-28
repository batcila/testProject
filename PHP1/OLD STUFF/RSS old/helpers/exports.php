<?php

	// Backup the table and save it to a sql file
	function export_certificates()
	{
		Global $_connection, $user_id;
		$return = "";

		// Set the suffix of the backup filename
		/*
		if ($tables == '*') {
			$extname = 'all';
		}else{
			$extname = str_replace(",", "_", $tables);
			$extname = str_replace(" ", "_", $extname);
		}
		*/

		$extname = 'certificates';

		// Generate the filename for the backup file
		$filess = LOCAL_PATH.'exports/export_' . $extname . '_' . date("Y-m-d_H-i-s") . '_' . $user_id . '.csv' ;

		$users = to_assoc_array(exec_query('SELECT * FROM users WHERE user_id IN (SELECT DISTINCT user_id FROM user_certificates)'));

		$caption = '"Salutation", "First Name", "Lead Number", "Surname", "Primary Phone", "Last Name", "Mobile Phone", "Applies for position", "Assigned To", "HR Status", "Created Time", "CV Source", "Created By", "CV Received", "Modified Time", "Primary Email", "Secondary Email", "Last Modified By", "Referred by", "Lead Type", "Skills", "Street", "PO Box", "Postal Code", "City", "Country", "State", "Description", "Confirmed to start", "Start Date", "Expectations", "Registration date", "Number of certificates", "Cathegory", "Technologies", "Certified in", "Previous dev experience", "Employment status", "Education"'.PHP_EOL;
		$settings = get_user_settings(array_column($users, 'user_id'));

		$castes_array = array('1' => 'White', '0' => 'Neutral', '-1' => 'Black');
		$important_fields = array(
			'education' => 'Образование',
			'employment' => 'Позиция на пазара на труда',
			'expectations' => 'Какво очаквате от нашата академия ?'
		);

		foreach($users as &$user)
		{
			$user['certificates'] = getCertificates(null, $user['user_id']);
			$user['registration'] = get_registration_info($user['yahoo']);
			$user['settings'] = $settings[$user['user_id']];

			// Set Default values
			$user['dev_experience'] = '';
			foreach($important_fields as $key => $field)
			{
				$user[$key] = '';
			}

			//Convert Some Data
			$user['caste_name'] = $castes_array[ $user['settings']['user_caste'] ];
			if(Only_One_Exists($user['registration']))
			{
				$user['registration'] = reset($user['registration']);
				$user['registration'] = unserialize($user['registration']['data']);

				foreach($user['registration'] as $field)
				{
					if(in_array($field['name'], $important_fields))
					{
						$key = array_search($field['name'], $important_fields);
						$user[$key] = $field['value'];
					}

					if($field['id'] == 17) // Hard Code Value for Prev Dev Experience
					{
						$user['dev_experience'] = $field['value'];
					}
				}
			}


			// Data Template
			$fields = array(
				'salutation' => '',
				'first_name' => transliterate($user['first_name']),
				'lead_number' => 'WA_'.$user['user_id'], // Remove Maybe?
				'surname' => '',
				'primary_phone' => $user['aim'],
				'last_name' => transliterate($user['last_name']),
				'mobile_phone' => '',
				'applies_for_pos' => '',
				'assigned_to' => 'snezhina.marburg',
				'hr_status' => '',
				'created_time' => date('d-m-Y H:i:s'),
				'CV_Source' => 'webacademy',
				'Created_By' => 'yordan.enev', // Remove Maybe?
				'CV_Received' => '--',
				'Modified_Time' => '',
				'Primary_Email' => $user['user_email'],
				'Secondary_Email' => '',
				'Last_Modified_By' => '',
				'Referred_by' => 'yordan.enev',
				'Lead_Type' => 'Student',
				'Education' => transliterate($user['education']),
				'Skills' => '',
				'Street' => '',
				'PO_Box' => '',
				'Postal_Code' => '',
				'City' => '',
				'Country' => '',
				'State' => '',
				'Description' => transliterate($user['settings']['user_descr']),
				'Confirmed_to_start' => '0',
				'Start_Date' => '--',
				'Registration_date' => date('d.m.Y', strtotime($user['user_registered'])),
				'Number_of_certificates' => count($user['certificates']),
				'Cathegory' => $user['caste_name'],
				'Technologies' => implode(' |##| ', array_column($user['certificates'], 'ancestor_tech')),
				'Certified_in' => implode(' |##| ', array_column($user['certificates'], 'ancestor_name')),
				'wa_number' => $user['user_id'],
				'Expectations' => transliterate($user['expectations']),
				'Employment_status' => transliterate($user['employment']),
				'Previous_dev_experience' => transliterate($user['dev_experience'])
			);

			foreach($fields as &$field)
			{
				$field = '"' . $field . '"';
			}

			$return .= implode(',', $fields).PHP_EOL;
		}

		//pre_print($fields);

		$return = $caption.$return;
		// Save the csv file
		$handle = fopen($filess,'w+');
		fwrite($handle,$return);
		fclose($handle);

		//echo '<a href="'.$filess.'">here</a>';
		header('location: /'.$filess);
		exit;
	}
?>