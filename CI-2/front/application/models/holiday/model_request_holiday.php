<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
|------------------------------------------------------------------------
| Author : 이현주
| Create-Date : 2014-07-10
|------------------------------------------------------------------------
*/

Class Model_request_holiday extends MY_Model {



	public function saveRequestHoliday($data) {

		$member_seq=$data['member_seq'];
		$gubun=$data['gubun'];
		$hdates=$data['hdates'];
		$sdate=$data['sdate'];
		$edate=$data['edate'];
		$use_day=$data['use_day'];
		$etc_str=$data['etc_str'];
		$stime=$data['stime'];
		$etime=$data['etime'];
		$use_time=$data['use_time'];
		$reason=$data['reason'];
		$reference=$data['reference'];
		$approval=$data['approval'];



		$sql = "insert into request_holiday (member_seq,gubun,etc_str,hdates,sdate,edate,use_day,stime,etime,use_time,reason,reference,approval,ins_date,upd_date)
		values (
			?,?,?,?,?,?,?,?,?,?,?,?,?,NOW(),NOW()
		)
		";

		$this->db->trans_begin();
		$this->db->query($sql
								,array(
									 $member_seq
									 ,$gubun
									 ,$etc_str
									 ,$hdates
									 ,$sdate
									 ,$edate
									 ,$use_day
									 ,$stime
									 ,$etime
									 ,$use_time
									 ,$reason
									 ,$reference
									 ,$approval
									)
								);


		$request_holiday_seq = $this->db->insert_id();

		if($this->db->trans_status() === FALSE){
			$this->db->trans_categoryback();
			return 0;
		}else{
			$this->db->trans_commit();
			return $request_holiday_seq;
		}

	}

	public function saveApproveHoliday_pm($data) {

		$request_holiday_seq=$data['request_holiday_seq'];
		$pm_member_seqs=$data['pm_member_seqs'];
		$member_seq=$data['member_seq'];
		$gubun="1";


		$this->db->trans_begin();

		for($i=0;$i<count($pm_member_seqs);$i++){

			if($pm_member_seqs[$i]!=""){

				$sql = "insert into approve_holiday (request_holiday_seq,member_seq,gubun,r_member_seq,ins_date,upd_date)
				values (
					?,?,?,?,NOW(),NOW()
				)
				";


				$this->db->query($sql
										,array(
											 $request_holiday_seq
											 ,$pm_member_seqs[$i]
											 ,$gubun
											 ,$member_seq
											)
										);
			}
		}

		if($this->db->trans_status() === FALSE){
			$this->db->trans_categoryback();
			return 0;
		}else{
			$this->db->trans_commit();
			return 1;
		}

	}

	public function saveApproveHoliday_duty($data) {

		$request_holiday_seq=$data['request_holiday_seq'];
		$duty_member_seq=$data['duty_member_seq'];
		$member_seq=$data['member_seq'];
		$gubun="2";


		$this->db->trans_begin();

		$sql = "insert into approve_holiday (request_holiday_seq,member_seq,gubun,r_member_seq,ins_date,upd_date)
		values (
			?,?,?,?,NOW(),NOW()
		)
		";


		$this->db->query($sql
								,array(
									 $request_holiday_seq
									 ,$duty_member_seq
									 ,$gubun
									 ,$member_seq
									)
								);

		if($this->db->trans_status() === FALSE){
			$this->db->trans_categoryback();
			return 0;
		}else{
			$this->db->trans_commit();
			return 1;
		}

	}


	public function listHoliday($data) {

		$member_seq=$data['member_seq'];

		$gubun=$data['gubun'];
		$page_num=(int)$data['page_num'];
		$page_size=(int)$data['page_size'];
		$page_no=($page_num-1)*$page_size;


		$sql = "SELECT a.*,b.member_name
				,(SELECT GROUP_CONCAT(DISTINCT  n.member_name) FROM approve_holiday m JOIN member n ON m.member_seq=n.member_seq WHERE m.request_holiday_seq=a.request_holiday_seq) approves
				FROM request_holiday a
				JOIN member b ON a.member_seq=b.member_seq ";
		$sql .="where 0=0";

		if($gubun=="1"){
			$sql .=" and a.request_holiday_seq IN (
						SELECT request_holiday_seq FROM approve_holiday WHERE r_member_seq='$member_seq'
					)  ";
		}else if($gubun=="2"){
			$sql .=" and a.request_holiday_seq IN (
						SELECT request_holiday_seq FROM approve_holiday WHERE member_seq='$member_seq'
					)  ";
		}
		$sql .=" order by a.approval ,a.upd_date desc limit ?,?";
		//echo $sql;
		return $this->db->query($sql
									,array(
										   	$page_no
											,$page_size
											)
								)->result();

	}

	public function listHoliday_count($data) {

		$member_seq=$data['member_seq'];

		$gubun=$data['gubun'];


		$sql = "SELECT count(1) cnt
				FROM request_holiday a
				JOIN member b ON a.member_seq=b.member_seq ";
		$sql .="where 0=0";

		if($gubun=="1"){
			$sql .=" and a.request_holiday_seq IN (
						SELECT request_holiday_seq FROM approve_holiday WHERE r_member_seq='$member_seq'
					)  ";
		}else if($gubun=="2"){
			$sql .=" and a.request_holiday_seq IN (
						SELECT request_holiday_seq FROM approve_holiday WHERE member_seq='$member_seq'
					)  ";
		}


		return $this->db->query($sql)->row()->cnt;

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
			$this->db->trans_categoryback();
			return "fail";
		}else{
			$this->db->trans_commit();
			return "success";
		}

	}

	public function view_request_holiday($data) {

		$member_seq=$data['member_seq'];
		$request_holiday_seq=$data['request_holiday_seq'];


		$sql = "SELECT a.*,b.member_name
				,(SELECT n.member_name FROM approve_holiday m JOIN  member n ON m.member_seq=n.member_seq WHERE m.request_holiday_seq=a.request_holiday_seq AND m.gubun='2') duty_member_name
				,(SELECT m.status FROM approve_holiday m JOIN  member n ON m.member_seq=n.member_seq WHERE m.request_holiday_seq=a.request_holiday_seq AND m.gubun='2') duty_status
				,(SELECT m.member_seq FROM approve_holiday m WHERE m.request_holiday_seq=a.request_holiday_seq AND m.gubun='2') duty_member_seq
				,(SELECT m.etc FROM approve_holiday m WHERE m.request_holiday_seq=a.request_holiday_seq AND m.gubun='2') duty_etc
				FROM request_holiday a
				JOIN member b ON a.member_seq=b.member_seq
				WHERE a.request_holiday_seq='$request_holiday_seq' ";


		//echo $sql;
		return $this->db->query($sql)->row();

	}
	public function view_approve_holiday($data) {

		$member_seq=$data['member_seq'];
		$request_holiday_seq=$data['request_holiday_seq'];


		$sql = "SELECT *
				FROM approve_holiday
				WHERE request_holiday_seq='$request_holiday_seq' and member_seq='$member_seq'";



		return $this->db->query($sql)->result();

	}

	public function view_approve_pm_holiday($data) {

		$member_seq=$data['member_seq'];
		$request_holiday_seq=$data['request_holiday_seq'];


		$sql = "SELECT distinct a.request_holiday_seq,a.r_member_seq,a.gubun,a.etc,a.member_seq,a.status,b.member_name,d.project
				FROM approve_holiday a
				JOIN member b ON a.member_seq=b.member_seq
				JOIN project_member c ON a.member_seq=c.member_seq
				JOIN project d ON c.project_seq=d.project_seq
				WHERE request_holiday_seq='$request_holiday_seq' AND roll_seq='1'  AND a.gubun!='2' AND a.gubun!='3' ";




		return $this->db->query($sql)->result();

	}

	public function view_approve_coo_holiday($data) {

		$member_seq=$data['member_seq'];
		$request_holiday_seq=$data['request_holiday_seq'];


		$sql = "SELECT a.*
		FROM approve_holiday a
		WHERE request_holiday_seq='$request_holiday_seq' AND a.gubun='3' ";

		return $this->db->query($sql)->result();

	}

	public function setApprovalHoliday($data) {


		$member_seq=$data['member_seq'];
		$request_holiday_seq=$data['request_holiday_seq'];
		$status=$data['status'];
		$gubun=$data['gubun'];
		$etc=$data['etc'];


		if($gubun!="3"){//경영기획실이 아니면
			$sql = "update approve_holiday set status=?,etc=?,upd_date=NOW()
				where member_seq=? and request_holiday_seq=? and gubun=?
			";


			$this->db->trans_begin();
			$this->db->query($sql
									,array(
										 $status
										 ,$etc
										 ,$member_seq
										 ,$request_holiday_seq
										 ,$gubun
										)
									);
		}else{

			$r_member_seq=$data['r_member_seq'];

			$sql = "insert into approve_holiday (status,etc,member_seq,request_holiday_seq,gubun,r_member_seq,ins_date,upd_date ) values
				(?,?,?,?,?,?,NOW(),NOW())
			";


			$this->db->trans_begin();
			$this->db->query($sql
									,array(
										 $status
										 ,$etc
										 ,$member_seq
										 ,$request_holiday_seq
										 ,$gubun
										 ,$r_member_seq
										)
									);

		}

		if($this->db->trans_status() === FALSE){
			$this->db->trans_categoryback();
			return "fail";
		}else{
			$this->db->trans_commit();
			return "success";
		}

	}

	public function cancelRequestHoliday($data) {


		$member_seq=$data['member_seq'];
		$request_holiday_seq=$data['request_holiday_seq'];

		$this->db->trans_begin();

		$sql = "delete from approve_holiday where r_member_seq=? and request_holiday_seq=?
		";

		$this->db->query($sql
								,array(
									 $member_seq
									 ,$request_holiday_seq
									)
								);

		$sql = "delete from request_holiday where member_seq=? and request_holiday_seq=?
		";


		$this->db->query($sql
								,array(
									 $member_seq
									 ,$request_holiday_seq
									)
								);


		if($this->db->trans_status() === FALSE){
			$this->db->trans_categoryback();
			return "fail";
		}else{
			$this->db->trans_commit();
			return "success";
		}

	}

	public function cancelApproveRequestHoliday($data) {


		$request_holiday_seq=$data['request_holiday_seq'];

		$this->db->trans_begin();

		$sql = "delete from approve_holiday where  request_holiday_seq=?
		";

		$this->db->query($sql
								,array(
									 $request_holiday_seq
									)
								);

		$sql = "delete from request_holiday where request_holiday_seq=?
		";


		$this->db->query($sql
								,array(
									 $request_holiday_seq
									)
								);

		$sql = "delete from goleave where request_holiday_seq=?
		";


		$this->db->query($sql
								,array(
									 $request_holiday_seq
									)
								);


		if($this->db->trans_status() === FALSE){
			$this->db->trans_categoryback();
			return "fail";
		}else{
			$this->db->trans_commit();
			return "success";
		}

	}


	public function check_pm_holiday($data) {

		$request_holiday_seq=$data['request_holiday_seq'];


		$sql = "SELECT (SELECT COUNT(1) FROM approve_holiday WHERE request_holiday_seq='$request_holiday_seq' AND gubun!='2')-(SELECT COUNT(1) FROM approve_holiday WHERE request_holiday_seq='$request_holiday_seq' AND STATUS='A') cnt ";



		return $this->db->query($sql)->row()->cnt;

	}

	public function modifyRequestApprovalHoliday($data) {


		$approval=$data['approval'];
		$request_holiday_seq=$data['request_holiday_seq'];

		$sql = "update request_holiday set approval=?,upd_date=NOW()
			where request_holiday_seq=?
		";


		$this->db->trans_begin();
		$this->db->query($sql
								,array(
									 $approval
									 ,$request_holiday_seq
									)
								);


		if($this->db->trans_status() === FALSE){
			$this->db->trans_categoryback();
			return "fail";
		}else{
			$this->db->trans_commit();
			return "success";
		}

	}


	public function selectHoliday($data) {

		$request_holiday_seq=$data['request_holiday_seq'];


		$sql = "select
				member_seq
				,CASE WHEN gubun='1' OR  gubun='2' OR  gubun='3' OR  gubun='4' THEN hdates
				ELSE sdate
				END hdates
				,use_day
				,gubun
				FROM request_holiday where request_holiday_seq='$request_holiday_seq' ";

		return $this->db->query($sql)->row();

	}

	public function approve_cnt($data) {

		$member_seq=$data['member_seq'];



		$sql = "SELECT COUNT(1) cnt FROM approve_holiday WHERE member_seq='$member_seq' AND status='N' ";

		return $this->db->query($sql)->row()->cnt;

	}
	public function workingHoliday($data) {

		$start=$data['start'];
		$end=$data['end'];


		$sql = "select
				request_holiday_seq id
				,CONCAT('[',duty,'] ', member_name,' ',degree,' ',gubun) title,sdate start,DATE_ADD(edate,INTERVAL 1 DAY) end
				FROM (
					SELECT a.request_holiday_seq,a.sdate
						,CASE WHEN a.edate='' THEN a.sdate
						ELSE a.edate
						END edate
						,CASE WHEN a.hdates='' THEN a.sdate
						ELSE a.hdates
						END hdates
						,b.member_name
						,c.degree
						,d.duty
						,a.stime
						,a.etime
						,CASE WHEN a.gubun='1' THEN '연차휴가'
						WHEN a.gubun='2' THEN '대체휴가'
						WHEN a.gubun='3' THEN '예비군/민방위훈련'
						WHEN a.gubun='4' THEN CONCAT(' (',etc_str,')')
						WHEN a.gubun='5' THEN CONCAT('조퇴 (',a.stime,' ~ )')
						WHEN a.gubun='6' THEN CONCAT('반일근무 ( ~ ',a.stime,')')
						WHEN a.gubun='7' THEN CONCAT('외출 (',a.stime,' ~ ',a.etime,')')
						END gubun

						FROM `request_holiday` a
						JOIN member b ON a.member_seq=b.member_seq
						LEFT JOIN degree c ON b.degree_seq=c.degree_seq
						LEFT JOIN duty d ON b.duty_seq=d.duty_seq
						WHERE a.approval='3'

					) tm WHERE
						not (sdate>'$end' or edate<'$start' )
			";


			//echo $sql;

		return $this->db->query($sql)->result();

	}


}
?>