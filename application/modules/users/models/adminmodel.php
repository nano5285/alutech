<?php
class Adminmodel extends CI_Model
{
	function __construct() {
		parent::__construct();
	}
	
	/**
	 * Get Users List
	 */			
	function get($per_page = '', $segment = '') {
		$dbprefix = $this->db->dbprefix;
		$this->db->select(
						  USERS_DB_TABLE.'.id,
						  '.USERS_DB_TABLE.'.group_id,
						  '.USERS_DB_TABLE.'.username,
						  '.USERS_DB_TABLE.'.name,
						  '.USER_GROUPS_DB_TABLE.'.description AS user_group_title
						  ');

		$this->db->join(USER_GROUPS_DB_TABLE, USER_GROUPS_DB_TABLE.'.id = '.USERS_DB_TABLE.'.group_id', 'left');
		$this->db->group_by(USERS_DB_TABLE.'.id');
		$this->db->order_by(USERS_DB_TABLE.'.id', 'desc');

		$tempdb = clone $this->db;

		// count user posts
		$this->db->select('COUNT('.$dbprefix.POSTS_DB_TABLE.'.author) AS post_num');
		$this->db->join(POSTS_DB_TABLE, POSTS_DB_TABLE.'.author = '.USERS_DB_TABLE.'.id', 'left');

		$this->db->limit($per_page, $segment);

		return array(
			'num_rows' => $tempdb->get(USERS_DB_TABLE)->num_rows(),
			'query' => $this->db->get(USERS_DB_TABLE)
		);
	}

	/**
	 * Get User Groups
	 * User will only see user groups with the same or lower access level then him self
	 */		
	function get_groups($role = '') {
		$this->db->select('id, title, description');
		return $this->db->get_where(USER_GROUPS_DB_TABLE, array('id <=' => $this->session->userdata('group_id')))->result();	
	}


	/**
	 * Get specific user data
	 */	
	function get_user($id) {
		return $this->db->get_where(USERS_DB_TABLE, array('id' => $id))->row();
	}


	/**
	 * Saving data
	 */
	function save($id = '') {	
		/**
		 * If editing profile, get user id from the user session
		 */
		$id = ($this->uri->segment(3) == 'profile') ? $this->session->userdata('user_id') : $this->uri->segment(4);
		$password = $this->input->post('password');
		
		$data = array(
					  'username' => $this->input->post('username'),
					  'name' => $this->input->post('name'),
					  'bio' => $this->input->post('bio'),
					  );
		
		// If ID is not defined (new user)
		if ($id == FALSE) {
			$data['password'] = $this->auth->encrypt($password);
			$data['group_id'] = $this->input->post('group');

			$this->db->set($data)->insert(USERS_DB_TABLE);
			return $this->db->insert_id();	
		} else if ($id == true || $this->uri->segment(3) == 'profile') {	
			// If new password has been entered, encrypt it
			if ($password != false) {
				$data['password'] = $this->auth->encrypt($password);
			}
			
			// If editing profile, use the same user group
			if ($this->uri->segment(3) != 'profile') {	
				$data['group_id'] = $this->input->post('group');
			}
			
			$this->db->set($data)->where('id', $id)->update(USERS_DB_TABLE);
			return $id;
		}
	}

	/**
	 * When deleting a user, all posts from that user must be updated with another author/user
	 */
	function update_posts($author, $new_author) {
		$data['author'] = $new_author;
		$this->db->set($data)->where('author', $author)->update(POSTS_DB_TABLE);
	}
}
?>