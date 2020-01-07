<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
|------------------------------------------------------------------------
| Author : 이현주
| Create-Date : 2014-07-10
|------------------------------------------------------------------------
*/

Class Model_project extends MY_Model {


	public function saveProject($data) {

		$project=$data['project'];
		$client_seq=$data['client_seq'];
		$member_seq=$data['member_seq'];
		$content=$data['content'];
		$sdate=$data['sdate'];
		$edate=$data['edate'];
		$step_seq=$data['step_seq'];


		$this->db->trans_begin();

		$sql = "insert into project (project,content,member_seq,client_seq,step_seq,sdate,edate,ins_date,upd_date)
				values (?,?,?,?,?,?,?,NOW(),NOW())
		";

		$this->db->query($sql
								,array(
									 $project
									 ,$content
									 ,$member_seq
									 ,$client_seq
									 ,$step_seq
									 ,$sdate
									 ,$edate
									)
								);

		$project_seq=$this->db->insert_id();

		$sql = "insert into project_member (project_seq,member_seq,roll_seq,ins_date,upd_date)
				values (?,?,0,NOW(),NOW())
			";

			$this->db->query($sql
							,array(
								 $project_seq
								 ,$member_seq
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
	public function modifyProject($data) {

		$project_seq=$data['project_seq'];
		$project=$data['project'];
		$client_seq=$data['client_seq'];
		$member_seq=$data['member_seq'];
		$old_member_seq=$data['old_member_seq'];
		$content=$data['content'];
		$sdate=$data['sdate'];
		$edate=$data['edate'];
		$step_seq=$data['step_seq'];



		$sql="select count(*) cnt from project_member where project_seq=? and member_seq=?";
		$cnt=$this->db->query($sql
								 ,array(
										$project_seq
										,$member_seq
										)
								 )->row()->cnt;


		$this->db->trans_begin();

		$sql = "update project set project=?,client_seq=?,step_seq=?,member_seq=?,content=?,sdate=?,edate=?,upd_date=NOW() where project_seq=?
		";

		$this->db->query($sql
								,array(
									 $project
									 ,$client_seq
									 ,$step_seq
									 ,$member_seq
									 ,$content
									 ,$sdate
									 ,$edate
									 ,$project_seq
									)
								);

		if($old_member_seq!=$member_seq){
			$sql = "delete from project_member where project_seq=? and member_seq=?
			";
			$this->db->query($sql
								,array(
									 $project_seq
									 ,$old_member_seq
									)
								);
		}


		if($cnt==0){
			$sql = "insert into project_member (project_seq,member_seq,roll_seq,ins_date,upd_date)
					values (?,?,0,NOW(),NOW())
				";

				$this->db->query($sql
								,array(
									 $project_seq
									 ,$member_seq
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

	public function listProject_count($data) {

		$project=$data['project'];
		$client_seq=$data['client_seq'];
		$member_seq=$data['member_seq'];

		$sql = "select count(*) cnt
				from project a
				where 0=0 and a.del_yn='N'";

		if($project!=""){
			$sql .=" and a.project like '%$project%'";
		}
		if($client_seq!=""){
			$sql .=" and a.client_seq='$client_seq' ";
		}

		if($this->auth_seq>2 || $this->auth_seq==NULL){//Master이상이면 모든 프로젝트 목록 확인 가능
			$sql .=" and a.project_seq in (select project_seq from project_member where member_seq='$member_seq') ";
		}
		return $this->db->query($sql)->row()->cnt;

	}
	public function listProject($data) {

		$project=$data['project'];
		$client_seq=$data['client_seq'];
		$member_seq=$data['member_seq'];

		$page_num=(int)$data['page_num'];
		$page_size=(int)$data['page_size'];
		$page_no=($page_num-1)*$page_size;
		$key =CKEY;

		$sql = "select a.*,
				AES_DECRYPT(UNHEX(b.cel), MD5('$key'))as cel,
				AES_DECRYPT(UNHEX(b.tel), MD5('$key'))as tel,
				AES_DECRYPT(UNHEX(b.email), MD5('$key'))as email,
				b.member_id,b.member_name,c.company,d.step,
				(SELECT board_seq FROM board_manage WHERE project_seq =a.project_seq AND useYn='Y' ORDER BY order_no ASC LIMIT 1) AS board_seq
				from project a
				left join member b on a.member_seq=b.member_seq
				left join client c on a.client_seq=c.client_seq
				left join step d on a.step_seq=d.step_seq
				where 0=0 and a.del_yn='N' ";
		if($project!=""){
			$sql .=" and a.project like '%$project%'";
		}
		if($client_seq!=""){
			$sql .=" and a.client_seq='$client_seq' ";
		}
		if($this->auth_seq>2 || $this->auth_seq==NULL){//Master이상이면 모든 프로젝트 목록 확인 가능
			$sql .=" and a.project_seq in (select project_seq from project_member where member_seq='$member_seq') ";
		}
		$sql .="  order by a.edate desc,ins_date desc limit ?,?";

		return $this->db->query($sql
									,array(
										   	$page_no
											,$page_size
											)
								)->result();

	}

	public function viewProject($data) {
		$project_seq=$data['project_seq'];

		$sql = "select a.*
				from project a
				where 0=0 and a.project_seq=? ";

		return $this->db->query($sql
									,array(
										   	$project_seq
											)
								)->row();

	}


	public function removeProject($data) {

		$project_seq=$data['project_seq'];


		$this->db->trans_begin();


		$sql = "update project set del_yn='Y' where project_seq=? ";
		$this->db->query($sql
								,array(
									 $project_seq
									)
								);
		/*
		$sql="delete from project_member where project_seq=?";
		$this->db->query($sql
								,array(
									 $project_seq
									)
								);

		$sql = "delete from project where project_seq=? ";
		$this->db->query($sql
								,array(
									 $project_seq
									)
								);


		*/

		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			return "fail";
		}else{
			$this->db->trans_commit();
			return "success";
		}

	}

	public function changeProject($data) {

		$project_seq=$data['project_seq'];
		$finish_yn=$data['finish_yn'];


		$this->db->trans_begin();


		$sql = "update project set finish_yn=? where project_seq=? ";
		$this->db->query($sql
								,array(
									 $finish_yn
									 ,$project_seq
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

	public function checkProject($data) {
		$project_seq=$data['project_seq'];
		$member_seq=$data['member_seq'];



		if($this->auth_seq>2 || $this->auth_seq==NULL){
			$sql = "select count(*) cnt
					from project_member a
					where 0=0 and a.project_seq=? and a.member_seq=? ";

			$cnt=$this->db->query($sql
										,array(
												$project_seq
												,$member_seq
												)
									)->row()->cnt;
		}else{
			$sql = "select count(*) cnt
					from project_member a
					where 0=0 and a.project_seq=? ";

			$cnt= $this->db->query($sql
										,array(
												$project_seq
												)
									)->row()->cnt;
		}
		if($this->auth_seq<="2"){
			$cnt=1;
		}

		return $cnt;
	}

	public function involveProject($data){

		$project_seq=$data['project_seq'];
		$client_dp_yn=$data['client_dp_yn'];



		$key =CKEY;
		$sql = "SELECT a.*,b.*,
				AES_DECRYPT(UNHEX(b.cel), MD5('$key'))as cel,
				AES_DECRYPT(UNHEX(b.tel), MD5('$key'))as tel,
				AES_DECRYPT(UNHEX(b.email), MD5('$key'))as email,
				c.roll,ifnull(e.company,'".HNAME."') company
				FROM project_member a
				JOIN member b ON a.member_seq=b.member_seq
				left JOIN roll c ON a.roll_seq=c.roll_seq
				JOIN project d ON a.project_seq=d.project_seq
				left join client e on b.client_seq=e.client_seq
				WHERE a.project_seq=? ";
		if($client_dp_yn=="N"){
			$sql .= " and b.nop_yn='Y' ";
		}
		$sql .= " order by a.roll_seq";

		//echo $sql;
		//exit;

		return $this->db->query($sql
									,array(
										   	$project_seq
											)
								)->result();


	}
	public function poolProject($data){

		$company=$data['company'];
		$project_seq=$data['project_seq'];
		$client_seq=$data['client_seq'];
		$duty_seq=$data['duty_seq'];
		$dutyWhere="";
		$key =CKEY;


		if($company==CNAME){
			if($duty_seq==""){
				$dutyWhere="";
			}else{
				$dutyWhere="WHERE tm.duty_seq =".$duty_seq;
			}
			$where="(
					SELECT a.*,
					AES_DECRYPT(UNHEX(a.cel), MD5('$key'))as member_cel,
					AES_DECRYPT(UNHEX(a.tel), MD5('$key'))as member_tel,
					AES_DECRYPT(UNHEX(a.email), MD5('$key'))as member_email,
					b.project_member_seq,b.roll_seq,b.project_seq
					FROM member a
					LEFT JOIN project_member b ON a.member_seq=b.member_seq AND b.project_seq=?
					WHERE a.nop_yn='Y' AND a.del_yn='N' AND  a.work_yn='Y' AND b.project_member_seq IS NULL
				   )";
		}else{
			$where="(
					SELECT a.*,
					AES_DECRYPT(UNHEX(a.cel), MD5('$key'))as member_cel,
					AES_DECRYPT(UNHEX(a.tel), MD5('$key'))as member_tel,
					AES_DECRYPT(UNHEX(a.email), MD5('$key'))as member_email,
					b.project_member_seq,b.roll_seq,b.project_seq
					FROM member a
					JOIN client d ON a.client_seq=d.client_seq
					LEFT JOIN project_member b ON a.member_seq=b.member_seq AND b.project_seq=?
					LEFT JOIN project c ON b.project_seq=c.project_seq
					WHERE a.nop_yn='N' AND a.del_yn='N' AND b.project_member_seq IS NULL AND a.client_seq=?
				   )";

		}


		$sql = "SELECT tm.*,b.degree,c.duty,IFNULL(d.company,'".HNAME."') company FROM (
					".$where."
				) tm
				left join degree b on tm.degree_seq=b.degree_seq
				left join duty c on tm.duty_seq=c.duty_seq
				left join client d on tm.client_seq=d.client_seq
				".$dutyWhere."
				ORDER BY tm.roll_seq,d.company";

		/*
		$sql = "SELECT tm.*,b.degree,c.duty,IFNULL(d.company,'".HNAME."') company FROM (
					(
						SELECT a.*,b.project_member_seq,b.roll_seq,b.project_seq
						FROM member a
						LEFT JOIN project_member b ON a.member_seq=b.member_seq AND b.project_seq=?
						WHERE a.nop_yn='Y' AND a.del_yn='N' AND b.project_member_seq IS NULL
					)
					UNION ALL
					(
						SELECT a.*,b.project_member_seq,b.roll_seq,b.project_seq
						FROM member a
						JOIN client d ON a.client_seq=d.client_seq
						LEFT JOIN project_member b ON a.member_seq=b.member_seq AND b.project_seq=?
						LEFT JOIN project c ON b.project_seq=c.project_seq
						WHERE a.nop_yn='N' AND a.del_yn='N' AND b.project_member_seq IS NULL AND a.client_seq=?
					)
				) tm
				left join degree b on tm.degree_seq=b.degree_seq
				left join duty c on tm.duty_seq=c.duty_seq
				left join client d on tm.client_seq=d.client_seq
				ORDER BY tm.roll_seq,d.company";
		*/
		return $this->db->query($sql
									,array(
										   	$project_seq
											,$client_seq
											)
								)->result();


	}
	public function saveInvolveProject($data) {

		$project_seq=$data['project_seq'];
		$member_seq=$data['member_seq'];


		$sql="select count(*) cnt from project_member where project_seq=? and member_seq=? ";
		$cnt=$this->db->query($sql
									,array(
										   	$project_seq
											,$member_seq
											)
								)->row()->cnt;
		if($cnt==0){
			$this->db->trans_begin();

			$sql="select nop_yn from member where member_seq=?";
			$nop_yn=$this->db->query($sql
									,array(
										   	$member_seq
											)
								)->row()->nop_yn;

			if($nop_yn=="Y"){
				$sql = "insert into project_member (project_seq,member_seq,ins_date,upd_date) values (?,?,NOW(),NOW()) ";
				$this->db->query($sql
										,array(
											 $project_seq
											 ,$member_seq
											)
										);
			}else{
				$sql = "insert into project_member (project_seq,member_seq,roll_seq,ins_date,upd_date) values (?,?,0,NOW(),NOW()) ";
				$this->db->query($sql
										,array(
											 $project_seq
											 ,$member_seq
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
		}else{
			return "exist";
		}
	}
	public function removeInvolveProject($data) {

		$project_seq=$data['project_seq'];
		$member_seq=$data['member_seq'];

		$this->db->trans_begin();
		$sql = "delete from project_member where project_seq=? and member_seq=? ";
		$this->db->query($sql
								,array(
									 $project_seq
									 ,$member_seq
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
	public function changeRollProject($data) {

		$project_seq=$data['project_seq'];
		$member_seq=$data['member_seq'];
		$roll_seq=$data['roll_seq'];

		$this->db->trans_begin();
		if($roll_seq==""){
			$sql = "update project_member set roll_seq=null where project_seq=? and member_seq=? ";
			$this->db->query($sql
									,array(
										 $project_seq
										 ,$member_seq
										)
									);
		}else{

			$sql = "update project_member set roll_seq=? where project_seq=? and member_seq=? ";
			$this->db->query($sql
									,array(
										 $roll_seq
										, $project_seq
										 ,$member_seq
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
	public function saveScheduleProject($data) {

		$project_seq=$data['project_seq'];
		$title=$data['title'];
		$sdate=$data['sdate'];
		$edate=$data['edate'];
		$fristBoard=$data['fristBoard'];
		$color=$data['color'];

		$this->db->trans_begin();

		$sql = "insert into project_schedule (project_seq,title,sdate,edate,boardNum,color,ins_date,upd_date) values (?,?,?,?,?,?,NOW(),NOW()) ";
					$this->db->query($sql
							,array(
								 $project_seq
								 ,$title
								 ,$sdate
								 ,$edate
								 ,$fristBoard
								 ,$color
								)
							);
		$project_schedule_seq=$this->db->insert_id();

		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			return "fail";
		}else{
			$this->db->trans_commit();
			return $project_schedule_seq;
		}

	}
	public function changeScheduleProject($data) {

		$project_seq=$data['project_seq'];
		$project_schedule_seq=$data['project_schedule_seq'];
		$title=$data['title'];
		$sdate=$data['sdate'];
		$edate=$data['edate'];
		$boardSelect=$data['boardSelect'];

		$this->db->trans_begin();

		$sql = "update project_schedule set title=?,sdate=?,edate=?,boardNum=?,upd_date=NOW() where project_seq=? and project_schedule_seq=? ";
					$this->db->query($sql
							,array(
								 $title
								 ,$sdate
								 ,$edate
								 ,$boardSelect
								 ,$project_seq
								 ,$project_schedule_seq
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
	public function removeScheduleProject($data) {

		$project_seq=$data['project_seq'];
		$project_schedule_seq=$data['project_schedule_seq'];

		$this->db->trans_begin();

		$sql = "delete from project_schedule where project_seq=? and project_schedule_seq=? ";
					$this->db->query($sql
							,array(
								 $project_seq
								 ,$project_schedule_seq
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
	public function scheduleDetailProject($data) {
		$project_seq=$data['project_seq'];
		$start=$data['start'];
		$end=$data['end'];
		/*
		$sql = "select a.project_schedule_seq id,CONCAT(a.title,'(',IFNULL((SELECT board_name FROM board_manage WHERE board_seq = a.boardNum),'-'),')') title,a.sdate start,DATE_ADD(a.edate,INTERVAL 1 DAY) end
				from project_schedule a
				where 0=0 and a.project_seq=? ";
		*/

		if($start!="" && $end!=""){
			$sql = "select a.project_schedule_seq id,a.title,a.color,a.boardNum,(SELECT board_name FROM board_manage WHERE board_seq = a.boardNum) as description   ,a.sdate start,DATE_ADD(a.edate,INTERVAL 1 DAY) end
					from project_schedule a
					where 0=0 and a.project_seq=?  and not (a.sdate>? or a.edate<? ) ";

			return $this->db->query($sql
										,array(
											   	$project_seq
											   	,$end
											   	,$start
												)
									)->result();
		}else{
			$sql = "select a.project_schedule_seq id,a.title,a.color,a.boardNum,(SELECT board_name FROM board_manage WHERE board_seq = a.boardNum) as description   ,a.sdate start,DATE_ADD(a.edate,INTERVAL 1 DAY) end
					from project_schedule a
					where 0=0 and a.project_seq=?  ";

			return $this->db->query($sql
										,array(
											   	$project_seq
												)
									)->result();
		}


	}
	public function saveIssueProject($data) {

		$project_seq=$data['project_seq'];
		$title=$data['title'];
		$project_schedule_seq=$data['project_schedule_seq'];
		$category_seq=$data['category_seq'];
		$member_seq_receive=$data['member_seq_receive'];
		$member_seq_send=$data['member_seq_send'];
		$content=$data['content'];
		$level=$data['level'];

		$ori_filename=$data['ori_filename'];
		$path=$data['path'];
		$filename=$data['filename'];
		$board_num=$data['board_num'];

		$client_dp_yn=$data['client_dp_yn'];



		$this->db->trans_begin();

		$sql = "insert into issue (title,project_schedule_seq,project_seq,category_seq,member_seq_receive,member_seq_send,level,board_seq,client_dp_yn,ins_date,upd_date)
				values (?,?,?,?,?,?,?,?,?,NOW(),NOW())
		";

		$this->db->query($sql
								,array(
									 $title
									 ,$project_schedule_seq
									 ,$project_seq
									 ,$category_seq
									 ,$member_seq_receive
									 ,$member_seq_send
									 ,$level
									 ,$board_num
									 ,$client_dp_yn
									)
								);

		$issue_seq=$this->db->insert_id();

		$sql = "insert into issue_reply (issue_seq,member_seq_receive,member_seq_send,content,ins_date,upd_date)
				values (?,?,?,?,NOW(),NOW())
		";

		$this->db->query($sql
								,array(
									 $issue_seq
									 ,$member_seq_receive
									 ,$member_seq_send
									 ,$content
									)
								);

		$issue_reply_seq=$this->db->insert_id();

		for($i=0;$i<count($filename);$i++){
			if($filename[$i]!=""){
				$sql = "insert into issue_file (issue_seq,issue_reply_seq,ori_filename,path,filename,ins_date,upd_date)
					values (?,?,?,?,?,NOW(),NOW())
					";

					$this->db->query($sql
											,array(
												 $issue_seq
												 ,$issue_reply_seq
												 ,$ori_filename[$i]
												 ,$path[$i]
												 ,$filename[$i]
												)
											);
			}
		}

		$sql="select member_name,member_id from member where member_seq=?";
		$member_send=$this->db->query($sql
								,array(
									 $member_seq_send
									)
								)->row();

		$sql="select member_name,member_id from member where member_seq=?";
		$member_receive=$this->db->query($sql
								,array(
									 $member_seq_receive
									)
								)->row();
		$gubun="0";//신규등록
		$history=$member_send->member_name."(".$member_send->member_id.") : 이슈등록";

		$sql="insert into issue_history (issue_seq,gubun,history,member_seq,ins_date,upd_date) values (?,?,?,?,NOW(),NOW()) ";
				$this->db->query($sql
								,array(
									 $issue_seq
									 ,$gubun
									 ,$history
									 ,$member_seq_send
									)
								);


		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			return "0";
		}else{
			$this->db->trans_commit();
			return $issue_seq;
		}

	}

	public function listIssueProject_count($data) {
		$project_seq=$data['project_seq'];
		$srcstr=$data['srcstr'];
		$member_seq_receive=$data['member_seq_receive'];
		$member_seq_send=$data['member_seq_send'];
		$project_schedule_seq=$data['project_schedule_seq'];
		$category_seq=$data['category_seq'];
		$level=$data['level'];
		$sdate=$data['sdate'];
		$edate=$data['edate'];
		$board_num=$data['board_num'];

		$sql = "select count(*) cnt
				from issue a
				where a.project_seq=? and a.board_seq=? ";

		if($srcstr!=""){
			$sql .=" and (a.title like '%$srcstr%' || a.issue_seq IN (SELECT DISTINCT issue_seq FROM issue_reply WHERE content LIKE '%$srcstr%'))";
		}
		if($member_seq_receive!=""){
			$sql .=" and a.member_seq_receive='$member_seq_receive' ";
		}
		if($member_seq_send!=""){
			$sql .=" and a.member_seq_send='$member_seq_send' ";
		}
		if($project_schedule_seq!=""){
			$sql .=" and a.project_schedule_seq='$project_schedule_seq' ";
		}
		if($category_seq!=""){
			$sql .=" and a.category_seq='$category_seq' ";
		}
		if($level!=""){
			$sql .=" and a.level='$level' ";
		}
		if($sdate!=""){
			$sql .=" and DATE_FORMAT(a.ins_date,'%Y-%m-%d')>='$sdate'";
		}
		if($edate!=""){
			$sql .=" and DATE_FORMAT(a.ins_date,'%Y-%m-%d')<='$edate'";
		}
		if($this->nop_yn=="N"){
			$sql .=" and a.client_dp_yn='Y' ";
		}
		return $this->db->query($sql,array($project_seq,$board_num))->row()->cnt;

	}
	public function listIssueProject($data) {

		$project_seq=$data['project_seq'];
		$srcstr=$data['srcstr'];
		$member_seq_receive=$data['member_seq_receive'];
		$member_seq_send=$data['member_seq_send'];
		$project_schedule_seq=$data['project_schedule_seq'];
		$category_seq=$data['category_seq'];
		$level=$data['level'];
		$sdate=$data['sdate'];
		$edate=$data['edate'];
		$addDaySearch=$data['addDaySearch'];
		$board_num=$data['board_num'];

		$page_num=(int)$data['page_num'];
		$page_size=(int)$data['page_size'];
		$page_no=($page_num-1)*$page_size;


		$sql = "select a.*,b.member_id member_id_receive,b.member_name member_name_receive,c.member_id member_id_send,c.member_name member_name_send
					,d.category,e.title schedule_title ,( SELECT upd_date FROM issue_reply WHERE issue_seq =a.issue_seq AND del_yn ='N' ORDER BY upd_date DESC LIMIT 1 ) AS upd_date
				from issue a
				left join member b on a.member_seq_receive=b.member_seq
				left join member c on a.member_seq_send=c.member_seq
				left join category d on a.category_seq=d.category_seq
				left join project_schedule e on a.project_schedule_seq=e.project_schedule_seq
				where a.project_seq=? and board_seq=?";

		if($srcstr!=""){
			$sql .=" and (a.title like '%$srcstr%' || a.issue_seq IN (SELECT DISTINCT issue_seq FROM issue_reply WHERE content LIKE '%$srcstr%'))";
		}
		if($member_seq_receive!=""){
			$sql .=" and a.member_seq_receive='$member_seq_receive' ";
		}
		if($member_seq_send!=""){
			$sql .=" and a.member_seq_send='$member_seq_send' ";
		}
		if($project_schedule_seq!=""){
			$sql .=" and a.project_schedule_seq='$project_schedule_seq' ";
		}
		if($category_seq!=""){
			$sql .=" and a.category_seq='$category_seq' ";
		}
		if($level!=""){
			$sql .=" and a.level='$level' ";
		}
		if($sdate!=""){
			$sql .=" and DATE_FORMAT(a.ins_date,'%Y-%m-%d')>='$sdate'";
		}
		if($edate!=""){
			$sql .=" and DATE_FORMAT(a.ins_date,'%Y-%m-%d')<='$edate'";
		}
		if($this->nop_yn=="N"){
			$sql .=" and a.client_dp_yn='Y' ";
		}
		if($addDaySearch =="issue"){
			$sql .=" order by ins_date desc limit ?,?";
		}else{
			$sql .=" order by upd_date desc limit ?,?";
		}

		return $this->db->query($sql
									,array(
										   	$project_seq
											,$board_num
											,$page_no
											,$page_size
											)
								)->result();

	}
	public function changeIssueProject($data) {

		$project_seq=$data['project_seq'];
		$issue_seq=$data['issue_seq'];
		$finish_yn=$data['finish_yn'];
		$rate=$data['rate'];

		$this->db->trans_begin();

		if($rate!=""){
			$sql = "update issue set finish_yn=?,rate=? where project_seq=? and issue_seq=? ";
			$this->db->query($sql
									,array(
										 $finish_yn
										 ,$rate
										 ,$project_seq
										 ,$issue_seq
										)
									);
		}else{
			$sql = "update issue set finish_yn=? where project_seq=? and issue_seq=? ";
			$this->db->query($sql
									,array(
										 $finish_yn
										 ,$project_seq
										 ,$issue_seq
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
	public function IssueDetailProject($data) {

		$project_seq=$data['project_seq'];
		$issue_seq=$data['issue_seq'];


		$sql = "select a.*,b.member_id member_id_receive,b.member_name member_name_receive,c.member_id member_id_send,c.member_name member_name_send
					,d.category,e.title schedule_title
				from issue a
				left join member b on a.member_seq_receive=b.member_seq
				left join member c on a.member_seq_send=c.member_seq
				left join category d on a.category_seq=d.category_seq
				left join project_schedule e on a.project_schedule_seq=e.project_schedule_seq
				where 0=0 and a.project_seq=? and a.issue_seq=? ";


		return $this->db->query($sql
									,array(
										   	$project_seq
											,$issue_seq
											)
								)->row();

	}
	public function IssueDetailReplyProject($data) {

		$project_seq=$data['project_seq'];
		$issue_seq=$data['issue_seq'];
		$issueSort=$data['issueSort'];

		//echo $issueSort;

		$sql = "select a.*,b.member_name member_name_send,b.member_id member_is_send
				from issue_reply a
				left join member b on a.member_seq_send=b.member_seq
				where 0=0 and a.del_yn='N' and a.issue_seq=?
				order by issue_reply_seq $issueSort ";

		 return $this->db->query($sql,array($issue_seq))->result();


	}
	public function IssueDetailReplyProjectCnt($data) {

		$issue_seq=$data['issue_seq'];

		$sql = "SELECT COUNT(*)AS cnt
				FROM issue_reply
				WHERE 0=0 AND del_yn='N' AND issue_seq=? ";

		 return $this->db->query($sql,array($issue_seq))->row();


	}



	public function IssueDetailProject_file($data) {

		$project_seq=$data['project_seq'];
		$issue_seq=$data['issue_seq'];
		$issue_reply_seq=$data['issue_reply_seq'];


		$sql = "select a.*
				from issue_file a
				where 0=0 and a.issue_seq=? and issue_reply_seq=? ";
		return $this->db->query($sql
									,array(
										   	$issue_seq
											,$issue_reply_seq
											)
								)->result();


	}
	public function saveIssueReplyProject($data) {

		$project_seq=$data['project_seq'];
		$issue_seq=$data['issue_seq'];
		$member_seq_send=$data['member_seq'];
		$content=$data['content'];

		$ori_filename=$data['ori_filename'];
		$path=$data['path'];
		$filename=$data['filename'];



		$this->db->trans_begin();

		$sql = "insert into issue_reply (issue_seq,content,member_seq_send,ins_date,upd_date)
				values (?,?,?,NOW(),NOW())
		";

		$this->db->query($sql
								,array(
									 $issue_seq
									 ,$content
									 ,$member_seq_send
									)
								);


		$issue_reply_seq=$this->db->insert_id();

		for($i=0;$i<count($filename);$i++){
			if($filename[$i]!=""){
				$sql = "insert into issue_file (issue_seq,issue_reply_seq,ori_filename,path,filename,ins_date,upd_date)
					values (?,?,?,?,?,NOW(),NOW())
					";

					$this->db->query($sql
											,array(
												 $issue_seq
												 ,$issue_reply_seq
												 ,$ori_filename[$i]
												 ,$path[$i]
												 ,$filename[$i]
												)
											);
			}
		}

		$sql="select member_name,member_id from member where member_seq=?";
		$member_send=$this->db->query($sql
								,array(
									 $member_seq_send
									)
								)->row();

		$gubun="6";//신규등록
		$history=$member_send->member_name."(".$member_send->member_id.") : 이슈내용등록";

		$sql="insert into issue_history (issue_seq,gubun,history,member_seq,ins_date,upd_date) values (?,?,?,?,NOW(),NOW()) ";
				$this->db->query($sql
								,array(
									 $issue_seq
									 ,$gubun
									 ,$history
									 ,$member_seq_send
									)
								);


		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			return "0";
		}else{
			$this->db->trans_commit();
			return $issue_reply_seq;
		}

	}
	public function saveIssueReplyFileProject($data) {

		$project_seq=$data['project_seq'];
		$issue_seq=$data['issue_seq'];
		$member_seq_send=$data['member_seq'];
		$issue_reply_seq=$data['issue_reply_seq'];

		$ori_filename=$data['ori_filename'];
		$path=$data['path'];
		$filename=$data['filename'];



		$this->db->trans_begin();


		$sql = "insert into issue_file (issue_seq,issue_reply_seq,ori_filename,path,filename,ins_date,upd_date)
			values (?,?,?,?,?,NOW(),NOW())
			";

			$this->db->query($sql
									,array(
										 $issue_seq
										 ,$issue_reply_seq
										 ,$ori_filename
										 ,$path
										 ,$filename
										)
									);

		$issue_file_seq=$this->db->insert_id();

		$sql="select member_name,member_id from member where member_seq=?";
		$member_send=$this->db->query($sql
								,array(
									 $member_seq_send
									)
								)->row();


		$gubun="4";//파일첨부
		$history=$member_send->member_name."(".$member_send->member_id.") : ".$ori_filename." 파일 첨부";

		$sql="insert into issue_history (issue_seq,gubun,history,member_seq,ins_date,upd_date) values (?,?,?,?,NOW(),NOW()) ";
				$this->db->query($sql
								,array(
									 $issue_seq
									 ,$gubun
									 ,$history
									 ,$member_seq_send
									)
								);


		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			return "0";
		}else{
			$this->db->trans_commit();
			return $issue_file_seq;
		}

	}
	public function removeIssueReplyFileProject($data) {

		$project_seq=$data['project_seq'];
		$issue_seq=$data['issue_seq'];
		$member_seq_send=$data['member_seq'];
		$issue_file_seq=$data['issue_file_seq'];
		$ori_filename=$data['ori_filename'];




		$this->db->trans_begin();


		$sql = "delete from issue_file where issue_seq=? and issue_file_seq=?
			";

			$this->db->query($sql
									,array(
										 $issue_seq
										 ,$issue_file_seq
										)
									);


		$sql="select member_name,member_id from member where member_seq=?";
		$member_send=$this->db->query($sql
								,array(
									 $member_seq_send
									)
								)->row();


		$gubun="4";//파일삭제
		$history=$member_send->member_name."(".$member_send->member_id.") : ".$ori_filename." 파일 삭제";

		$sql="insert into issue_history (issue_seq,gubun,history,member_seq,ins_date,upd_date) values (?,?,?,?,NOW(),NOW()) ";
				$this->db->query($sql
								,array(
									 $issue_seq
									 ,$gubun
									 ,$history
									 ,$member_seq_send
									)
								);


		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			return "0";
		}else{
			$this->db->trans_commit();
			return "1";
		}

	}

	public function removeIssueReplyProject($data) {

		$project_seq=$data['project_seq'];
		$issue_seq=$data['issue_seq'];
		$issue_reply_seq=$data['issue_reply_seq'];
		$member_seq_send=$data['member_seq'];

		$this->db->trans_begin();


		$sql = "delete from issue_file where issue_seq=? and issue_reply_seq=?
			";

			$this->db->query($sql
									,array(
										 $issue_seq
										 ,$issue_reply_seq
										)
									);

		$sql = "update issue_reply set del_yn='Y' where issue_seq=? and issue_reply_seq=? and member_seq_send=?
			";

			$this->db->query($sql
									,array(
										 $issue_seq
										 ,$issue_reply_seq
										 ,$member_seq_send
										)
									);


		$sql="select member_name,member_id from member where member_seq=?";
		$member_send=$this->db->query($sql
								,array(
									 $member_seq_send
									)
								)->row();


		$gubun="7";//내용 삭제
		$history=$member_send->member_name."(".$member_send->member_id.") : [".$issue_reply_seq."] 내용 삭제";

		$sql="insert into issue_history (issue_seq,gubun,history,member_seq,ins_date,upd_date) values (?,?,?,?,NOW(),NOW()) ";
				$this->db->query($sql
								,array(
									 $issue_seq
									 ,$gubun
									 ,$history
									 ,$member_seq_send
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
	public function editIssueReplyProject($data) {

		$project_seq=$data['project_seq'];
		$issue_seq=$data['issue_seq'];
		$issue_reply_seq=$data['issue_reply_seq'];
		$content=$data['content'];
		$member_seq_send=$data['member_seq'];

		$this->db->trans_begin();



		$sql = "update issue_reply set content=?,upd_date=NOW() where issue_seq=? and issue_reply_seq=? and member_seq_send=?
			";

			$this->db->query($sql
									,array(
										 $content
										 ,$issue_seq
										 ,$issue_reply_seq
										 ,$member_seq_send
										)
									);


		$sql="select member_name,member_id from member where member_seq=?";
		$member_send=$this->db->query($sql
								,array(
									 $member_seq_send
									)
								)->row();


		$gubun="8";//내용 삭제
		$history=$member_send->member_name."(".$member_send->member_id.") : [".$issue_reply_seq."] 내용 수정";

		$sql="insert into issue_history (issue_seq,gubun,history,member_seq,ins_date,upd_date) values (?,?,?,?,NOW(),NOW()) ";
				$this->db->query($sql
								,array(
									 $issue_seq
									 ,$gubun
									 ,$history
									 ,$member_seq_send
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
	public function changeIssueCategory($data) {

		$project_seq=$data['project_seq'];
		$issue_seq=$data['issue_seq'];
		$category_seq=$data['category_seq'];
		$member_seq_send=$data['member_seq'];

		$ori_category=$data['ori_category'];

		$this->db->trans_begin();



		$sql = "update issue set category_seq=?,upd_date=NOW() where issue_seq=?
			";

			$this->db->query($sql
									,array(
										 $category_seq
										 ,$issue_seq
										)
									);
		$sql="select category from category where category_seq=?";
		$categoryView=$this->db->query($sql
								,array(
									 $category_seq
									)
								)->row();

		$sql="select member_name,member_id from member where member_seq=?";
		$member_send=$this->db->query($sql
								,array(
									 $member_seq_send
									)
								)->row();


		$gubun="1";//업무구분
		$history=$member_send->member_name."(".$member_send->member_id.") : ".$ori_category." -> ".$categoryView->category." : 업무구분수정";

		$sql="insert into issue_history (issue_seq,gubun,history,member_seq,ins_date,upd_date) values (?,?,?,?,NOW(),NOW()) ";
				$this->db->query($sql
								,array(
									 $issue_seq
									 ,$gubun
									 ,$history
									 ,$member_seq_send
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
	public function changeIssueMemberReceive($data) {

		$project_seq=$data['project_seq'];
		$issue_seq=$data['issue_seq'];
		$member_seq_receive=$data['member_seq_receive'];
		$member_seq_send=$data['member_seq'];

		$ori_member_name_receive=$data['ori_member_name_receive'];
		$ori_member_id_receive=$data['ori_member_id_receive'];

		$this->db->trans_begin();



		$sql = "update issue set member_seq_send=?,member_seq_receive=?,upd_date=NOW() where issue_seq=?
			";

			$this->db->query($sql
									,array(
										  $member_seq_send
										 ,$member_seq_receive
										 ,$issue_seq
										)
									);


		$sql="select member_name,member_id from member where member_seq=?";
		$member_send=$this->db->query($sql
								,array(
									 $member_seq_send
									)
								)->row();
		$sql="select member_name,member_id from member where member_seq=?";
		$member_receive=$this->db->query($sql
								,array(
									 $member_seq_receive
									)
								)->row();

		$gubun="2";//이슈할당
		$history=$member_send->member_name."(".$member_send->member_id.") :".$ori_member_name_receive."(".$ori_member_id_receive.") -> ".$member_receive->member_name."(".$member_receive->member_id.") : 이슈할당 변경";

		$sql="insert into issue_history (issue_seq,gubun,history,member_seq,ins_date,upd_date) values (?,?,?,?,NOW(),NOW()) ";
				$this->db->query($sql
								,array(
									 $issue_seq
									 ,$gubun
									 ,$history
									 ,$member_seq_send
									)
								);


		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			return "0";
		}else{
			$this->db->trans_commit();
			return "1";
		}

	}
	public function changeIssueLevel($data) {

		$project_seq=$data['project_seq'];
		$issue_seq=$data['issue_seq'];
		$level=$data['level'];
		$ori_level=$data['ori_level'];
		$member_seq_send=$data['member_seq'];

		$this->db->trans_begin();



		$sql = "update issue set level=?,upd_date=NOW() where issue_seq=?
			";

			$this->db->query($sql
									,array(
										 $level
										 ,$issue_seq
										)
									);


		$sql="select member_name,member_id from member where member_seq=?";
		$member_send=$this->db->query($sql
								,array(
									 $member_seq_send
									)
								)->row();


		$gubun="3";//중요도
		$history=$member_send->member_name."(".$member_send->member_id.") ".$ori_level." -> ".$level.": 중요도변경";

		$sql="insert into issue_history (issue_seq,gubun,history,member_seq,ins_date,upd_date) values (?,?,?,?,NOW(),NOW()) ";
				$this->db->query($sql
								,array(
									 $issue_seq
									 ,$gubun
									 ,$history
									 ,$member_seq_send
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
	public function viewIssueFile($data) {

		$issue_file_seq=$data['seq'];

		$sql = "select a.*
				from issue_file a
				where 0=0 and issue_file_seq=? ";


		return $this->db->query($sql
									,array(
										   $issue_file_seq
									)
								)->row();

	}
	public function listIssueFile($data) {

		$issue_reply_seq=$data['issue_reply_seq'];
		$issue_seq=$data['issue_seq'];

		$sql = "select a.*
				from issue_file a
				where 0=0 and issue_seq=? and issue_reply_seq=? ";


		return $this->db->query($sql
									,array(
										   $issue_seq
										   ,$issue_reply_seq
									)
								)->result();

	}
	public function listIssueHistory($data) {

		$issue_seq=$data['issue_seq'];

		$sql = "select a.*
				from issue_history a
				where 0=0 and issue_seq=? ";


		return $this->db->query($sql
									,array(
										   $issue_seq

									)
								)->result();

	}

	public function saveConferenceProject($data) {


		$project_seq=$data['project_seq'];
		$title=$data['title'];
		$meeting_time=$data['meeting_time'];
		$members=$data['members'];
		$member_seqs=$data['member_seqs'];
		$meeting_date=$data['meeting_date'];
		$content=$data['content'];
		$issue=$data['issue'];
		$color=$data['color'];
		$member_seq=$data['member_seq'];
		$place=$data['place'];
		$client_dp_yn=$data['client_dp_yn'];

		$ori_filename=$data['ori_filename'];
		$path=$data['path'];
		$filename=$data['filename'];



		$this->db->trans_begin();


		$sql = "insert into project_conference (project_seq,title,meeting_time,place,color,members,meeting_date,content,issue,member_seq,member_seqs,client_dp_yn,ins_date,upd_date)
			values (?,?,?,?,?,?,?,?,?,?,?,?,NOW(),NOW())
			";

			$this->db->query($sql
									,array(
										 $project_seq
										 ,$title
										 ,$meeting_time
										 ,$place
										 ,$color
										 ,$members
										 ,$meeting_date
										 ,$content
										 ,$issue
										 ,$member_seq
										 ,$member_seqs
										 ,$client_dp_yn
										)
									);

		$project_conference_seq=$this->db->insert_id();

		for($i=0;$i<count($filename);$i++){
			if($filename[$i]!=""){
				$sql = "insert into project_conference_file (project_conference_seq,ori_filename,path,filename,ins_date,upd_date)
					values (?,?,?,?,NOW(),NOW())
					";

					$this->db->query($sql
											,array(
												 $project_conference_seq
												 ,$ori_filename[$i]
												 ,$path[$i]
												 ,$filename[$i]
												)
											);
			}
		}

		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			return "0";
		}else{
			$this->db->trans_commit();
			return $project_conference_seq;
		}

	}
	public function listConferenceProject($data) {
		$project_seq=$data['project_seq'];


		$sql = "select a.project_conference_seq id,a.title,a.color,a.meeting_date start,DATE_ADD(a.meeting_date,INTERVAL 1 DAY) end
				from project_conference a
				where a.del_yn='N' and a.project_seq=? ";
		if($this->nop_yn=="N"){
			$sql .=" and a.client_dp_yn='Y' ";
		}
		$sql.="		order by meeting_date";

		return $this->db->query($sql
									,array(
										   	$project_seq
											)
								)->result();

	}
	public function viewConferenceProject($data) {
		$project_seq=$data['project_seq'];
		$project_conference_seq=$data['project_conference_seq'];

		$sql = "select *
				from project_conference a
				where a.del_yn='N' and a.project_seq=?  and project_conference_seq=? ";

		return $this->db->query($sql
									,array(
										   	$project_seq
											,$project_conference_seq
											)
								)->row();

	}
	public function viewConferenceProject_file($data) {
		$project_seq=$data['project_seq'];
		$project_conference_seq=$data['project_conference_seq'];

		$sql = "select *
				from project_conference_file a
				where 0=0 and project_conference_seq=? ";

		return $this->db->query($sql
									,array(
										   	$project_conference_seq
											)
								)->result();

	}
	public function editConferenceProject($data) {

		$project_seq=$data['project_seq'];
		$project_conference_seq=$data['project_conference_seq'];
		$title=$data['title'];
		$meeting_time=$data['meeting_time'];
		$members=$data['members'];
		$meeting_date=$data['meeting_date'];
		$content=$data['content'];
		$issue=$data['issue'];
		$member_seq=$data['member_seq'];
		$member_seqs=$data['member_seqs'];
		$place=$data['place'];

		$filename=$data['filename'];
		$ori_filename=$data['ori_filename'];
		$path=$data['path'];


		$this->db->trans_begin();


		$sql = "update project_conference set title=?,meeting_time=?,place=?,members=?,content=?,issue=?,member_seqs=?,upd_date=NOW()
				where project_conference_seq=?
			";

			$this->db->query($sql
									,array(
										 $title
										 ,$meeting_time
										 ,$place
										 ,$members
										 ,$content
										 ,$issue
										 ,$member_seqs
										 ,$project_conference_seq
										)
									);

		$sql="delete from project_conference_file where project_conference_seq=?";
		$this->db->query($sql
								,array(
									 $project_conference_seq
									)
								);

		for($i=0;$i<count($filename);$i++){
			if($filename[$i]!=""){
				$sql = "insert into project_conference_file (project_conference_seq,ori_filename,path,filename,ins_date,upd_date)
					values (?,?,?,?,NOW(),NOW())
					";

					$this->db->query($sql
											,array(
												 $project_conference_seq
												 ,$ori_filename[$i]
												 ,$path[$i]
												 ,$filename[$i]
												)
											);
			}
		}

		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			return "fail";
		}else{
			$this->db->trans_commit();
			return "success";
		}

	}
	public function removeConferenceProject($data) {

		$project_seq=$data['project_seq'];
		$project_conference_seq=$data['project_conference_seq'];
		$member_seq=$data['member_seq'];



		$this->db->trans_begin();


		$sql = "update project_conference set del_yn='Y',upd_date=NOW()
				where project_conference_seq=?
			";

			$this->db->query($sql
									,array(
										 $project_conference_seq
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
	public function viewConferenceFile($data) {
		$project_conference_file_seq=$data['seq'];

		$sql = "select *
				from project_conference_file a
				where 0=0 and project_conference_file_seq=? ";

		return $this->db->query($sql
									,array(
										   	$project_conference_file_seq
											)
								)->row();

	}

	public function listDocumentProjectNotice($data) {

		$project_seq=$data['project_seq'];

		$sql = "select a.*,(select count(project_document_file_seq) from project_document_file where project_document_seq=a.project_document_seq) file_cnt
						,b.member_name
				from project_document a
				join member b on a.member_seq=b.member_seq
				where 0=0 and a.project_seq=? and a.notice_yn='Y' ";

		if($this->nop_yn=="N"){
			$sql .=" and a.client_dp_yn='Y' ";
		}

		$sql .= " order by a.upd_date desc";

		return $this->db->query($sql,array($project_seq))->result();

	}
	public function listDocumentProject_count($data) {

		$title=$data['title'];
		$content=$data['content'];
		$member=$data['member'];
		$sdate=$data['sdate'];
		$edate=$data['edate'];

		$project_seq=$data['project_seq'];

		$sql = "select count(*) cnt
				from project_document a
				join member b on a.member_seq=b.member_seq
				where 0=0 ";

		if($title!=""){
			$sql .=" and a.title like '%$title%'";
		}
		if($content!=""){
			$sql .=" and a.content like '%$content%'";
		}
		if($member!=""){
			$sql .=" and (b.member_id like '%$member%' or b.member_name like '%$member%')";
		}
		if($sdate!=""){
			$sql .=" and DATE_FORMAT(a.ins_date,'%Y-%m-%d')>='$sdate'";
		}
		if($edate!=""){
			$sql .=" and DATE_FORMAT(a.ins_date,'%Y-%m-%d')<='$edate'";
		}

		$sql.= " and a.notice_yn='N' and a.project_seq=?
				 ";

		if($this->nop_yn=="N"){
			$sql .=" and a.client_dp_yn='Y' ";
		}


		return $this->db->query($sql,array($project_seq))->row()->cnt;

	}
	public function listDocumentProject($data) {

		$title=$data['title'];
		$content=$data['content'];
		$member=$data['member'];
		$sdate=$data['sdate'];
		$edate=$data['edate'];


		$project_seq=$data['project_seq'];
		$page_num=(int)$data['page_num'];
		$page_size=(int)$data['page_size'];
		$page_no=($page_num-1)*$page_size;


		$sql = "select a.*,(select count(project_document_file_seq) from project_document_file where project_document_seq=a.project_document_seq) file_cnt
					,b.member_name
				from project_document a
				join member b on a.member_seq=b.member_seq
				where 0=0 ";
		if($title!=""){
			$sql .=" and a.title like '%$title%'";
		}
		if($content!=""){
			$sql .=" and a.content like '%$content%'";
		}
		if($member!=""){
			$sql .=" and (b.member_id like '%$member%' or b.member_name like '%$member%')";
		}
		if($sdate!=""){
			$sql .=" and DATE_FORMAT(a.ins_date,'%Y-%m-%d')>='$sdate'";
		}
		if($edate!=""){
			$sql .=" and DATE_FORMAT(a.ins_date,'%Y-%m-%d')<='$edate'";
		}

		$sql .=" and a.notice_yn='N' and a.project_seq=?  ";

		if($this->nop_yn=="N"){
			$sql .=" and a.client_dp_yn='Y' ";
		}

		$sql .= " order by a.upd_date desc limit ?,?";
		//echo $sql;
		return $this->db->query($sql
									,array(
										   	$project_seq
											,$page_no
											,$page_size
											)
								)->result();

	}
	public function saveDocumentProject($data) {

		$project_seq=$data['project_seq'];
		$title=$data['title'];
		$notice_yn=$data['notice_yn'];
		$client_dp_yn=$data['client_dp_yn'];
		$content=$data['content'];
		$member_seq=$data['member_seq'];

		$ori_filename=$data['ori_filename'];
		$path=$data['path'];
		$filename=$data['filename'];



		$this->db->trans_begin();

		$sql = "insert into project_document (title,notice_yn,client_dp_yn,content,project_seq,member_seq,ins_date,upd_date)
				values (?,?,?,?,?,?,NOW(),NOW())
		";

		$this->db->query($sql
								,array(
									 $title
									 ,$notice_yn
									 ,$client_dp_yn
									 ,$content
									 ,$project_seq
									 ,$member_seq
									)
								);

		$project_document_seq=$this->db->insert_id();

		for($i=0;$i<count($filename);$i++){
			if($filename[$i]!=""){
				$sql = "insert into project_document_file (project_document_seq,ori_filename,path,filename,ins_date,upd_date)
					values (?,?,?,?,NOW(),NOW())
					";

					$this->db->query($sql
											,array(
												 $project_document_seq
												 ,$ori_filename[$i]
												 ,$path[$i]
												 ,$filename[$i]
												)
											);
			}
		}



		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			return "fail";
		}else{
			$this->db->trans_commit();
			return "success";
		}

	}
	public function editDocumentProject($data) {

		$project_seq=$data['project_seq'];
		$project_document_seq=$data['project_document_seq'];
		$title=$data['title'];
		$notice_yn=$data['notice_yn'];
		$content=$data['content'];
		$member_seq=$data['member_seq'];

		$ori_filename=$data['ori_filename'];
		$path=$data['path'];
		$filename=$data['filename'];




		$this->db->trans_begin();

		$sql = "update project_document set title=?,notice_yn=?,content=?,upd_date=NOW() where project_document_seq=?  and member_seq=?
		";

		$this->db->query($sql
								,array(
									 $title
									 ,$notice_yn
									 ,$content
									 ,$project_document_seq
									 ,$member_seq
									)
								);

		$sql="delete from project_document_file where project_document_seq=?";
		$this->db->query($sql
								,array(
									 $project_document_seq
									)
								);

		for($i=0;$i<count($filename);$i++){
			if($filename[$i]!=""){
				$sql = "insert into project_document_file (project_document_seq,ori_filename,path,filename,ins_date,upd_date)
					values (?,?,?,?,NOW(),NOW())
					";

					$this->db->query($sql
											,array(
												 $project_document_seq
												 ,$ori_filename[$i]
												 ,$path[$i]
												 ,$filename[$i]
												)
											);
			}
		}



		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			return "fail";
		}else{
			$this->db->trans_commit();
			return "success";
		}

	}

	public function viewDocumentProject($data) {
		$project_document_seq=$data['project_document_seq'];

		$sql = "select a.*,(select count(project_document_file_seq) from project_document_file where project_document_seq=a.project_document_seq) file_cnt
					,b.member_name
				from project_document a
				join member b on a.member_seq=b.member_seq
				where 0=0 and a.project_document_seq=? ";

		return $this->db->query($sql
									,array(
										   	$project_document_seq
											)
								)->row();

	}
	public function viewDocumentProject_file($data) {
		$project_document_seq=$data['project_document_seq'];

		$sql = "select a.*
				from project_document_file a
				where 0=0 and a.project_document_seq=? ";

		return $this->db->query($sql
									,array(
										   	$project_document_seq
											)
								)->result();

	}


	public function removeDocumentProject($data) {

		$project_document_seq=$data['project_document_seq'];
		$member_seq=$data['member_seq'];


		$this->db->trans_begin();



		$sql = "delete from project_document where project_document_seq=? and member_seq=?";
		$this->db->query($sql
								,array(
									 $project_document_seq
									 ,$member_seq
									)
								);

		$sql="delete from project_document_file where project_document_seq=? ";
		$this->db->query($sql
								,array(
									 $project_document_seq
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

	public function viewDocumentProjectFile($data) {

		$seq=$data['seq'];


		$sql = "select a.*
			from project_document_file a
			where project_document_file_seq=?";

		return $this->db->query($sql,array($seq))->row();
	}
	public function projectRoll($data) {

		$member_seq=$data['member_seq'];
		$project_seq=$data['project_seq'];


		$sql = "select a.roll_seq
			from project_member a
			where a.member_seq=? and a.project_seq=? ";

		return $this->db->query($sql,array($member_seq,$project_seq))->row();
	}
}
?>