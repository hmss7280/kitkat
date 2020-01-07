<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
|------------------------------------------------------------------------
| Author : 이현주
| Create-Date : 2014-07-10
|------------------------------------------------------------------------
*/

Class Model_message extends MY_Model {

	
	public function saveMessage($data) {
		
		$title=$data['title'];
		$message=$data['message'];
		$member_seq_send=$data['member_seq_send'];
		$member_seq_receive=$data['member_seq_receive'];
		
	
		
		$this->db->trans_begin();
		
		$sql = "insert into message (title,message,member_seq_send,member_seq_receive,ins_date,upd_date) 
				values (?,?,?,?,NOW(),NOW())
		";	
		
		$this->db->query($sql
								,array(
									 $title
									 ,$message
									 ,$member_seq_send
									 ,$member_seq_receive
									)
								);
		$message_seq=$this->db->insert_id();
		
		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			return "0";
		}else{
			$this->db->trans_commit();
			return $message_seq;
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
	
	public function listMessageReceive_count($data) {

		$title=$data['title'];
		$message=$data['message'];
		$sdate=$data['sdate'];
		$edate=$data['edate'];
		
		$member_seq=$data['member_seq'];

		
		$sql = "select count(*) cnt 
				from message a
				join member b on a.member_seq_send=b.member_seq
				join member c on a.member_seq_receive=c.member_seq
				where 0=0 "; 
		
		if($title!=""){
			$sql .=" and a.title like '%$title%'";
		}
		if($message!=""){
			$sql .=" and a.message like '%$message%'";
		}
		
		if($sdate!=""){
			$sql .=" and DATE_FORMAT(a.ins_date,'%Y-%m-%d')>='$sdate'";
		}
		if($edate!=""){
			$sql .=" and DATE_FORMAT(a.ins_date,'%Y-%m-%d')<='$edate'";
		}		
				
		$sql.= " and a.del_yn_receive='N' and a.member_seq_receive=? ";
		
		
		return $this->db->query($sql,array($member_seq))->row()->cnt;

	}	
	public function listMessageReceive($data) {
		
		$title=$data['title'];
		$message=$data['message'];
		$sdate=$data['sdate'];
		$edate=$data['edate'];
		
		$member_seq=$data['member_seq'];
		
		$page_num=(int)$data['page_num'];
		$page_size=(int)$data['page_size'];
		$page_no=($page_num-1)*$page_size;
		
		
		$sql = "select a.*
					,b.member_name member_name_send,b.member_id member_id_send
					,c.member_name member_name_receive,c.member_id member_id_receive
				from message a
				join member b on a.member_seq_send=b.member_seq
				join member c on a.member_seq_receive=c.member_seq
				where 0=0 ";
		if($title!=""){
			$sql .=" and a.title like '%$title%'";
		}
		if($message!=""){
			$sql .=" and a.message like '%$message%'";
		}

		if($sdate!=""){
			$sql .=" and DATE_FORMAT(a.ins_date,'%Y-%m-%d')>='$sdate'";
		}
		if($edate!=""){
			$sql .=" and DATE_FORMAT(a.ins_date,'%Y-%m-%d')<='$edate'";
		}
		$sql .=" and a.del_yn_receive='N' and a.member_seq_receive=? order by a.ins_date desc limit ?,?";	
		//echo $sql;
		return $this->db->query($sql
									,array(
										   	$member_seq
											,$page_no
											,$page_size
											)
								)->result();

	}
	
	public function listMessageSend_count($data) {

		$title=$data['title'];
		$message=$data['message'];
		$sdate=$data['sdate'];
		$edate=$data['edate'];
		
		$member_seq=$data['member_seq'];

		
		$sql = "select count(*) cnt 
				from message a
				join member b on a.member_seq_send=b.member_seq
				join member c on a.member_seq_receive=c.member_seq
				where 0=0 "; 
		
		if($title!=""){
			$sql .=" and a.title like '%$title%'";
		}
		if($message!=""){
			$sql .=" and a.message like '%$message%'";
		}
		
		if($sdate!=""){
			$sql .=" and DATE_FORMAT(a.ins_date,'%Y-%m-%d')>='$sdate'";
		}
		if($edate!=""){
			$sql .=" and DATE_FORMAT(a.ins_date,'%Y-%m-%d')<='$edate'";
		}		
				
		$sql.= " and a.del_yn_send='N' and a.member_seq_send=? ";
		
		
		return $this->db->query($sql,array($member_seq))->row()->cnt;

	}	
	public function listMessageSend($data) {
		
		$title=$data['title'];
		$message=$data['message'];
		$sdate=$data['sdate'];
		$edate=$data['edate'];
		
		$member_seq=$data['member_seq'];
		
		$page_num=(int)$data['page_num'];
		$page_size=(int)$data['page_size'];
		$page_no=($page_num-1)*$page_size;
		
		
		$sql = "select a.*
					,b.member_name member_name_send,b.member_id member_id_send
					,c.member_name member_name_receive,c.member_id member_id_receive
				from message a
				join member b on a.member_seq_send=b.member_seq
				join member c on a.member_seq_receive=c.member_seq
				where 0=0 ";
		if($title!=""){
			$sql .=" and a.title like '%$title%'";
		}
		if($message!=""){
			$sql .=" and a.message like '%$message%'";
		}

		if($sdate!=""){
			$sql .=" and DATE_FORMAT(a.ins_date,'%Y-%m-%d')>='$sdate'";
		}
		if($edate!=""){
			$sql .=" and DATE_FORMAT(a.ins_date,'%Y-%m-%d')<='$edate'";
		}
		$sql .=" and a.del_yn_send='N' and a.member_seq_send=? order by a.ins_date desc limit ?,?";	
		//echo $sql;
		return $this->db->query($sql
									,array(
										   	$member_seq
											,$page_no
											,$page_size
											)
								)->result();

	}
	
	
	public function viewMessage($data) {
		$message_seq=$data['message_seq'];
		$member_seq=$data['member_seq'];
		
		

		
		$sql = "select a.*
					,b.member_name member_name_send,b.member_id member_id_send
					,c.member_name member_name_receive,c.member_id member_id_receive
				from message a
				join member b on a.member_seq_send=b.member_seq
				join member c on a.member_seq_receive=c.member_seq
				where 0=0  and a.message_seq=? and (a.member_seq_receive=? or a.member_seq_send=?)";	
		
		$result= $this->db->query($sql
									,array(
										   	$message_seq
											,$member_seq
											,$member_seq
											)
								)->row();
		
		if($result->read_date==null || $result->read_date==""){
			$sql="update message set read_date=NOW() where message_seq=? and member_seq_receive=? ";
			$this->db->query($sql ,array(
												$message_seq
												,$member_seq
												,$member_seq
												)
									);
		}
		
		return $result;

	}
	
	public function viewMessage_mail($data) {
		$message_seq=$data['message_seq'];
		
		
		$sql = "select a.*
					,b.member_name member_name_send,b.member_id member_id_send
					,c.member_name member_name_receive,c.member_id member_id_receive
				from message a
				join member b on a.member_seq_send=b.member_seq
				join member c on a.member_seq_receive=c.member_seq
				where 0=0  and a.message_seq=?";	
		
		$result= $this->db->query($sql
									,array(
										   	$message_seq
											)
								)->row();
		
		
		return $result;

	}

	public function removeMessageReceive($data) {

		$message_seq=$data['message_seq'];
		$member_seq=$data['member_seq'];
		
		$this->db->trans_begin();


		$sql="update  message set del_yn_receive='Y' where message_seq in ($message_seq) and member_seq_receive=?";
		$this->db->query($sql
								,array(
									 $member_seq
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
	public function removeMessageSend($data) {

		$message_seq=$data['message_seq'];
		$member_seq=$data['member_seq'];
		
		$this->db->trans_begin();


		$sql="update  message set del_yn_send='Y' where message_seq in ($message_seq) and member_seq_send=?";
		$this->db->query($sql
								,array(
									 $member_seq
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
	public function newcheckMessage($data){
		$member_seq=$data['member_seq'];
		
		$sql="select count(*) cnt  from message where read_date is null and del_yn_receive='N' and member_seq_receive=? ";
		return	$this->db->query($sql
								,array(
									 $member_seq
									)
								)->row()->cnt;
	}
}
?>