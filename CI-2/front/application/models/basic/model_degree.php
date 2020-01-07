<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
|------------------------------------------------------------------------
| Author : 이현주
| Create-Date : 2014-07-10
|------------------------------------------------------------------------
*/

Class Model_degree extends MY_Model {

	
	public function saveDegree($data) {
		$degree=$data['degree'];
		
		$sql = "insert into degree (degree,order_no,ins_date,upd_date) SELECT ?,count(degree_seq)+1,NOW(),NOW() FROM degree ";	
		
		$this->db->trans_begin();
		$this->db->query($sql
								,array(
									 $degree
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
	public function modifyDegree($data) {
		//$board_manage=$data['board_manage'];
		//$order_no=$data['order_no'];
		//$board_manage_seq=$data['board_manage_seq'];
		
		
		$degree= $data['degree'];
		$order_no = $data['order_no'];
		$degree_seq = $data['degree_seq'];
		
		//$sql = "update board_manage set board_name=?,order_no=?,upd_date=NOW() where board_seq=? ";	
		$sql = "update degree set degree=?,order_no=?,upd_date=NOW() where degree_seq=? ";	
		
		$this->db->trans_begin();
		$this->db->query($sql
								,array(
									 $degree
									 ,$order_no
									 ,$degree_seq
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
	public function removeDegree($data) {

		$degree_seq=$data['degree_seq'];
		
		$sql = "delete from degree  where degree_seq=? ";	
		
		$this->db->trans_begin();
		$this->db->query($sql
								,array(
									 $degree_seq
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
	public function listDegree() {
		
		$sql = "select * from degree order by order_no";	
	
		return $this->db->query($sql)->result();

	}

}
?>