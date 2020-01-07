<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
|------------------------------------------------------------------------
| Author : 이현주
| Create-Date : 2014-07-10
|------------------------------------------------------------------------
*/

Class Model_client extends MY_Model {


	public function saveClient($data) {

		$company=$data['company'];
		$company_no=$data['company_no'];
		$channel=$data['channel'];
		$email=$data['email'];
		$tel=$data['tel'];
		$cel=$data['cel'];
		$fax=$data['fax'];
		$zipcode=$data['zipcode'];
		$address=$data['address'];
		$address_detail=$data['address_detail'];
		$key =CKEY;

		$sql = "insert into client (company,company_no,channel,email,tel,cel,fax,zipcode,address,address_detail,ins_date,upd_date)
				values (?,HEX(AES_ENCRYPT(?, MD5('$key'))),?,HEX(AES_ENCRYPT(?, MD5('$key'))),HEX(AES_ENCRYPT(?, MD5('$key'))),HEX(AES_ENCRYPT(?, MD5('$key'))),?,?,HEX(AES_ENCRYPT(?, MD5('$key'))),HEX(AES_ENCRYPT(?, MD5('$key'))),NOW(),NOW())
		";

		$this->db->trans_begin();
		$this->db->query($sql
								,array(
									 $company
									 ,$company_no
									 ,$channel
									 ,$email
									 ,$tel
									 ,$cel
									 ,$fax
									 ,$zipcode
									 ,$address
									 ,$address_detail
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
	public function modifyClient($data) {

		$client_seq=$data['client_seq'];
		$company=$data['company'];
		$company_no=$data['company_no'];
		$channel=$data['channel'];
		$email=$data['email'];
		$tel=$data['tel'];
		$cel=$data['cel'];
		$fax=$data['fax'];
		$zipcode=$data['zipcode'];
		$address=$data['address'];
		$address_detail=$data['address_detail'];
		$close_yn=$data['close_yn'];
		$key =CKEY;


		$sql = "update client set company=?,company_no=HEX(AES_ENCRYPT(?, MD5('$key'))),
				channel=?,
				email=HEX(AES_ENCRYPT(?, MD5('$key'))),
				tel=HEX(AES_ENCRYPT(?, MD5('$key'))),
				cel=HEX(AES_ENCRYPT(?, MD5('$key'))),
				fax=?,
				zipcode=?,
				address=HEX(AES_ENCRYPT(?, MD5('$key'))),
				address_detail=HEX(AES_ENCRYPT(?, MD5('$key'))),
				upd_date=NOW(),
				close_yn=?
				where client_seq=?
		";

		$this->db->trans_begin();
		$this->db->query($sql
								,array(
									 $company
									 ,$company_no
									 ,$channel
									 ,$email
									 ,$tel
									 ,$cel
									 ,$fax
									 ,$zipcode
									 ,$address
									 ,$address_detail
									 ,$close_yn
									 ,$client_seq
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
	public function listClient_count($data) {
		$clinetNm=$data['clinetNm'];
		$sql = "select count(*) cnt
				from client a
				where 0=0
				 ";
		if($clinetNm!=""){
			$sql.= " and a.company like '%$clinetNm%' ";
		}
		return $this->db->query($sql)->row()->cnt;

	}
	public function listClient($data) {
		$page_num=(int)$data['page_num'];
		$page_size=(int)$data['page_size'];
		$clinetNm=$data['clinetNm'];
		$page_no=($page_num-1)*$page_size;
		$key =CKEY;

		$sql = "select a.*,
			AES_DECRYPT(UNHEX(a.company_no), MD5('$key'))as company_no,
			AES_DECRYPT(UNHEX(a.cel), MD5('$key'))as cel,
			AES_DECRYPT(UNHEX(a.tel), MD5('$key'))as tel,
			AES_DECRYPT(UNHEX(a.email), MD5('$key'))as email,
			AES_DECRYPT(UNHEX(a.address), MD5('$key'))as address,
			AES_DECRYPT(UNHEX(a.address_detail), MD5('$key'))as address_detail,
			(select count(member_seq) from member where client_seq=a.client_seq and nop_yn='N') channel_cnt
			from client a
			where 0=0
			";
		if($clinetNm!=""){
			$sql.= " and a.company like '%$clinetNm%' ";
		}
		$sql .=	"order by close_yn,company limit ?,?";



		return $this->db->query($sql
									,array(
											$page_no
											,$page_size
											)
								)->result();

	}

	public function viewClient($data) {
		$client_seq=$data['client_seq'];
		$key =CKEY;
		$sql = "select a.*,
			AES_DECRYPT(UNHEX(a.company_no), MD5('$key'))as company_no,
			AES_DECRYPT(UNHEX(a.cel), MD5('$key'))as cel,
			AES_DECRYPT(UNHEX(a.tel), MD5('$key'))as tel,
			AES_DECRYPT(UNHEX(a.email), MD5('$key'))as email,
			AES_DECRYPT(UNHEX(a.address), MD5('$key'))as address,
			AES_DECRYPT(UNHEX(a.address_detail), MD5('$key'))as address_detail
			from client a
			where client_seq=?";

		return $this->db->query($sql
									,array(
											$client_seq
											)
								)->row();

	}

	public function removeClient($data) {

		$client_seq=$data['client_seq'];

		$sql = "delete from client  where client_seq=? ";

		$this->db->trans_begin();
		$this->db->query($sql
								,array(
									 $client_seq
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
	public function listClientAll($client_seq="") {


		if($client_seq!=""){
			$sql = "select a.*,LPAD(client_seq,6,'0') client_seq_6
				from client a
				where client_seq=?";
			return $this->db->query($sql,array($client_seq))->row();
		}else{
			$sql = "select a.*,LPAD(client_seq,6,'0') client_seq_6
				from client a
				where 0=0 and close_yn='N' order by company";
			return $this->db->query($sql)->result();
		}

	}
	public function viewClinet($client_seq) {

		$sql = "select * from client where client_seq ='$client_seq'";

		return $this->db->query($sql)->row();

	}

	public function listClientList($data) {

		$clientNm=$data['clientNm'];


		$sql = "select *
			from client
			where 0=0 and close_yn='N'";

		if($clientNm!=""){
			$sql.= " and company like '%$clientNm%' ";
		}
		$sql.= "  order by company";


		return $this->db->query($sql)->result();


	}


}
?>