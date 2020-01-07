<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
|------------------------------------------------------------------------
| Author : 이현주
| Create-Date : 2014-07-10
|------------------------------------------------------------------------
*/

Class Model_goleave extends MY_Model {


	public function listGoleave($data) {

		$date_ym=$data['date_ym'];
		$duty_seq=$data['duty_seq'];

		$sql = "
			SELECT b.*
			,
			CASE WHEN b.go_time is null or b.go_time='' or b.leave_time is null or b.leave_time='' then ''
			else
				substring(SEC_TO_TIME(TIME_TO_SEC(CONCAT('1230','00'))-TIME_TO_SEC(CONCAT(b.go_time,'00'))+TIME_TO_SEC(CONCAT(b.leave_time,'00'))-TIME_TO_SEC(CONCAT('1330','00'))),1,5)
			end work_time
			,CASE WHEN b.go_time>'0930' THEN
				SUBSTRING(SEC_TO_TIME(TIME_TO_SEC(CONCAT(b.go_time,'00'))-TIME_TO_SEC('093000')),1,5)
			ELSE
				''
			END	 late_time
			FROM member a
			LEFT JOIN goleave b ON a.member_seq=b.member_seq AND SUBSTRING(b.t_date,1,7)=?
			WHERE a.del_yn='N'
			AND a.nop_yn='Y'
			AND a.work_yn='Y'
			AND a.duty_seq>1  ";

		if($duty_seq!="0"){
			$sql .= "	AND a.duty_seq='$duty_seq' ";
		}
		//echo $sql;
		return $this->db->query($sql,array(
									 $date_ym

									)
								)->result();

	}
	public function listGoleaveMember($data) {

		$date_ym=$data['date_ym'];
		$duty_seq=$data['duty_seq'];
		$member_seq=$data['member_seq'];

		$sql = "
			SELECT b.*
			,
			CASE WHEN b.go_time is null or b.go_time='' or b.leave_time is null or b.leave_time='' then ''
			else
				substring(SEC_TO_TIME(TIME_TO_SEC(CONCAT('1230','00'))-TIME_TO_SEC(CONCAT(b.go_time,'00'))+TIME_TO_SEC(CONCAT(b.leave_time,'00'))-TIME_TO_SEC(CONCAT('1330','00'))),1,5)
			end work_time
			,CASE WHEN b.go_time>'0930' THEN
				SUBSTRING(SEC_TO_TIME(TIME_TO_SEC(CONCAT(b.go_time,'00'))-TIME_TO_SEC('093000')),1,5)
			ELSE
				''
			END	 late_time
			FROM member a
			LEFT JOIN goleave b ON a.member_seq=b.member_seq AND SUBSTRING(b.t_date,1,7)=?
			WHERE a.del_yn='N'
			AND a.nop_yn='Y'
			AND a.work_yn='Y'
			AND a.duty_seq>1  ";

		if($duty_seq!="0"){
			$sql .= "	AND a.duty_seq='$duty_seq' ";
		}
		if($member_seq!=""){
			$sql .= "	AND a.member_seq='$member_seq' ";
		}
		//echo $sql;
		return $this->db->query($sql,array(
									 $date_ym

									)
								)->result();

	}

	public function goleave_month_sum($data) {

		$date_ym=$data['date_ym'];
		$duty_seq=$data['duty_seq'];

		$sql = "
		SELECT member_seq,SEC_TO_TIME(SUM(work_time_sec)) work_time_sum ,SEC_TO_TIME(SUM(late_time_sec)) late_time_sum
		FROM (
			SELECT b.*
			,
			CASE WHEN b.go_time is null or b.go_time='' or b.leave_time is null or b.leave_time='' then ''
			else
				substring(SEC_TO_TIME(TIME_TO_SEC(CONCAT('1230','00'))-TIME_TO_SEC(CONCAT(b.go_time,'00'))+TIME_TO_SEC(CONCAT(b.leave_time,'00'))-TIME_TO_SEC(CONCAT('1330','00'))),1,5)
			end work_time
			,CASE WHEN b.go_time>'0930' THEN
				SUBSTRING(SEC_TO_TIME(TIME_TO_SEC(CONCAT(b.go_time,'00'))-TIME_TO_SEC('093000')),1,5)
			ELSE
				''
			END	 late_time
			,
			CASE WHEN b.go_time IS NULL OR b.go_time='' OR b.leave_time IS NULL OR b.leave_time='' THEN
				''
			ELSE
				TIME_TO_SEC(CONCAT('1230','00'))-TIME_TO_SEC(CONCAT(b.go_time,'00'))+TIME_TO_SEC(CONCAT(b.leave_time,'00'))-TIME_TO_SEC(CONCAT('1330','00'))
			END work_time_sec
			,CASE WHEN b.go_time>'0930' THEN
				TIME_TO_SEC(CONCAT(b.go_time,'00'))-TIME_TO_SEC('093000')
			ELSE
				''
			END late_time_sec

			FROM member a
			LEFT JOIN goleave b ON a.member_seq=b.member_seq AND SUBSTRING(b.t_date,1,7)=?
			WHERE a.del_yn='N'
			AND a.nop_yn='Y'
			AND a.work_yn='Y'
			AND a.duty_seq>1  ";

		if($duty_seq!="0"){
			$sql .= "	AND a.duty_seq='$duty_seq' ";
		}
		$sql .= ") a GROUP BY member_seq ";
		return $this->db->query($sql,array(
									 $date_ym

									)
								)->result();

	}

	public function goleave_sum($data) {

		$date_ym=$data['date_ym'];
		$duty_seq=$data['duty_seq'];

		$sql = "
		SELECT b.*
			FROM member a
			LEFT JOIN goleave_sum b ON a.member_seq=b.member_seq  AND b.date_ym=?
			WHERE a.del_yn='N'
			AND a.nop_yn='Y'
			AND a.work_yn='Y'
			AND a.duty_seq>1  ";

		if($duty_seq!="0"){
			$sql .= "	AND a.duty_seq='$duty_seq' ";
		}

		return $this->db->query($sql,array(
									 $date_ym

									)
								)->result();

	}


	public function checkGoleave($data) {

		$t_date=$data['t_date'];
		$member_seq=$data['member_seq'];

		$sql = "
			select count(1) cnt from goleave where member_seq=? and t_date=?
		";

		return $this->db->query($sql,array(
									 $member_seq
									 ,$t_date
									)
								)->row()->cnt;

	}

	public function checkGoleaveSum($data) {

		$date_ym=$data['date_ym'];
		$member_seq=$data['member_seq'];

		$sql = "
			select count(1) cnt from goleave_sum where member_seq=? and date_ym=?
		";

		return $this->db->query($sql,array(
									 $member_seq
									 ,$date_ym
									)
								)->row()->cnt;

	}

	public function addGoleave($data) {

		$t_date=$data['t_date'];
		$member_seq=$data['member_seq'];

		$go_time=$data['go_time'];
		$leave_time=$data['leave_time'];
		$use_holiday=$data['use_holiday'];
		$etc=$data['etc'];


		$sql = "insert into goleave (member_seq,t_date,go_time,leave_time,use_holiday,etc,ins_date,upd_date)
		values (
			?,?,?,?,?,?,NOW(),NOW()
		)
		";

		$this->db->trans_begin();
		$this->db->query($sql
								,array(
									 $member_seq
									 ,$t_date
									 ,$go_time
									 ,$leave_time
									 ,$use_holiday
									 ,$etc
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

	public function modifyGoleave($data) {

		$t_date=$data['t_date'];
		$member_seq=$data['member_seq'];

		$go_time=$data['go_time'];
		$leave_time=$data['leave_time'];
		$use_holiday=$data['use_holiday'];
		$etc=$data['etc'];


		$sql = "update goleave set go_time=?,leave_time=?,use_holiday=?,etc=?,upd_date=NOW()
			where member_seq=? and t_date=?
		";

		$this->db->trans_begin();
		$this->db->query($sql
								,array(
									 $go_time
									 ,$leave_time
									 ,$use_holiday
									 ,$etc
									 ,$member_seq
									 ,$t_date
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


public function addGoleave_sum($data) {

		$date_ym=$data['date_ym'];
		$member_seq=$data['member_seq'];

		$late_holiday=$data['late_holiday'];


		$sql = "insert into goleave_sum (member_seq,date_ym,late_holiday,ins_date,upd_date)
		values (
			?,?,?,NOW(),NOW()
		)
		";

		$this->db->trans_begin();
		$this->db->query($sql
								,array(
									 $member_seq
									 ,$date_ym
									 ,$late_holiday
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

	public function modifyGoleave_sum($data) {

		$date_ym=$data['date_ym'];
		$member_seq=$data['member_seq'];

		$late_holiday=$data['late_holiday'];


		$sql = "update goleave_sum set late_holiday=?,upd_date=NOW()
			where member_seq=? and date_ym=?
		";

		$this->db->trans_begin();
		$this->db->query($sql
								,array(
									 $late_holiday
									 ,$member_seq
									 ,$date_ym
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


	public function addHolidayGoleave($data) {

		$t_date=$data['t_date'];
		$member_seq=$data['member_seq'];
		$request_holiday_seq=$data['request_holiday_seq'];

		$use_holiday=$data['use_holiday'];



		$sql = "insert into goleave (member_seq,t_date,use_holiday,request_holiday_seq,ins_date,upd_date)
		values (
			?,?,?,?,NOW(),NOW()
		)
		";

		$this->db->trans_begin();
		$this->db->query($sql
								,array(
									 $member_seq
									 ,$t_date
									 ,$use_holiday
									 ,$request_holiday_seq
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

	public function modifyHolidayGoleave($data) {

		$t_date=$data['t_date'];
		$member_seq=$data['member_seq'];

		$use_holiday=$data['use_holiday'];


		$sql = "update goleave set use_holiday=?,upd_date=NOW()
			where member_seq=? and t_date=?
		";

		$this->db->trans_begin();
		$this->db->query($sql
								,array(
									 $use_holiday
									 ,$member_seq
									 ,$t_date
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

	public function addGoTimeAuto($data) {

		$pre_date=str_replace("-","",$data['pre_date']);

		/*
		$sql = "replace into goleave (member_seq,t_date,go_time,leave_time,ins_date,upd_date)

			SELECT b.member_seq,DATE_FORMAT(a.work_date,'%Y-%m-%d') t_date,SUBSTR(a.work_time,1,4) work_stime
			,(SELECT leave_time FROM goleave WHERE t_date=DATE_FORMAT(a.work_date,'%Y-%m-%d') AND member_seq=b.member_seq)	work_etime
			,now(),now()
			FROM work_history a
			JOIN member b ON a.card_no=b.card_no
			WHERE a.work_gbn='1' and a.work_date=? and a.apply_yn='N'
		";
		*/
		$sql = "replace into goleave (member_seq,t_date,go_time,leave_time,use_holiday,etc,ins_date,upd_date)

			SELECT *
			,(SELECT leave_time FROM goleave WHERE t_date=t.t_date AND member_seq=t.member_seq)	work_etime
			,(SELECT use_holiday FROM goleave WHERE t_date=t.t_date AND member_seq=t.member_seq)	use_holiday
			,(SELECT etc FROM goleave WHERE t_date=t.t_date AND member_seq=t.member_seq)	etc
			,NOW(),NOW()
			FROM (
				SELECT b.member_seq,DATE_FORMAT(a.work_date,'%Y-%m-%d') t_date,SUBSTR(MIN(a.work_time),1,4) work_stime

							FROM work_history a
							JOIN member b ON a.card_no=b.card_no
							WHERE a.work_gbn='1' AND a.work_date=? and a.apply_yn='N'
							GROUP BY b.member_seq,a.work_date
				) t
		";


		$this->db->trans_begin();
		$this->db->query($sql
								,array(
									$pre_date
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
	public function addLeaveTimeAuto($data) {

		$pre_date=str_replace("-","",$data['pre_date']);

		/*
		$sql = "replace into goleave (member_seq,t_date,go_time,leave_time,ins_date,upd_date)

			SELECT b.member_seq
			,DATE_FORMAT(a.work_date,'%Y-%m-%d') t_date
			,(SELECT go_time FROM goleave WHERE t_date=DATE_FORMAT(a.work_date,'%Y-%m-%d') AND member_seq=b.member_seq) work_stime
			,SUBSTR(a.work_time,1,4) work_etime
			,now(),now()
			FROM work_history a
			JOIN member b ON a.card_no=b.card_no
			WHERE a.work_gbn='4'  and a.work_date=? and a.apply_yn='N'
		";
		*/
		$sql = "replace into goleave (member_seq,t_date,go_time,leave_time,use_holiday,etc,ins_date,upd_date)

			SELECT  t.member_seq,t.t_date
			,(SELECT go_time FROM goleave WHERE t_date=t.t_date AND member_seq=t.member_seq) work_stime
			,t.work_etime
			,(SELECT use_holiday FROM goleave WHERE t_date=t.t_date AND member_seq=t.member_seq) use_holiday
			,(SELECT etc FROM goleave WHERE t_date=t.t_date AND member_seq=t.member_seq) etc
			,NOW(),NOW()
			FROM (
				SELECT b.member_seq,DATE_FORMAT(a.work_date,'%Y-%m-%d') t_date,SUBSTR(MAX(a.work_time),1,4) work_etime

							FROM work_history a
							JOIN member b ON a.card_no=b.card_no
							WHERE a.work_gbn='4' AND a.work_date=? and a.apply_yn='N'
							GROUP BY b.member_seq,a.work_date
				) t
		";



		$this->db->trans_begin();
		$this->db->query($sql
								,array(
									$pre_date
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