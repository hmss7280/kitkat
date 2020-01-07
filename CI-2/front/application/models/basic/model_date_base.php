<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
|------------------------------------------------------------------------
| Author : 이현주
| Create-Date : 2014-07-10
|------------------------------------------------------------------------
*/

Class Model_date_base extends MY_Model {


	public function listDateBase($data) {

		$date_ym=$data['date_ym'];
		$sql = "SELECT *,getWeekName(DAYOFWEEK(date_ymd)) weeknm,DAYOFWEEK(date_ymd) weekno FROM date_base WHERE date_ym=?";

		return $this->db->query($sql,array(
									 $date_ym
									)
								)->result();

	}

	public function saveRedday($data) {
		$sdate=$data['sdate'];

		$sql = "select holiday_yn from date_base where date_ymd='$sdate' ";
		$holiday_yn=$this->db->query($sql)->row()->holiday_yn;
		if($holiday_yn=="Y"){
			$holiday_yn="N";
		}else{
			$holiday_yn="Y";
		}


		$sql = "update date_base set holiday_yn=? where date_ymd=? ";

		$this->db->trans_begin();
		$this->db->query($sql
								,array(
									 $holiday_yn
									 ,$sdate
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
	public function listRedday() {

		$sql = "select a.date_ymd id,'' title,a.date_ymd start,DATE_ADD(a.date_ymd,INTERVAL 1 DAY) end
				from date_base a
				where a.holiday_yn='Y'";

		return $this->db->query($sql)->result();

	}
	public function listHolidays() {

		$sql = "select GROUP_CONCAT(a.date_ymd)  holidays
				from date_base a
				where a.holiday_yn='Y'";

		return $this->db->query($sql)->row()->holidays;

	}
}
?>