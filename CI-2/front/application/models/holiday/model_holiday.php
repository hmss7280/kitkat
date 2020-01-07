<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
|------------------------------------------------------------------------
| Author : 이현주
| Create-Date : 2014-07-10
|------------------------------------------------------------------------
*/

Class Model_holiday extends MY_Model {

	public function checkHoliday($data) {
		
		$date_y=$data['date_y'];
		$member_seq=$data['member_seq'];
		
		$sql = "
			select count(1) cnt from holiday where member_seq=? and date_y=?
		";	
	
		return $this->db->query($sql,array(
									 $member_seq
									 ,$date_y
									)
								)->row()->cnt;

	}


	public function addHoliday($data) {
		
		$date_y=$data['date_y'];
		$member_seq=$data['member_seq'];
		$pre_holiday=$data['pre_holiday'];
		
		
		
		$sql = "insert into holiday (member_seq,date_y,pre_holiday,ins_date,upd_date) 
		values (
			?,?,?,NOW(),NOW()																								 
		)
		";	
		
		$this->db->trans_begin();
		$this->db->query($sql
								,array(
									 $member_seq
									 ,$date_y
									 ,$pre_holiday
									 
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

	public function modifyHoliday($data) {
		
		$date_y=$data['date_y'];
		$member_seq=$data['member_seq'];
		$pre_holiday=$data['pre_holiday'];
		
		
		$sql = "update holiday set pre_holiday=?,upd_date=NOW()
			where member_seq=? and date_y=?
		";	
		
		$this->db->trans_begin();
		$this->db->query($sql
								,array(
									 $pre_holiday
									 ,$member_seq
									 ,$date_y
									 
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

}
?>