<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
|------------------------------------------------------------------------
| Author : 이현주
| Create-Date : 2014-07-10
|------------------------------------------------------------------------
*/

Class Model_step extends MY_Model {

	
	public function saveStep($data) {
		$step=$data['step'];
		
		$sql = "insert into step (step,order_no,ins_date,upd_date) SELECT ?,count(step_seq)+1,NOW(),NOW() FROM step ";	
		
		$this->db->trans_begin();
		$this->db->query($sql
								,array(
									 $step
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
	public function modifyStep($data) {
		$step=$data['step'];
		$order_no=$data['order_no'];
		$step_seq=$data['step_seq'];
		
		$sql = "update step set step=?,order_no=?,upd_date=NOW() where step_seq=? ";	
		
		$this->db->trans_begin();
		$this->db->query($sql
								,array(
									 $step
									 ,$order_no
									 ,$step_seq
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
	public function removeStep($data) {

		$step_seq=$data['step_seq'];
		
		$sql = "delete from step  where step_seq=? ";	
		
		$this->db->trans_begin();
		$this->db->query($sql
								,array(
									 $step_seq
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
	public function listStep() {
		
		$sql = "select * from step order by order_no";	
	
		return $this->db->query($sql)->result();

	}

}
?>