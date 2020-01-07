<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
|------------------------------------------------------------------------
| Author : 이현주
| Create-Date : 2014-07-10
|------------------------------------------------------------------------
*/

Class Model_work_history extends MY_Model {

	public function addWorkHistory($data) {

		$card_no=$data['card_no'];
		$work_date=$data['work_date'];

		$work_time=$data['work_time'];
		$worker=$data['worker'];
		$work_gbn=$data['work_gbn'];

		//echo $worker;
		$sql="select count(1) cnt from work_history where card_no=? and work_date=? and work_time=? and work_gbn=?";
		$cnt=$this->db->query($sql,array( $card_no
							,$work_date
							,$work_time
							,$work_gbn
							)
								)->row()->cnt;

		if($cnt==0){

			$sql = "insert into work_history (card_no,work_date,work_time,worker,work_gbn)
			values (
				?,?,?,?,?
			)
			";

			$this->db->trans_begin();
			$this->db->query($sql
									,array(
										 $card_no
										 ,$work_date
										 ,$work_time
										 ,$worker
										 ,$work_gbn
										)
									);
		}



		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			return "fail";
		}else{
			$this->db->trans_commit();
			return "success";
		}

	}
	public function modifyWorkHistory($data) {


		$pre_date=$data['pre_date'];

		$sql = "update work_history set apply_yn='Y' where  apply_yn='N' and  work_date=? ";

		$this->db->trans_begin();
		$this->db->query($sql,array($pre_date));


		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			return "fail";
		}else{
			$this->db->trans_commit();
			return "success";
		}

	}



}
?>