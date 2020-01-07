<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
|------------------------------------------------------------------------
| Author : 이현주
| Create-Date : 2014-07-10
|------------------------------------------------------------------------
*/

Class Model_board extends MY_Model {


	public function saveBoard($data) {

		$title=$data['title'];
		$notice_yn=$data['notice_yn'];
		$content=$data['content'];
		$level=$data['level'];
		$gubun=$data['gubun'];
		$member_seq=$data['member_seq'];

		$ori_filename=$data['ori_filename'];
		$path=$data['path'];
		$filename=$data['filename'];

		$duty_seq=$data['duty_seq'];
		$duty_seqs=$data['duty_seqs'];
		$member_seq_limit=$data['member_seq_limit'];


		$this->db->trans_begin();

		$sql = "insert into board (title,notice_yn,content,level,gubun,member_seq,duty_seqs,ins_date,upd_date)
				values (?,?,?,?,?,?,?,NOW(),NOW())
		";

		$this->db->query($sql
								,array(
									 $title
									 ,$notice_yn
									 ,$content
									 ,$level
									 ,$gubun
									 ,$member_seq
									 ,$duty_seqs
									)
								);

		$board_seq=$this->db->insert_id();

		for($i=0;$i<count($filename);$i++){
			if($filename[$i]!=""){
				$sql = "insert into board_file (board_seq,ori_filename,path,filename,ins_date,upd_date)
					values (?,?,?,?,NOW(),NOW())
					";

					$this->db->query($sql
											,array(
												 $board_seq
												 ,$ori_filename[$i]
												 ,$path[$i]
												 ,$filename[$i]
												)
											);
			}
		}

		if($member_seq_limit!=""){
			for($i=0;$i<count($member_seq_limit);$i++){
				$sql="insert into board_limit (board_seq,member_seq,ins_date,upd_date) values (?,?,NOW(),NOW())";
				$this->db->query($sql
												,array(
													 $board_seq
													 ,$member_seq_limit[$i]
													)
												);
			}
		}else{
			for($i=0;$i<count($duty_seq);$i++){
				$sql="select member_seq from member where duty_seq=? and del_yn='N' and nop_yn='Y' ";
				$result=$this->db->query($sql
												,array(
													 $duty_seq[$i]
													)
												)->result();


				foreach($result as $row){
					$sql="insert into board_limit (board_seq,member_seq,ins_date,upd_date) values (?,?,NOW(),NOW())";
					$this->db->query($sql
												,array(
													 $board_seq
													 ,$row->member_seq
													)
												);
				}

			}

		}


		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			return "0";
		}else{
			$this->db->trans_commit();
			return $board_seq;
		}

	}
	public function modifyBoard($data) {

		$board_seq=$data['board_seq'];
		$title=$data['title'];
		$notice_yn=$data['notice_yn'];
		$content=$data['content'];

		$ori_filename=$data['ori_filename'];
		$path=$data['path'];
		$filename=$data['filename'];

		$level=$data['level'];

		$duty_seq=$data['duty_seq'];
		$duty_seqs=$data['duty_seqs'];
		$member_seq_limit=$data['member_seq_limit'];

		$this->db->trans_begin();

		$sql = "update board set title=?,notice_yn=?,content=?,level=?,duty_seqs=?,upd_date=NOW() where board_seq=?
		";

		$this->db->query($sql
								,array(
									 $title
									 ,$notice_yn
									 ,$content
									 ,$level
									 ,$duty_seqs
									 ,$board_seq
									)
								);

		$sql="delete from board_file where board_seq=?";
		$this->db->query($sql
								,array(
									 $board_seq
									)
								);

		for($i=0;$i<count($filename);$i++){
			if($filename[$i]!=""){
				$sql = "insert into board_file (board_seq,ori_filename,path,filename,ins_date,upd_date)
					values (?,?,?,?,NOW(),NOW())
					";

					$this->db->query($sql
											,array(
												 $board_seq
												 ,$ori_filename[$i]
												 ,$path[$i]
												 ,$filename[$i]
												)
											);
			}
		}

		$sql = "delete from board_limit where board_seq=? ";
					$this->db->query($sql
										,array(
											 $board_seq
										));

		if($member_seq_limit!=""){
			for($i=0;$i<count($member_seq_limit);$i++){
				$sql="insert into board_limit (board_seq,member_seq,ins_date,upd_date) values (?,?,NOW(),NOW())";
				$this->db->query($sql
												,array(
													 $board_seq
													 ,$member_seq_limit[$i]
													)
												);
			}
		}else{
			for($i=0;$i<count($duty_seq);$i++){
				$sql="select member_seq from member where duty_seq=? and del_yn='N' and nop_yn='Y' ";
				$result=$this->db->query($sql
												,array(
													 $duty_seq[$i]
													)
												)->result();


				foreach($result as $row){
					$sql="insert into board_limit (board_seq,member_seq,ins_date,upd_date) values (?,?,NOW(),NOW())";
					$this->db->query($sql
												,array(
													 $board_seq
													 ,$row->member_seq
													)
												);
				}

			}

		}



		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			return "0";
		}else{
			$this->db->trans_commit();
			return $board_seq;
		}

	}
	public function listBoardNotice($data) {

		$gubun=$data['gubun'];
		$member_seq=$data['member_seq'];

		$sql = "select a.*,(select count(board_file_seq) from board_file where board_seq=a.board_seq) file_cnt
						,b.member_name
				from board a
				join member b on a.member_seq=b.member_seq
				where 0=0 and a.gubun=? and a.notice_yn='Y' ";
		if($this->auth_seq>2){
			$sql .=" and case when a.duty_seqs!='' then a.board_seq in (select distinct board_seq from board_limit where member_seq='$member_seq')";
			$sql .="      else 0=0 end";
		}
		$sql .="	order by a.upd_date desc";

		return $this->db->query($sql,array($gubun))->result();

	}
	public function listBoard_count($data) {

		$title=$data['title'];
		$content=$data['content'];
		$member=$data['member'];
		$sdate=$data['sdate'];
		$edate=$data['edate'];

		$member_seq=$data['member_seq'];

		$gubun=$data['gubun'];

		$sql = "select count(*) cnt
				from board a
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

		$sql.= " and a.notice_yn='N' and a.gubun=?";

		if($this->auth_seq>2){
			$sql .=" and case when a.duty_seqs!='' then a.board_seq in (select distinct board_seq from board_limit where member_seq='$member_seq')";
			$sql .="      else 0=0 end";
		}



		return $this->db->query($sql,array($gubun))->row()->cnt;

	}
	public function listBoard($data) {

		$title=$data['title'];
		$content=$data['content'];
		$member=$data['member'];
		$sdate=$data['sdate'];
		$edate=$data['edate'];

		$member_seq=$data['member_seq'];

		$gubun=$data['gubun'];
		$page_num=(int)$data['page_num'];
		$page_size=(int)$data['page_size'];
		$page_no=($page_num-1)*$page_size;


		$sql = "select a.*,(select count(board_file_seq) from board_file where board_seq=a.board_seq) file_cnt
					,b.member_name
				from board a
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
		if($this->auth_seq>2){
			$sql .=" and case when a.duty_seqs!='' then a.board_seq in (select distinct board_seq from board_limit where member_seq='$member_seq')";
			$sql .="      else 0=0 end";
		}
		if($gubun=="10" && isset($data['client_seq'])){

			$client_seq=sprintf("%06d",$data['client_seq']);

			$sql .=" and a.client_seqs like '%$client_seq%'";
		}


		$sql .=" and a.notice_yn='N' and a.gubun=?  order by a.upd_date desc limit ?,?";
		//echo $sql;
		return $this->db->query($sql
									,array(
										   	$gubun
											,$page_no
											,$page_size
											)
								)->result();

	}

	public function listIpmanage($data) {

		$ip=$data['ip'];
		$user=$data['user'];
		$group=$data['group'];
		$ipUse=$data['ipUse'];


		$page_num=(int)$data['page_num'];
		$page_size=(int)$data['page_size'];
		$page_no=($page_num-1)*$page_size;


		$sql = "select * from ip_manage
				where 0=0 ";
		if($ip!=""){
			$sql .=" and ip like '%$ip%'";
		}
		if($user!=""){
			$sql .=" and user like '%$user%'";
		}
		if($group!=""){
			$sql .=" and gubun = '$group' ";
		}

		if($ipUse!=""){
			if($ipUse =="Y"){
				$sql .=" and user !='' ";
			}else if($ipUse =="N"){
				$sql .=" and user = '' ";
			}
		}



		$sql .=" order by gubun,INET_ATON(ip) asc limit ?,?";

		return $this->db->query($sql
									,array(
											$page_no
											,$page_size
											)
								)->result();

	}


	public function listIpmanage_count($data) {

		$ip=$data['ip'];
		$user=$data['user'];
		$group=$data['group'];
		$ipUse=$data['ipUse'];

		$sql = "select count(*) as cnt from ip_manage
				where 0=0 ";
		if($ip!=""){
			$sql .=" and ip like '%$ip%'";
		}
		if($user!=""){
			$sql .=" and user like '%$user%'";
		}
		if($group!=""){
			$sql .=" and gubun = '$group'";
		}

		if($ipUse!=""){
			if($ipUse =="Y"){
				$sql .=" and user !='' ";
			}else if($ipUse =="N"){
				$sql .=" and user = '' ";
			}
		}
		return $this->db->query($sql)->row()->cnt;

	}


	public function saveIpmanage($data) {

		$ip=$data['ip'];
		$group=$data['group'];
		$user=$data['user'];
		$comment=$data['comment'];



		$this->db->trans_begin();

		$sql = "insert into ip_manage (ip,gubun,user,comment,ins_date,upd_date)
				values (?,?,?,?,NOW(),NOW())";

		$this->db->query($sql
								,array(
									 $ip
									 ,$group
									 ,$user
									 ,$comment
									)
								);



		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			return "0";
		}else{
			$this->db->trans_commit();
			return "11";
		}

	}

	public function modifyIpmanage($data) {

		$ipSeq=$data['ipSeq'];
		$ip=$data['ip'];
		$group=$data['group'];
		$user=$data['user'];
		$comment=$data['comment'];


		$this->db->trans_begin();

		$sql = "update ip_manage set ip=?,gubun=?,user=?,comment=?,upd_date=NOW() where ipSeq=?	";

		$this->db->query($sql
								,array(
									 $ip
									 ,$group
									 ,$user
									 ,$comment
									 ,$ipSeq
									)
								);




		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			return "0";
		}else{
			$this->db->trans_commit();
			return "11";
		}

	}


	public function viewIpmanage($data) {
		$ipSeq=$data['ipSeq'];


		$sql = "select * from ip_manage where ipSeq=? ";


		return $this->db->query($sql
									,array(
										   	$ipSeq
											)
								)->row();

	}



	public function removeIpmanage($data) {

		$ipSeq=$data['ipSeq'];


		$this->db->trans_begin();


		$sql="delete from ip_manage where ipSeq=?";
		$this->db->query($sql
								,array(
									 $ipSeq
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


	public function viewBoard($data) {
		$board_seq=$data['board_seq'];
		$member_seq=$data['member_seq'];

		$sql = "select a.*,(select count(board_file_seq) from board_file where board_seq=a.board_seq) file_cnt
					,b.member_name
				from board a
				join member b on a.member_seq=b.member_seq
				where 0=0 and a.board_seq=? ";

		if($this->auth_seq>2){
			$sql .=" and case when a.duty_seqs!='' then a.board_seq in (select distinct board_seq from board_limit where member_seq='$member_seq')";
			$sql .="      else 0=0 end";
		}

		return $this->db->query($sql
									,array(
										   	$board_seq
											)
								)->row();

	}
	public function viewBoard_file($data) {
		$board_seq=$data['board_seq'];

		$sql = "select a.*
				from board_file a
				where 0=0 and a.board_seq=? ";

		return $this->db->query($sql
									,array(
										   	$board_seq
											)
								)->result();

	}


	public function removeBoard($data) {

		$board_seq=$data['board_seq'];


		$this->db->trans_begin();


		$sql="delete from board_file where board_seq=?";
		$this->db->query($sql
								,array(
									 $board_seq
									)
								);

		$sql = "delete from board where board_seq=? ";
		$this->db->query($sql
								,array(
									 $board_seq
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

	public function viewBoardFile($data) {

		$seq=$data['seq'];


		$sql = "select a.*
			from board_file a
			where board_file_seq=?";

		return $this->db->query($sql,array($seq))->row();
	}
	public function listBoard_limit($data) {

		$board_seq=$data['board_seq'];
		$duty_seqs=$data['duty_seqs'];
		$key =CKEY;
		if($duty_seqs!=""){
			$sql = "select AES_DECRYPT(UNHEX(b.email), MD5('$key')) as email
				from board_limit a
				join member b on a.member_seq=b.member_seq
				where a.board_seq=? and b.del_yn='N' and b.nop_yn='Y' and b.work_yn='Y' ";
				return $this->db->query($sql,array($board_seq))->result();
		}else{
			$sql = "select AES_DECRYPT(UNHEX(email), MD5('$key')) as email from member where del_yn='N' and nop_yn='Y' and work_yn='Y' ";
				return $this->db->query($sql)->result();
		}

	}

	public function listBoard_client_limit($data) {

		$board_seq=$data['board_seq'];
		$client_seqs=$data['client_seqs'];
		$key =CKEY;

		if($client_seqs!=""){
			$sql = "select AES_DECRYPT(UNHEX(b.email), MD5('$key')) as email
				from board_limit a
				join member b on a.member_seq=b.member_seq
				where a.board_seq=? and b.del_yn='N' and b.nop_yn='N' and b.work_yn='Y' ";
				return $this->db->query($sql,array($board_seq))->result();
		}else{
			$sql = "select AES_DECRYPT(UNHEX(email), MD5('$key')) as email from member where del_yn='N' and nop_yn='N' ";
				return $this->db->query($sql)->result();
		}

	}

	public function saveBoardClient($data) {

		$title=$data['title'];
		$notice_yn=$data['notice_yn'];
		$content=$data['content'];
		$level=$data['level'];
		$gubun=$data['gubun'];
		$member_seq=$data['member_seq'];

		$ori_filename=$data['ori_filename'];
		$path=$data['path'];
		$filename=$data['filename'];

		$client_seq=$data['client_seq'];
		$client_seqs=$data['client_seqs'];
		$member_seq_limit=$data['member_seq_limit'];


		$this->db->trans_begin();

		$sql = "insert into board (title,notice_yn,content,level,gubun,member_seq,client_seqs,ins_date,upd_date)
				values (?,?,?,?,?,?,?,NOW(),NOW())
		";

		$this->db->query($sql
								,array(
									 $title
									 ,$notice_yn
									 ,$content
									 ,$level
									 ,$gubun
									 ,$member_seq
									 ,$client_seqs
									)
								);

		$board_seq=$this->db->insert_id();

		for($i=0;$i<count($filename);$i++){
			if($filename[$i]!=""){
				$sql = "insert into board_file (board_seq,ori_filename,path,filename,ins_date,upd_date)
					values (?,?,?,?,NOW(),NOW())
					";

					$this->db->query($sql
											,array(
												 $board_seq
												 ,$ori_filename[$i]
												 ,$path[$i]
												 ,$filename[$i]
												)
											);
			}
		}

		if($member_seq_limit!=""){
			for($i=0;$i<count($member_seq_limit);$i++){
				$sql="insert into board_limit (board_seq,member_seq,ins_date,upd_date) values (?,?,NOW(),NOW())";
				$this->db->query($sql
												,array(
													 $board_seq
													 ,$member_seq_limit[$i]
													)
												);
			}
		}else{
			for($i=0;$i<count($client_seq);$i++){
				$sql="select member_seq from member where client_seq=? and del_yn='N' and nop_yn='N' ";
				$result=$this->db->query($sql
												,array(
													 $client_seq[$i]
													)
												)->result();


				foreach($result as $row){
					$sql="insert into board_limit (board_seq,member_seq,ins_date,upd_date) values (?,?,NOW(),NOW())";
					$this->db->query($sql
												,array(
													 $board_seq
													 ,$row->member_seq
													)
												);
				}

			}

		}


		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			return "0";
		}else{
			$this->db->trans_commit();
			return $board_seq;
		}

	}
	public function modifyBoardClient($data) {

		$board_seq=$data['board_seq'];
		$title=$data['title'];
		$notice_yn=$data['notice_yn'];
		$content=$data['content'];

		$ori_filename=$data['ori_filename'];
		$path=$data['path'];
		$filename=$data['filename'];

		$level=$data['level'];

		$client_seq=$data['client_seq'];
		$client_seqs=$data['client_seqs'];
		$member_seq_limit=$data['member_seq_limit'];

		$this->db->trans_begin();

		$sql = "update board set title=?,notice_yn=?,content=?,level=?,client_seqs=?,upd_date=NOW() where board_seq=?
		";

		$this->db->query($sql
								,array(
									 $title
									 ,$notice_yn
									 ,$content
									 ,$level
									 ,$client_seqs
									 ,$board_seq
									)
								);

		$sql="delete from board_file where board_seq=?";
		$this->db->query($sql
								,array(
									 $board_seq
									)
								);

		for($i=0;$i<count($filename);$i++){
			if($filename[$i]!=""){
				$sql = "insert into board_file (board_seq,ori_filename,path,filename,ins_date,upd_date)
					values (?,?,?,?,NOW(),NOW())
					";

					$this->db->query($sql
											,array(
												 $board_seq
												 ,$ori_filename[$i]
												 ,$path[$i]
												 ,$filename[$i]
												)
											);
			}
		}

		$sql = "delete from board_limit where board_seq=? ";
					$this->db->query($sql
										,array(
											 $board_seq
										));

		if($member_seq_limit!=""){
			for($i=0;$i<count($member_seq_limit);$i++){
				$sql="insert into board_limit (board_seq,member_seq,ins_date,upd_date) values (?,?,NOW(),NOW())";
				$this->db->query($sql
												,array(
													 $board_seq
													 ,$member_seq_limit[$i]
													)
												);
			}
		}else{
			for($i=0;$i<count($client_seq);$i++){
				$sql="select member_seq from member where client_seq=? and del_yn='N' and nop_yn='N' ";
				$result=$this->db->query($sql
												,array(
													 $client_seq[$i]
													)
												)->result();


				foreach($result as $row){
					$sql="insert into board_limit (board_seq,member_seq,ins_date,upd_date) values (?,?,NOW(),NOW())";
					$this->db->query($sql
												,array(
													 $board_seq
													 ,$row->member_seq
													)
												);
				}

			}

		}



		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			return "0";
		}else{
			$this->db->trans_commit();
			return $board_seq;
		}

	}


}
?>