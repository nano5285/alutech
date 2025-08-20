<?php
class Commonmodel extends CI_Model
{
	function __construct() {
		parent::__construct();
	}

	/**
	 * Get data from a DB table
	 */	
	function get($options = array()) {
		// Required values
		// if(!$this->_required(array('module'), $options)) return false;

		// Default values
		$options = $this->_default(array('sort_column' => 'id' ,'sort_direction' => 'desc'), $options);

		if(isset($options['search_query'])) {		
			// If language is defined...
			if (isset($options['lang']) AND $options['lang']) {
				$this->db->where('lang', $options['lang']);
			}
			
			// Search fields with provided keyword
			$term = strtolower(@$options['search_query']);
			if(isset($options['identifier'])) {
				$this->db->where("(LOWER(title) LIKE '%{$term}%' OR LOWER(content) LIKE '%{$term}%' OR LOWER(identifier) LIKE '%{$term}%')");
			} else {
				$this->db->where("(LOWER(title) LIKE '%{$term}%' OR LOWER(content) LIKE '%{$term}%')");
			}

			// Where clause parameters
			$qualificationArray = array('module', 'item_id', 'title');		
		} else {
			// Where clause parameters when not searching
			$qualificationArray = array('id', 'post_id', 'status', 'identifier', 'module', 'slug', 'lang', 'menu_id', 'item_id');	
		}	
			
		// Define where clause		
		foreach($qualificationArray as $qualifier) {
			if(isset($options[$qualifier])) $this->db->where($qualifier, $options[$qualifier]);
		}

		// Sort
		if(isset($options['sort_column']) || isset($options['sort_direction'])) {
			$this->db->order_by($options['sort_column'], $options['sort_direction']);
		}

		// Clone/save the currenty query so we can return the total rows number
		$tempdb = clone $this->db;

		// If limit / offset is declared		
		if(isset($options['limit']) && isset($options['offset'])) {
			$this->db->limit($options['limit'], $options['offset']);
		} else if(isset($options['limit'])) {
			$this->db->limit($options['limit']);
		}	

		return array(
			'num_rows' => $tempdb->get($options['table'])->num_rows(),
			'query' => $this->db->get($options['table']),
		);
	}

	/**
	 * Delete data from a DB table
	 */	
	function delete($options = array()) {
		// add where clauses to query
		$qualificationArray = array('id', 'post_id', 'menu_id', 'identifier', 'module', 'slug', 'lang');
		foreach($qualificationArray as $qualifier) {
			if(isset($options[$qualifier])) $this->db->where($qualifier, $options[$qualifier]);
		}

		// delete items from db and return true or false depending on affected rows
		$this->db->delete($options['table']);
		if ( $this->db->affected_rows() > 0 ) {
			return true;
		}
	}

	/**
	 * Insert data to the DB table
	 */		
	function insert($table, $data) {
		$this->db->set($data);
		$this->db->insert($table);
	}

	/**
	 * Update data
	 */	
	function update($options = array(), $data) {
		// add where clauses to query
		$qualificationArray = array('id', 'post_id', 'menu_id', 'identifier', 'module', 'slug', 'lang');
		foreach($qualificationArray as $qualifier) {
			if(isset($options[$qualifier])) $this->db->where($qualifier, $options[$qualifier]);
		}

		// delete items from db and return true or false depending on affected rows
		$this->db->update($options['table'], $data);
		if ( $this->db->affected_rows() > 0 ) {
			return true;
		}
	}
	
	function encrypt($data) {
		 // sha1 encryption
		 return sha1($this->config->item('encryption_key').$data);
	}	

	function count($table) {
		// count and return total rows in the db table
		return $this->db->count_all($table);
	}

	/**
	* _required method returns false if the $data array does not contain all of the keys assigned by the $required array.
	*
	* @param array $required
	* @param array $data
	* @return bool
	*/
	function _required($required, $data) {
		foreach($required as $field) if(!isset($data[$field])) return false;
		return true;
	}

	/**
	* _default method combines the options array with a set of defaults giving the values in the options array priority.
	*
	* @param array $defaults
	* @param array $options
	* @return array
	*/
	function _default($defaults, $options) {
		return array_merge($defaults, $options);
	}

	/**
	 * Post meta data (custon fields)
	 */
	function get_post_meta($options = array()) {
		$qualificationArray = array('id', 'post_id', 'identifier', 'module', 'lang');
		foreach($qualificationArray as $qualifier) {
			if(isset($options[$qualifier])) $this->db->where($qualifier, $options[$qualifier]);
		}
		return $this->db->get('post_meta');
	}

	/**
	 * Processing custom fields
	 */
	function save_post_meta($options = array()) {
		$custom_fields = $this->input->post('custom_field');
		if ($custom_fields) {
			foreach($custom_fields as $field => $value) {
				$custom_field_id = $custom_fields[$field]['id'];
				$custom_field_data = array(
					'content' => $custom_fields[$field]['content'],
					'editor' => $custom_fields[$field]['editor'],
					'lang' => $this->input->post('lang')
				);

				/**
				 * If custom field does not already exist, it will be inserted as a new item.
				 * If it does, it will be updated.
				 */
				if ( !$custom_field_id ) {
					$custom_field_data['post_id'] = $options['post_id'];
					$custom_field_data['module'] = $options['module'];
					$custom_field_data['identifier'] = $custom_fields[$field]['identifier'];

					$this->db->insert(POST_META_DB_TABLE, $custom_field_data);
				} else {
					$this->db->set($custom_field_data);
					$this->db->where('id', $custom_field_id)->update(POST_META_DB_TABLE, $custom_field_data);
				}
			}
		}	
	}
	
}
?>