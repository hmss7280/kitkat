<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
|------------------------------------------------------------------------
| Author : 이현주
| Create-Date : 2014-07-10
|------------------------------------------------------------------------
*/

Class Model_roll extends MY_Model {

	
	public function saveRoll($data) {
		$roll=$data['roll'];
		
		$sql = "insert into roll (roll,order_no,ins_date,upd_date) SELECT ?,count(roll_seq)+1,NOW(),NOW() FROM roll ";	
		
		$this->db->trans_begin();
		$this->db->query($sql
								,array(
									 $roll
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
	public function modifyRoll($data) {
		$roll=$data['roll'];
		$order_no=$data['order_no'];
		$roll_seq=$data['roll_seq'];
		
		$sql = "update roll set roll=?,order_no=?,upd_date=NOW() where roll_seq=? ";	
		
		$this->db->trans_begin();
		$this->db->query($sql
								,array(
									 $roll
									 ,$order_no
									 ,$roll_seq
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
	public function removeRoll($data) {

		$roll_seq=$data['roll_seq'];
		
		$sql = "delete from roll  where roll_seq=? ";	
		
		$this->db->trans_begin();
		$this->db->query($sql
								,array(
									 $roll_seq
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
	public function listRoll() {
		
		$sql = "select * from roll order by order_no";	
	
		return $this->db->query($sql)->result();

	}

}
?>