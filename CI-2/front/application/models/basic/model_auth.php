<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
|------------------------------------------------------------------------
| Author : 이현주
| Create-Date : 2014-07-10
|------------------------------------------------------------------------
*/

Class Model_auth extends MY_Model {

	
	public function listAuth() {
		
		$sql = "select * from auth order by order_no";	
	
		return $this->db->query($sql)->result();

	}
	
	
	
	
}
?>