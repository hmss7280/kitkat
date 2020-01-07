<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
|------------------------------------------------------------------------
| Author : 김옥훈
| Create-Date : 2014-08-21
|------------------------------------------------------------------------
*/

Class Model_position extends MY_Model {

	
	public function savePosition($data) {
		$position=$data['position'];
		
		$sql = "insert into position (position,order_no,ins_date,upd_date) SELECT ?,count(position_seq)+1,NOW(),NOW() FROM position ";	
		
		$this->db->trans_begin();
		$this->db->query($sql
								,array(
									 $position
									)
								);
		

		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			return "fail";
		}else{
			$this->db->trans_commit();
			return "success";
		}

	}
	public function modifyPosition($data) {
		$position=$data['position'];
		$order_no=$data['order_no'];
		$position_seq=$data['position_seq'];
		
		$sql = "update position set position=?,order_no=?,upd_date=NOW() where position_seq=? ";	
		
		$this->db->trans_begin();
		$this->db->query($sql
								,array(
									 $position
									 ,$order_no
									 ,$position_seq
									)
								);
		

		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			return "fail";
		}else{
			$this->db->trans_commit();
			return "success";
		}

	}	
	public function removePosition($data) {

		$position_seq=$data['position_seq'];
		
		$sql = "delete from position  where position_seq=? ";	
		
		$this->db->trans_begin();
		$this->db->query($sql
								,array(
									 $position_seq
									)
								);
		

		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			return "fail";
		}else{
			$this->db->trans_commit();
			return "success";
		}

	}	
	public function listPosition() {
		
		$sql = "select * from position order by order_no";	
	
		return $this->db->query($sql)->result();

	}

}
?>