<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
|------------------------------------------------------------------------
| Author : 이현주
| Create-Date : 2014-07-10
|------------------------------------------------------------------------
*/

Class Model_duty extends MY_Model {


	public function saveDuty($data) {
		$duty=$data['duty'];

		$sql = "insert into duty (duty,order_no,ins_date,upd_date) SELECT ?,count(duty_seq)+1,NOW(),NOW() FROM duty ";

		$this->db->trans_begin();
		$this->db->query($sql
								,array(
									 $duty
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
	public function modifyDuty($data) {
		$duty=$data['duty'];
		$order_no=$data['order_no'];
		$duty_seq=$data['duty_seq'];

		$sql = "update duty set duty=?,order_no=?,upd_date=NOW() where duty_seq=? ";

		$this->db->trans_begin();
		$this->db->query($sql
								,array(
									 $duty
									 ,$order_no
									 ,$duty_seq
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
	public function removeDuty($data) {

		$duty_seq=$data['duty_seq'];

		$sql = "delete from duty  where duty_seq=? ";

		$this->db->trans_begin();
		$this->db->query($sql
								,array(
									 $duty_seq
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
	public function listDuty() {

		$sql = "select * from duty order by order_no";

		return $this->db->query($sql)->result();

	}

	public function listDutyExcept() {

		$sql = "select * from duty where duty_seq!='1' order by order_no";

		return $this->db->query($sql)->result();

	}
}
?>