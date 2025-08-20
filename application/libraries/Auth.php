<?php

class Auth
{
    var $CI;
    var $_username;
    var $_table = array(
                    'users' => USERS_DB_TABLE,
                    'groups' => USER_GROUPS_DB_TABLE
                    );

    function __construct() {
        $this->CI =& get_instance();
        $this->CI->load->helper('url');
        $this->CI->load->helper('string');
		$this->CI->load->helper('cookie');
	}

    function Auth() {
        self::__construct();
    }

	/**
	 * Restrict access for specific user groups
	 */	
    function restrict($restrict_to = NULL, $redirect_to = NULL) {
		// Redirect to admin homepage if user has not privileges to access the page
		$redirect_to = ($redirect_to == NULL) ? $this->CI->config->item('base_url') . ADMIN_URL : $redirect_to;
		
		/**
		 * Check if user is logged in and if the access is granted
		 * If access is not granted, redirect to user profile
		 * If user is not logged in, redirect to homepage
		 */	
		if ($restrict_to !== NULL) {
			if ($this->logged_in() == TRUE AND $this->CI->session->userdata('group_id') >= $restrict_to) {
				return TRUE;
			} else {
				$this->CI->session->set_flashdata('error', $this->CI->lang->line('insufficient_rights'));
				redirect($redirect_to);	
			}
		} else {
			redirect($this->CI->config->item('base_url'));
		}
	}

	/**
	 * Check if username exist
	 */	
	function username_exists( $username ) {
		$this->CI->db->select('username');
		$query = $this->CI->db->get_where($this->_table['users'], array('username' => $username), 1);
		if ($query->num_rows() !== 1) {
			return FALSE;
		} else {
			$this->_username = $username;
			return TRUE;
		}
	}

	/**
	 * Encrypt provided password and check if it is valid
	 */	
	function check_password( $password ) {
		$this->CI->db->select('password');
		$query = $this->CI->db->where($this->_table['users'], array('username' => $this->_username), 1)->row();
		if ($query->password === $this->encrypt($password)) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	/**
	 * Check length of the string
	 */	
    function check_string_length( $string ) {
		$string = trim($string);
		return strlen($string);
    }

	/**
	 * Encrypt password
	 */	
    function encrypt( $data ) {
		if ($this->CI->config->item('encryption_key') !== NULL) {
			return sha1($this->CI->config->item('encryption_key').$data);
		} else {
			show_error('Please set an encryption key in your config file.');
		}
    }
	
	/**
	 * Login
	 */		
	function login($username, $password, $redirect_to = NULL, $error_view = NULL) {		
		// Get specific user from the db
		$query = $this->CI->db->get_where($this->_table['users'], 
										array(
											'username' => $this->CI->security->xss_clean($username),
											'password' => $this->CI->security->xss_clean($this->encrypt($password))
											), 1
									  );
		
		/**
		 * If user exist, start new session and redirect.
		 * If user does not exist, return error
		 */
		if ($query->num_rows() === 1) {
			session_start();
			$row = $query->row();
			$data = array(
							'logged_in' => TRUE,
							'sess_expire_on_close' => TRUE,
							'username' => $row->username,
							'user_id' => $row->id,
							'name' => $row->name,
							'group_id' => $row->group_id
						  );
			$this->CI->session->set_userdata($data);
						
			// Enable kcfinder file manager
			$_SESSION['KCFINDER'] = array();
			$_SESSION['KCFINDER']['disabled'] = false;			
			
			redirect($redirect_to);
		} else {
			if ($error_view != NULL) {
				$data['error'] = $this->CI->lang->line('incorrect_login');
				$this->CI->load->view($error_view, $data);
			} else {
				redirect($redirect_to);
			}
		}
	}

	/**
	 * Check if user is logged in
	 */	
    function logged_in() {
		return $this->CI->session->userdata('logged_in');
    }

	/**
	 * Logout
	 *
	 * Disable KCfinder file manager, destroy session and redirect to another URL
	 */	
    function logout($redirect_to = NULL) {		
		session_start();
		$_SESSION['KCFINDER'] = array();
		$_SESSION['KCFINDER']['disabled'] = true;			
		
		$this->CI->session->sess_destroy();

		if ($redirect_to != NULL) {
			redirect($redirect_to);	
		}
    }
	
}

/* End of file Auth.php */
/* Location: ./application/libraries/Auth.php */