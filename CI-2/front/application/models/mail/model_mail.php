<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
|------------------------------------------------------------------------
| Author : 이현주
| Create-Date : 2014-07-10
|------------------------------------------------------------------------
*/

Class Model_mail extends MY_Model {


	public function saveMail($data) {

		$title=$data['title'];
		$mail_date=$data['mail_date'];
		$content=$data['content'];
		$target_member_seqs=$data['target_member_seqs'];
		$member_seq=$data['member_seq'];
		$client_seq=$data['client_seq'];

		$ori_filename=$data['ori_filename'];
		$path=$data['path'];
		$filename=$data['filename'];
		$gbn=$data['gbn'];




		$this->db->trans_begin();

		$sql = "insert into mail (title,content,member_seq,target_member_seq,client_seq,mail_date,gbn,ins_date,upd_date)
				values (?,?,?,?,?,?,?,NOW(),NOW())
		";

		$this->db->query($sql
								,array(
									 $title
									 ,$content
									 ,$member_seq
									 ,$target_member_seqs
									 ,$client_seq
									 ,$mail_date
									 ,$gbn
									)
								);

		$mail_seq=$this->db->insert_id();

		for($i=0;$i<count($filename);$i++){
			if($filename[$i]!=""){
				$sql = "insert into mail_file (mail_seq,ori_filename,path,filename,ins_date,upd_date)
					values (?,?,?,?,NOW(),NOW())
					";

					$this->db->query($sql
											,array(
												 $mail_seq
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
			return $mail_seq;
		}

	}
	public function editMail($data) {

		$mail_seq=$data['mail_seq'];
		$title=$data['title'];
		$mail_date=$data['mail_date'];
		$content=$data['content'];
		$send_yn=$data['send_yn'];

		$target_member_seqs=$data['target_member_seqs'];
		$member_seq=$data['member_seq'];
		$client_seq=$data['client_seq'];

		$ori_filename=$data['ori_filename'];
		$path=$data['path'];
		$filename=$data['filename'];


		$this->db->trans_begin();

		$sql = "update mail set title=?,content=?,target_member_seq=?,client_seq=?,mail_date=?,send_yn=?,upd_date=NOW() where mail_seq=? and member_seq=?
		";

		$this->db->query($sql
								,array(
									 $title
									 ,$content
									 ,$target_member_seqs
									 ,$client_seq
									 ,$mail_date
									 ,$send_yn
									 ,$mail_seq
									 ,$member_seq
									)
								);

		$sql="delete from mail_file where mail_seq=?";
		$this->db->query($sql
								,array(
									 $mail_seq
									)
								);

		for($i=0;$i<count($filename);$i++){
			if($filename[$i]!=""){
				$sql = "insert into mail_file (mail_seq,ori_filename,path,filename,ins_date,upd_date)
					values (?,?,?,?,NOW(),NOW())
					";

					$this->db->query($sql
											,array(
												 $mail_seq
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
			return $mail_seq;
		}

	}

	public function listMail_count($data) {

		$title=$data['title'];
		$content=$data['content'];
		$member=$data['member'];
		$sdate=$data['sdate'];
		$edate=$data['edate'];

		$member_seq=$data['member_seq'];
		$gbn=$data['gbn'];

		$sql = "select count(*) cnt
				from mail a
				join member b on a.member_seq=b.member_seq
				where 0=0 and a.member_seq='$member_seq' and a.gbn='$gbn' ";

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

		return $this->db->query($sql)->row()->cnt;

	}
	public function listMail($data) {

		$title=$data['title'];
		$content=$data['content'];
		$member=$data['member'];
		$sdate=$data['sdate'];
		$edate=$data['edate'];

		$member_seq=$data['member_seq'];
		$gbn=$data['gbn'];

		$page_num=(int)$data['page_num'];
		$page_size=(int)$data['page_size'];
		$page_no=($page_num-1)*$page_size;


		$sql = "select a.*,(select count(mail_file_seq) from mail_file where mail_seq=a.mail_seq) file_cnt
					,b.member_name
					,c.company
				from mail a
				join member b on a.member_seq=b.member_seq
				left join client c on a.client_seq=c.client_seq
				where 0=0 and a.member_seq='$member_seq' and a.gbn='$gbn'  ";
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



		$sql .=" order by a.upd_date desc limit ?,?";
		//echo $sql;
		return $this->db->query($sql
									,array(
										   	$page_no
											,$page_size
											)
								)->result();

	}

	public function viewMail($data) {
		$mail_seq=$data['mail_seq'];
		$member_seq=$data['member_seq'];
		$gbn=$data['gbn'];

		$sql = "select a.*,(select count(mail_file_seq) from mail_file where mail_seq=a.mail_seq) file_cnt
					,b.member_name
					,c.company
				from mail a
				join member b on a.member_seq=b.member_seq
				left join client c on a.client_seq=c.client_seq
				where 0=0 and a.mail_seq=? and a.member_seq=? and a.gbn=? ";


		return $this->db->query($sql
						,array(
							   $mail_seq
							   ,$member_seq
							   ,$gbn
							)
					)->row();
	}
	public function viewMail_file($data) {
		$mail_seq=$data['mail_seq'];

		$sql = "select a.*
				from mail_file a
				where 0=0 and a.mail_seq=? ";

		return $this->db->query($sql
						,array(
							   	$mail_seq
								)
					)->result();

	}


	public function removeMail($data) {

		$mail_seq=$data['mail_seq'];
		$member_seq=$data['member_seq'];


		$this->db->trans_begin();


		$sql="delete from mail_file where mail_seq=? ";
		$this->db->query($sql
								,array(
									$mail_seq
									)
								);

		$sql = "delete from mail where mail_seq=? and member_seq=? ";
		$this->db->query($sql
								,array(
									$mail_seq
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

	public function viewMailFile($data) {

		$seq=$data['seq'];


		$sql = "select a.*
			from mail_file a
			where mail_file_seq=?";
		//echo $sql;
		return $this->db->query($sql,array($seq))->row();
	}

	public function listSendMail($data) {


		$sql = "select a.*
			from mail a
			where mail_date=DATE_FORMAT(NOW(),'%Y-%m-%d') and a.send_yn='N' ";
		//echo $sql;
		return $this->db->query($sql)->result();
	}
	public function updateMailSendDate($data) {

		$mail_seq=$data['mail_seq'];


		$this->db->trans_begin();


		$sql = "update mail set send_yn=?,send_date=now() where mail_seq=? ";
		$this->db->query($sql
								,array(
									'Y'
									 ,$mail_seq
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