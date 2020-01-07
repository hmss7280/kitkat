<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
|------------------------------------------------------------------------
| Author : 김옥훈
| Create-Date : 2014-08-27
|------------------------------------------------------------------------
*/

Class Model_Board_manage extends MY_Model {

	
	public function saveBoard_manage($data) {
		$board_name=$data['board_name'];
		$project_seq=$data['project_seq'];
		
		$sql = "insert into board_manage (project_seq,board_name,order_no,ins_date,upd_date) SELECT ?,?,count(board_seq)+1,NOW(),NOW() FROM board_manage ";	
		
		$this->db->trans_begin();
		$this->db->query($sql
								,array(
									  $project_seq
									 ,$board_name
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
	public function modifyBoard_manage($data) {
		$board_name=$data['board_name'];
		$order_no=$data['order_no'];
		$onOff=$data['onOff'];
		$useYn=$data['useYn'];
		$board_manage_seq=$data['board_manage_seq'];
		
		$sql = "update board_manage set board_name=?,order_no=?,onOff=?,useYn=?,upd_date=NOW() where board_seq=? ";	
		
		$this->db->trans_begin();
		$this->db->query($sql
								,array(
									 $board_name
									 ,$order_no
									 ,$onOff
									 ,$useYn
									 ,$board_manage_seq
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
	public function removeBoard_manage($data) {

		$board_manage_seq=$data['board_manage_seq'];
		
		$sql = "delete from board_manage  where board_seq=? ";	
		
		$this->db->trans_begin();
		$this->db->query($sql
								,array(
									 $board_manage_seq
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
	public function listBoard_manage($data) {
		$project_seq=$data['project_seq'];
		$sql = "select * from board_manage where project_seq = ".$project_seq." order by order_no";	
		return $this->db->query($sql)->result();

	}

}
?>