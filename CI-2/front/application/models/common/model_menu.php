<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
|------------------------------------------------------------------------
| Author : 김옥훈
| Create-Date : 2014-08-28
|------------------------------------------------------------------------
*/

Class Model_menu extends MY_Model {

	
	public function leftSubMenu($data) {
		$project_seq=$data['project_seq'];
		
		$sql = "select * from board_manage where project_seq=? and useYn='Y' order by order_no";	

		return $this->db->query($sql
									,array(
									 	$project_seq
									 )
								)->result();

	}
	
	public function leftFirstSubMenu($data) {
		$project_seq=$data['project_seq'];
		$sql = "select board_seq,count(*)as cnt  from board_manage where project_seq=? and useYn='Y' order by order_no";	
		
		return $this->db->query($sql
								,array(
									 	$project_seq
									 )
								)->result();

	}
	
	
	
	
	
}
?>