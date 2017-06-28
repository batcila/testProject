<?php

	if (!function_exists('get_user_by_id')) {
		function get_user_by_id( $id ) {
			$id = (int) $id;
			$data = exec_query('SELECT uc.*, u.username, u.first_name, u.last_name FROM users AS u LEFT JOIN user_contacts AS uc ON u.user_id=uc.user_id WHERE u.user_id=' . $id );
			$data = to_assoc_array($data);
			return $data;
		}
	} else { trigger_error('function exist ! on ' . __LINE__ . ' in ' . __FILE__, E_USER_ERROR); }

	if (!function_exists('get_current_user_id')) {
		function get_current_user_id() {
			if (isset($_SESSION['CURRENT_USER'])) {
				return $_SESSION['CURRENT_USER']['user_id'];
			} else {
				return false;
			}
		}
	} else { trigger_error('function exist ! on ' . __LINE__ . ' in ' . __FILE__, E_USER_ERROR); }
	

?>