<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
|------------------------------------------------------------------------
| Author : 이현주
| Create-Date : 2014-07-10
|------------------------------------------------------------------------
*/

Class Model_member extends MY_Model {


	public function login_check($data) {
		$member_id=$data['member_id'];
		$member_pw=$data['member_pw'];
		$key =CKEY;
		$sql = "select *,ifnull(auth_seq,0)
			,case when a.nop_yn='Y' then b.duty
			else c.company
			end company
			,AES_DECRYPT(UNHEX(a.calendar_id), MD5('$key')) AS calendar_id
			from member  a
			left join duty b on a.duty_seq=b.duty_seq
			left join client c on a.client_seq=c.client_seq
			where member_id=? and member_pw=SHA1(?) and del_yn='N' and work_yn='Y' ";

		return $this->db->query($sql
									,array(
											$member_id
											,$member_pw
											)
										)->row();

	}
	public function saveMember($data) {

		$member_name=$data['member_name'];
		$member_id=$data['member_id'];
		$member_pw=$data['member_pw'];
		$email=$data['email'];
		$tel=$data['tel'];
		$inner_tel=$data['inner_tel'];
		$cel=$data['cel'];
		$fax=$data['fax'];
		$zipcode=$data['zipcode'];
		$address=$data['address'];
		$address_detail=$data['address_detail'];
		$degree_seq=$data['degree_seq'];
		$position_seq=$data['position_seq'];
		$duty_seq=$data['duty_seq'];
		$auth_seq=$data['auth_seq'];
		$nop_yn=$data['nop_yn'];
		$join_date=$data['join_date'];
		$card_no=$data['card_no'];
		$key =CKEY;

		$sql = "insert into member (member_name,member_id,member_pw,card_no,email,tel,inner_tel,cel,fax,zipcode,address,address_detail,degree_seq,position_seq,duty_seq,auth_seq,nop_yn,ins_date,upd_date,join_date)
				values (?,?,SHA1(?),?,HEX(AES_ENCRYPT(?, MD5('$key'))),HEX(AES_ENCRYPT(?, MD5('$key'))),HEX(AES_ENCRYPT(?, MD5('$key'))),HEX(AES_ENCRYPT(?, MD5('$key'))),?,?,HEX(AES_ENCRYPT(?, MD5('$key'))),HEX(AES_ENCRYPT(?, MD5('$key'))),?,?,?,?,?,NOW(),NOW(),?)
		";

		$this->db->trans_begin();
		$this->db->query($sql
								,array(
									 $member_name
									 ,$member_id
									 ,$member_pw
									 ,$card_no
									 ,$email
									 ,$tel
									 ,$inner_tel
									 ,$cel
									 ,$fax
									 ,$zipcode
									 ,$address
									 ,$address_detail
									 ,$degree_seq
									 ,$position_seq
									 ,$duty_seq
									 ,$auth_seq
									 ,$nop_yn
									 ,$join_date
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
	public function modifyMember($data) {

		$member_seq=$data['member_seq'];
		$member_name=$data['member_name'];
		$member_id=$data['member_id'];
		$member_pw=$data['member_pw'];
		$email=$data['email'];
		$tel=$data['tel'];
		$inner_tel=$data['inner_tel'];
		$cel=$data['cel'];
		$fax=$data['fax'];
		$zipcode=$data['zipcode'];
		$address=$data['address'];
		$address_detail=$data['address_detail'];
		$degree_seq=$data['degree_seq'];
		$position_seq=$data['position_seq'];
		$duty_seq=$data['duty_seq'];
		$auth_seq=$data['auth_seq'];
		$nop_yn=$data['nop_yn'];
		$workGubun=$data['workGubun'];
		$join_date=$data['join_date'];
		$resign_date=$data['resign_date'];
		$card_no=$data['card_no'];

		$key =CKEY;


		if($member_pw!=""){
			if($card_no!=""){
				$sql = "update member set member_name=?,member_id=?,card_no=?,member_pw=SHA1(?),email=HEX(AES_ENCRYPT(?, MD5('$key'))),tel=HEX(AES_ENCRYPT(?, MD5('$key'))),inner_tel=HEX(AES_ENCRYPT(?, MD5('$key'))),cel=HEX(AES_ENCRYPT(?, MD5('$key'))),fax=?,zipcode=?,address=HEX(AES_ENCRYPT(?, MD5('$key'))),address_detail=HEX(AES_ENCRYPT(?, MD5('$key'))),degree_seq=?,position_seq=?,duty_seq=?,auth_seq=?,work_yn=?,upd_date=NOW()
						,join_date=?,resign_date=?
						where member_seq=?
				";

				$this->db->trans_begin();
				$this->db->query($sql
										,array(
											 $member_name
											 ,$member_id
											 ,$card_no
											 ,$member_pw
											 ,$email
											 ,$tel
											 ,$inner_tel
											 ,$cel
											 ,$fax
											 ,$zipcode
											 ,$address
											 ,$address_detail
											 ,$degree_seq
											 ,$position_seq
											 ,$duty_seq
											 ,$auth_seq
											 ,$workGubun
											 ,$join_date
											 ,$resign_date
											 ,$member_seq
											)
										);
			}else{
				$sql = "update member set member_name=?,member_id=?,member_pw=SHA1(?),email=HEX(AES_ENCRYPT(?, MD5('$key'))),tel=HEX(AES_ENCRYPT(?, MD5('$key'))),inner_tel=HEX(AES_ENCRYPT(?, MD5('$key'))),cel=HEX(AES_ENCRYPT(?, MD5('$key'))),fax=?,zipcode=?,address=HEX(AES_ENCRYPT(?, MD5('$key'))),address_detail=HEX(AES_ENCRYPT(?, MD5('$key'))),degree_seq=?,position_seq=?,duty_seq=?,auth_seq=?,work_yn=?,upd_date=NOW()
						,join_date=?,resign_date=?
						where member_seq=?
				";

				$this->db->trans_begin();
				$this->db->query($sql
										,array(
											 $member_name
											 ,$member_id
											 ,$member_pw
											 ,$email
											 ,$tel
											 ,$inner_tel
											 ,$cel
											 ,$fax
											 ,$zipcode
											 ,$address
											 ,$address_detail
											 ,$degree_seq
											 ,$position_seq
											 ,$duty_seq
											 ,$auth_seq
											 ,$workGubun
											 ,$join_date
											 ,$resign_date
											 ,$member_seq
											)
										);
			}



		}else{
			if($card_no!=""){
				$sql = "update member set member_name=?,member_id=?,card_no=?,email=HEX(AES_ENCRYPT(?, MD5('$key'))),tel=HEX(AES_ENCRYPT(?, MD5('$key'))),inner_tel=HEX(AES_ENCRYPT(?, MD5('$key'))),cel=HEX(AES_ENCRYPT(?, MD5('$key'))),fax=?,zipcode=?,address=HEX(AES_ENCRYPT(?, MD5('$key'))),address_detail=HEX(AES_ENCRYPT(?, MD5('$key'))),degree_seq=?,position_seq=?,duty_seq=?,auth_seq=?,work_yn=?,upd_date=NOW(),join_date=?,resign_date=?
						where member_seq=?
				";

				$this->db->trans_begin();
				$this->db->query($sql
										,array(
											 $member_name
											 ,$member_id
											 ,$card_no
											 ,$email
											 ,$tel
											 ,$inner_tel
											 ,$cel
											 ,$fax
											 ,$zipcode
											 ,$address
											 ,$address_detail
											 ,$degree_seq
											 ,$position_seq
											 ,$duty_seq
											 ,$auth_seq
											 ,$workGubun
											 ,$join_date
											 ,$resign_date
											 ,$member_seq
											)
										);
			}else{
				$sql = "update member set member_name=?,member_id=?,email=HEX(AES_ENCRYPT(?, MD5('$key'))),tel=HEX(AES_ENCRYPT(?, MD5('$key'))),inner_tel=HEX(AES_ENCRYPT(?, MD5('$key'))),cel=HEX(AES_ENCRYPT(?, MD5('$key'))),fax=?,zipcode=?,address=HEX(AES_ENCRYPT(?, MD5('$key'))),address_detail=HEX(AES_ENCRYPT(?, MD5('$key'))),degree_seq=?,position_seq=?,duty_seq=?,auth_seq=?,work_yn=?,upd_date=NOW(),join_date=?,resign_date=?
						where member_seq=?
				";

				$this->db->trans_begin();
				$this->db->query($sql
										,array(
											 $member_name
											 ,$member_id
											 ,$email
											 ,$tel
											 ,$inner_tel
											 ,$cel
											 ,$fax
											 ,$zipcode
											 ,$address
											 ,$address_detail
											 ,$degree_seq
											 ,$position_seq
											 ,$duty_seq
											 ,$auth_seq
											 ,$workGubun
											 ,$join_date
											 ,$resign_date
											 ,$member_seq
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
	public function listMember_count($data) {
		$nop_yn=$data['nop_yn'];
		$client_seq=$data['client_seq'];
		$memberNm=$data['memberNm'];
		$memberId=$data['memberId'];

		if (isset($data['workGubun'])){
			$workGubun=$data['workGubun'];
		}
		$sql = "select count(*) cnt
				from member a
				left join degree b on a.degree_seq=b.degree_seq
				left join duty c on a.duty_seq=c.duty_seq
				left join auth d on a.auth_seq=d.auth_seq
				left join client e on a.client_seq=e.client_seq
				left join position f on a.position_seq=f.position_seq
				where a.del_yn='N' and nop_yn=? ";
		if (isset($data['workGubun'])){
			if($workGubun !="A" ){
				$sql.= " and work_yn='$workGubun' ";
			}
		}
		if($client_seq!=""){
			$sql.= " and a.client_seq='$client_seq' ";
		}
		if($memberNm!=""){
			$sql.= " and a.member_name like '%$memberNm%' ";
		}
		if($memberId!=""){
			$sql.= " and a.member_id like '%$memberId%' ";
		}
		return $this->db->query($sql
									,array(
											$nop_yn
										)
								)->row()->cnt;

	}
	public function listMember($data) {
		$nop_yn=$data['nop_yn'];
		$client_seq=$data['client_seq'];

		if (isset($data['workGubun'])){
			$workGubun=$data['workGubun'];
		}
		$page_num=(int)$data['page_num'];
		$page_size=(int)$data['page_size'];
		$memberNm=$data['memberNm'];
		$memberId=$data['memberId'];
		$page_no=($page_num-1)*$page_size;
		$key =CKEY;

		$sql = "select a.*,
			AES_DECRYPT(UNHEX(a.tel), MD5('$key'))as tel,
			AES_DECRYPT(UNHEX(a.inner_tel), MD5('$key'))as inner_tel,
			AES_DECRYPT(UNHEX(a.cel), MD5('$key'))as cel,
			AES_DECRYPT(UNHEX(a.email), MD5('$key'))as email,
			AES_DECRYPT(UNHEX(a.address), MD5('$key'))as address,
			AES_DECRYPT(UNHEX(a.address_detail), MD5('$key'))as address_detail,
			b.degree,c.duty,d.auth,e.company,f.position
			from member a
			left join degree b on a.degree_seq=b.degree_seq
			left join duty c on a.duty_seq=c.duty_seq
			left join auth d on a.auth_seq=d.auth_seq
			left join client e on a.client_seq=e.client_seq
			left join position f on a.position_seq=f.position_seq
			where a.del_yn='N' and nop_yn=? ";

		if (isset($data['workGubun'])){
			if($workGubun !="A" ){
				$sql.= " and work_yn='$workGubun' ";
			}
		}
		if($client_seq !=""){
			$sql.= " and a.client_seq='$client_seq' ";
		}
		if($memberNm !=""){
			$sql.= " and a.member_name like '%$memberNm%' ";
		}
		if($memberId !=""){
			$sql.= " and a.member_id like '%$memberId%' ";
		}
		$sql .=	"order by a.degree_seq,a.member_name limit ?,?";

		return $this->db->query($sql
									,array(
											$nop_yn
											,$page_no
											,$page_size
										)
								)->result();

	}

	public function viewMember($data) {
		$member_seq=$data['member_seq'];
		$key =CKEY;
		$sql = "select a.*,
			AES_DECRYPT(UNHEX(a.tel), MD5('$key'))as tel,
			AES_DECRYPT(UNHEX(a.inner_tel), MD5('$key'))as inner_tel,
			AES_DECRYPT(UNHEX(a.cel), MD5('$key'))as cel,
			AES_DECRYPT(UNHEX(a.email), MD5('$key'))as email,
			AES_DECRYPT(UNHEX(a.address), MD5('$key'))as address,
			AES_DECRYPT(UNHEX(a.address_detail), MD5('$key'))as address_detail,
			AES_DECRYPT(UNHEX(a.calendar_id), MD5('$key'))as calendar_id,
			b.degree,c.duty,d.auth,e.company,f.position
			from member a
			left join degree b on a.degree_seq=b.degree_seq
			left join duty c on a.duty_seq=c.duty_seq
			left join auth d on a.auth_seq=d.auth_seq
			left join client e on a.client_seq=e.client_seq
			left join position f on a.position_seq=f.position_seq

			where a.del_yn='N' and member_seq=?";
		return $this->db->query($sql
									,array(
											$member_seq
											)
								)->row();

	}

	public function removeMember($data) {

		$member_seq=$data['member_seq'];

		$sql = "update member set del_yn='Y' where member_seq=? ";

		$this->db->trans_begin();
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

	public function saveChannel($data) {

		$member_name=$data['member_name'];
		$member_id=$data['member_id'];
		$member_pw=$data['member_pw'];
		$email=$data['email'];
		$tel=$data['tel'];
		$cel=$data['cel'];
		$fax=$data['fax'];
		$zipcode=$data['zipcode'];
		$address=$data['address'];
		$address_detail=$data['address_detail'];

		$client_degree=$data['client_degree'];
		$client_pos=$data['client_pos'];
		$client_seq=$data['client_seq'];
		$nop_yn=$data['nop_yn'];
		$key =CKEY;

		$sql = "insert into member (member_name,member_id,member_pw,email,tel,cel,fax,zipcode,address,address_detail,client_degree,client_pos,client_seq,nop_yn,auth_seq,ins_date,upd_date)
				values (?,?,SHA1(?),HEX(AES_ENCRYPT(?, MD5('$key'))),
									HEX(AES_ENCRYPT(?, MD5('$key'))),
									HEX(AES_ENCRYPT(?, MD5('$key'))),
									?,
									?,
									HEX(AES_ENCRYPT(?, MD5('$key'))),
									HEX(AES_ENCRYPT(?, MD5('$key'))),?,?,?,?,'5',NOW(),NOW())
		";

		$this->db->trans_begin();
		$this->db->query($sql
								,array(
									 $member_name
									 ,$member_id
									 ,$member_pw
									 ,$email
									 ,$tel
									 ,$cel
									 ,$fax
									 ,$zipcode
									 ,$address
									 ,$address_detail

									 ,$client_degree
									 ,$client_pos
									 ,$client_seq
									 ,$nop_yn
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
	public function modifyChannel($data) {

		$member_seq=$data['member_seq'];
		$member_name=$data['member_name'];
		$member_id=$data['member_id'];
		$member_pw=$data['member_pw'];
		$email=$data['email'];
		$tel=$data['tel'];
		$cel=$data['cel'];
		$fax=$data['fax'];
		$zipcode=$data['zipcode'];
		$address=$data['address'];
		$address_detail=$data['address_detail'];
		$client_degree=$data['client_degree'];
		$client_pos=$data['client_pos'];
		$client_seq=$data['client_seq'];
		$nop_yn=$data['nop_yn'];
		$key =CKEY;


		if($member_pw!=""){
			$sql = "update member set member_name=?,member_id=?,member_pw=SHA1(?),
					email=HEX(AES_ENCRYPT(?, MD5('$key'))),
					tel=HEX(AES_ENCRYPT(?, MD5('$key'))),
					cel=HEX(AES_ENCRYPT(?, MD5('$key'))),
					fax=?,
					zipcode=?,
					address=HEX(AES_ENCRYPT(?, MD5('$key'))),
					address_detail=HEX(AES_ENCRYPT(?, MD5('$key'))),
					client_degree=?,
					client_pos=?,
					client_seq=?,
					upd_date=NOW()
					where member_seq=?
			";

			$this->db->trans_begin();
			$this->db->query($sql
									,array(
										 $member_name
										 ,$member_id
										 ,$member_pw
										 ,$email
										 ,$tel
										 ,$cel
										 ,$fax
										 ,$zipcode
										 ,$address
										 ,$address_detail
										 ,$client_degree
									 	 ,$client_pos
										 ,$client_seq
										 ,$member_seq
										)
									);


		}else{
			$sql = "update member set member_name=?,member_id=?,
					email=HEX(AES_ENCRYPT(?, MD5('$key'))),
					tel=HEX(AES_ENCRYPT(?, MD5('$key'))),
					cel=HEX(AES_ENCRYPT(?, MD5('$key'))),
					fax=?,
					zipcode=?,
					address=HEX(AES_ENCRYPT(?, MD5('$key'))),
					address_detail=HEX(AES_ENCRYPT(?, MD5('$key'))),
					client_degree=?,
					client_pos=?,
					client_seq=?,
					upd_date=NOW()
					where member_seq=?
			";

			$this->db->trans_begin();
			$this->db->query($sql
									,array(
										 $member_name
										 ,$member_id
										 ,$email
										 ,$tel
										 ,$cel
										 ,$fax
										 ,$zipcode
										 ,$address
										 ,$address_detail
										 ,$client_degree
									 	 ,$client_pos
										 ,$client_seq
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

	public function listMemberAll($data) {
		$nop_yn=$data['nop_yn'];
		$client_seq=$data['client_seq'];


		$sql = "select a.*
			from member a
			where del_yn='N' ";
		if($nop_yn!=""){
			$sql.= " and nop_yn=? ";
		}
		if($client_seq!=""){
			$sql.= " and a.client_seq='$client_seq' ";
		}else{
			$sql.= " and 0=1 ";
		}
		$sql .=	"order by a.degree_seq";

		return $this->db->query($sql
									,array(
											$nop_yn
										)
								)->result();

	}

	public function listMemberMail($data) {
		$nop_yn=$data['nop_yn'];
		$duty_seq=$data['duty_seq'];
		$client_seq=$data['client_seq'];



		$sql = "select a.*,b.company,c.duty
			from member a
			left join client b on a.client_seq=b.client_seq
			left join duty c on a.duty_seq=c.duty_seq
			where a.del_yn='N' ";
		if($nop_yn!=""){
			$sql.= " and a.nop_yn=? ";
		}
		if($nop_yn=="Y"){
			$sql.= " and a.work_yn='Y' ";
		}
		if($duty_seq!="" || $client_seq!=""){
			if($duty_seq!="" && $client_seq==""){
				$sql.= " and a.duty_seq in ($duty_seq) ";
			}else if($duty_seq=="" && $client_seq!=""){
				$sql.= " and a.client_seq in ($client_seq) ";
			}else if($duty_seq!="" && $client_seq!=""){
				$sql.= " and (a.duty_seq in ($duty_seq) or a.client_seq in ($client_seq)) ";
			}
		}else{
			$sql.= " and 0=1 ";
		}
		$sql .=	" order by a.degree_seq";




		return $this->db->query($sql
									,array(
											$nop_yn
										)
								)->result();

	}

	public function listMember_all($data) {
		$member_seq_receive=$data['member_seq_receive'];
		$member_seq=$data['member_seq'];

		if($this->nop_yn=="Y"){
			$sql = "SELECT * FROM (
					SELECT a.member_seq,a.member_id,a.member_name,a.nop_yn,'".HNAME."' company,c.duty
					FROM member a
					LEFT JOIN duty c ON a.duty_seq=c.duty_seq
					WHERE a.del_yn='N'  AND a.work_yn='Y' AND a.nop_yn='Y'

					UNION ALL

					SELECT DISTINCT b.member_seq,b.member_id,b.member_name,b.nop_yn,c.company,d.duty
					FROM project_member a
					JOIN member b ON a.member_seq=b.member_seq
					LEFT JOIN client c ON b.client_seq=c.client_seq
					LEFT JOIN duty d ON b.duty_seq=d.duty_seq
					JOIN project e ON a.project_seq=e.project_seq
					WHERE a.project_seq IN (SELECT project_seq FROM project_member WHERE member_seq='$member_seq') AND b.nop_yn='N' AND e.finish_yn='N' AND e.del_yn='N'

				) a WHERE 0=0 ";
				if($member_seq_receive!=""){
					$sql .=	" and member_seq='$member_seq_receive' ";
				}
				$sql .=	" order by a.nop_yn DESC,a.member_name,a.member_id";
		}else{
			$sql = "SELECT DISTINCT b.member_seq,b.member_id,b.member_name,b.nop_yn,c.company,d.duty
					FROM project_member a
					JOIN member b ON a.member_seq=b.member_seq
					LEFT JOIN client c ON b.client_seq=c.client_seq
					LEFT JOIN duty d ON b.duty_seq=d.duty_seq
					WHERE a.project_seq IN (SELECT project_seq FROM project_member WHERE member_seq='$member_seq')";
					if($member_seq_receive!=""){
						$sql .=	" and b.member_seq='$member_seq_receive' ";
					}
					$sql .=	" order by b.member_name,b.member_id";
		}

		//echo $sql;

		return $this->db->query($sql)->result();

	}


	public function modifyPersMember($data) {

		$member_seq=$data['member_seq'];
		$member_name=$data['member_name'];
		$member_id=$data['member_id'];
		$member_pw=$data['member_pw'];
		$email=$data['email'];
		$tel=$data['tel'];
		$inner_tel=$data['inner_tel'];
		$cel=$data['cel'];
		$fax=$data['fax'];
		$zipcode=$data['zipcode'];
		$address=$data['address'];
		$address_detail=$data['address_detail'];
		$calendar_id=$data['calendar_id'];

		$nop_yn=$data['nop_yn'];

		$key =CKEY;


		if($member_pw!=""){
			$sql = "update member set member_name=?,member_id=?,member_pw=SHA1(?),email=HEX(AES_ENCRYPT(?, MD5('$key'))),tel=HEX(AES_ENCRYPT(?, MD5('$key'))),inner_tel=HEX(AES_ENCRYPT(?, MD5('$key'))),cel=HEX(AES_ENCRYPT(?, MD5('$key'))),fax=?,zipcode=?,address=HEX(AES_ENCRYPT(?, MD5('$key'))),address_detail=HEX(AES_ENCRYPT(?, MD5('$key'))),calendar_id=HEX(AES_ENCRYPT(?, MD5('$key'))),upd_date=NOW()

					where member_seq=?
			";

			$this->db->trans_begin();
			$this->db->query($sql
									,array(
										 $member_name
										 ,$member_id
										 ,$member_pw
										 ,$email
										 ,$tel
										 ,$inner_tel
										 ,$cel
										 ,$fax
										 ,$zipcode
										 ,$address
										 ,$address_detail
 										 ,$calendar_id
										 ,$member_seq
										)
									);


		}else{
			$sql = "update member set member_name=?,member_id=?,email=HEX(AES_ENCRYPT(?, MD5('$key'))),tel=HEX(AES_ENCRYPT(?, MD5('$key'))),inner_tel=HEX(AES_ENCRYPT(?, MD5('$key'))),cel=HEX(AES_ENCRYPT(?, MD5('$key'))),fax=?,zipcode=?,address=HEX(AES_ENCRYPT(?, MD5('$key'))),address_detail=HEX(AES_ENCRYPT(?, MD5('$key'))),calendar_id=HEX(AES_ENCRYPT(?, MD5('$key'))),upd_date=NOW()
					where member_seq=?
			";

			$this->db->trans_begin();
			$this->db->query($sql
									,array(
										 $member_name
										 ,$member_id
										 ,$email
										 ,$tel
										 ,$inner_tel
										 ,$cel
										 ,$fax
										 ,$zipcode
										 ,$address
										 ,$address_detail
										 ,$calendar_id
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

	public function listMember_employee() {

		$key =CKEY;

		$sql = "SELECT a.*,
			AES_DECRYPT(UNHEX(a.tel), MD5('$key'))AS tel,
			AES_DECRYPT(UNHEX(a.inner_tel), MD5('$key'))AS inner_tel,
			AES_DECRYPT(UNHEX(a.cel), MD5('$key'))AS cel,
			AES_DECRYPT(UNHEX(a.email), MD5('$key'))AS email,
			AES_DECRYPT(UNHEX(a.address), MD5('$key'))AS address,
			AES_DECRYPT(UNHEX(a.address_detail), MD5('$key'))AS address_detail,
			b.degree,c.duty,d.auth,e.company,f.position
			,(SELECT COUNT(1) FROM member WHERE duty_seq=a.duty_seq AND del_yn='N' AND nop_yn='Y' AND work_yn='Y') duty_num
			FROM member a
			LEFT JOIN degree b ON a.degree_seq=b.degree_seq
			LEFT JOIN duty c ON a.duty_seq=c.duty_seq
			LEFT JOIN auth d ON a.auth_seq=d.auth_seq
			LEFT JOIN client e ON a.client_seq=e.client_seq
			LEFT JOIN position f ON a.position_seq=f.position_seq
			WHERE a.del_yn='N' AND nop_yn='Y' AND work_yn='Y'
			ORDER BY c.order_no,b.order_no,f.order_no DESC,a.member_name";

		//echo $sql;

		return $this->db->query($sql)->result();

	}
	public function listMember_duty($data) {


		$duty_seq=$data['duty_seq'];
		$member_seq=$data['member_seq'];

		$sql = "SELECT a.member_seq,a.member_id,a.member_name,a.join_date,b.degree,c.duty,d.auth,e.company,f.position
				FROM member a
				LEFT JOIN degree b ON a.degree_seq=b.degree_seq
				LEFT JOIN duty c ON a.duty_seq=c.duty_seq
				LEFT JOIN auth d ON a.auth_seq=d.auth_seq
				LEFT JOIN client e ON a.client_seq=e.client_seq
				LEFT JOIN position f ON a.position_seq=f.position_seq
				WHERE a.del_yn='N' AND a.nop_yn='Y' AND a.work_yn='Y' AND a.duty_seq>1 ";

		if($duty_seq!="0"){
			$sql.="	AND a.duty_seq='$duty_seq'";
		}
		if($member_seq!=""){
			$sql.="	AND a.member_seq='$member_seq'";
		}
		$sql.=" ORDER BY c.order_no,b.order_no,f.order_no DESC,a.member_name";
		//echo $sql;
		return $this->db->query($sql)->result();

	}

	public function listMember_holiday() {

		$key =CKEY;

		$time=strtotime(date('Y-m-d'));
		$preyear= substr(date("Y-m-d",strtotime("-1 year", $time)),0,4);
		$thisyear=date('Y');

		$sql = "SELECT a.*
				,c.duty,IFNULL(b.pre_holiday,0) pre_holiday
				,(SELECT COUNT(1) FROM member WHERE duty_seq=a.duty_seq AND del_yn='N' AND nop_yn='Y' AND work_yn='Y' and member_seq!='242'  and member_seq!='237') duty_num
				,ifnull((SELECT SUM(use_holiday) FROM goleave WHERE member_seq=a.member_seq AND SUBSTRING(t_date,1,4)='$thisyear'),0) use_holiday
				,IFNULL((SELECT SUM(late_holiday) FROM goleave_sum WHERE member_seq=a.member_seq AND SUBSTRING(date_ym,1,4)='$thisyear'),0) late_holiday
				,d.degree
				,e.position
				FROM member a
				LEFT JOIN holiday b ON a.member_seq=b.member_seq AND b.date_y='$preyear'
				LEFT JOIN duty c ON a.duty_seq=c.duty_seq
				LEFT JOIN degree d ON a.degree_seq=d.degree_seq
				LEFT JOIN position e ON a.position_seq=e.position_seq


				WHERE a.del_yn='N' AND a.nop_yn='Y' AND a.work_yn='Y' and a.member_seq!='242'  and a.member_seq!='237'
				ORDER BY c.order_no,d.order_no,a.member_name";

		//echo $sql;

		return $this->db->query($sql)->result();

	}

	public function viewMember_holiday($data) {

		$time=strtotime(date('Y-m-d'));
		$preyear= substr(date("Y-m-d",strtotime("-1 year", $time)),0,4);
		$thisyear=date('Y');
		$member_seq=$data['member_seq'];


		$sql = "SELECT a.join_date
				,IFNULL(b.pre_holiday,0) pre_holiday
				,ifnull((SELECT SUM(use_holiday) FROM goleave WHERE member_seq=a.member_seq AND SUBSTRING(t_date,1,4)='$thisyear'),0) use_holiday
				,IFNULL((SELECT SUM(late_holiday) FROM goleave_sum WHERE member_seq=a.member_seq AND SUBSTRING(date_ym,1,4)='$thisyear'),0) late_holiday
				FROM member a
				LEFT JOIN holiday b ON a.member_seq=b.member_seq AND b.date_y='$preyear'
				LEFT JOIN duty c ON a.duty_seq=c.duty_seq
				LEFT JOIN degree d ON a.degree_seq=d.degree_seq

				WHERE a.del_yn='N' AND nop_yn='Y' AND work_yn='Y' and a.member_seq='$member_seq'
				";

		//echo $sql;

		return $this->db->query($sql)->row();

	}

	public function listMember_hodiday_duty($data) {


		$duty_seq=$data['duty_seq'];
		$member_seq=$data['member_seq'];

		$sql = "SELECT a.member_seq,a.member_id,a.member_name,b.degree
				FROM member a
				LEFT JOIN degree b ON a.degree_seq=b.degree_seq
				WHERE a.del_yn='N' AND a.nop_yn='Y' AND a.work_yn='Y' AND a.duty_seq>1 ";

		if($duty_seq!="0"){
			$sql.="	AND a.duty_seq='$duty_seq'";
		}
		if($member_seq!=""){
			//$sql.="	AND a.member_seq!='$member_seq'";

		}
		//$sql.="	AND a.member_seq!='235'";

		//echo $sql;
		return $this->db->query($sql)->result();

	}

	public function listMember_project($data) {


		$duty_seq=$data['duty_seq'];
		$member_seq=$data['member_seq'];

		$sql = "SELECT b.member_seq,b.member_id,b.member_name,c.project
				FROM project_member a
				JOIN member b ON a.member_seq=b.member_seq
				JOIN project c ON a.project_seq=c.project_seq
				WHERE a.project_seq IN (
					SELECT a.project_seq
						FROM project_member a
						JOIN project b ON a.project_seq=b.project_seq
						WHERE a.member_seq='$member_seq'
						AND b.finish_yn='N' AND b.del_yn='N'

				) AND roll_seq='1'
				AND b.del_yn='N' AND b.nop_yn='Y' AND b.work_yn='Y' AND b.duty_seq>1 ";

		if($member_seq=="235"){
			//$sql .=" and 0=1" ;
		}

		return $this->db->query($sql)->result();

	}


	public function viewMember_in_seq($data) {
		$member_seq=$data['seq'];
		$key =CKEY;
		$sql = "select a.*,AES_DECRYPT(UNHEX(a.tel), MD5('$key'))as tel,
			AES_DECRYPT(UNHEX(a.cel), MD5('$key'))as cel,
			AES_DECRYPT(UNHEX(a.email), MD5('$key'))as email,
			AES_DECRYPT(UNHEX(a.address), MD5('$key'))as address,
			AES_DECRYPT(UNHEX(a.address_detail), MD5('$key'))as address_detail,
			b.degree,c.duty,d.auth,e.company,f.position
			from member a
			left join degree b on a.degree_seq=b.degree_seq
			left join duty c on a.duty_seq=c.duty_seq
			left join auth d on a.auth_seq=d.auth_seq
			left join client e on a.client_seq=e.client_seq
			left join position f on a.position_seq=f.position_seq

			where a.del_yn='N' and member_seq in ($member_seq)";

		return $this->db->query($sql)->result();

	}

	public function viewMembername_in_seq($data) {
		$member_seq=$data['seq'];
		if($member_seq==""){
			$member_seq="0";
		}
		$key =CKEY;
		$sql = "SELECT GROUP_CONCAT(CONCAT(member_name,'(',company,')')) member_names FROM (
					SELECT a.member_name,CASE WHEN e.company IS NULL THEN '".HNAME."' ELSE e.company END company
					FROM member a
					LEFT JOIN client e ON a.client_seq=e.client_seq
					LEFT JOIN position f ON a.position_seq=f.position_seq
					WHERE a.del_yn='N' AND member_seq IN ($member_seq)
				) tm";

		//echo $sql;
		return $this->db->query($sql)->row();

	}


	public function viewMemberMail_in_seq($data) {
		$member_seq=$data['member_seq'];
		$client_seq=$data['client_seq'];
		$key =CKEY;

		$sql = "select a.*,AES_DECRYPT(UNHEX(a.tel), MD5('$key'))as tel,
			AES_DECRYPT(UNHEX(a.cel), MD5('$key'))as cel,
			AES_DECRYPT(UNHEX(a.email), MD5('$key'))as email,
			AES_DECRYPT(UNHEX(a.address), MD5('$key'))as address,
			AES_DECRYPT(UNHEX(a.address_detail), MD5('$key'))as address_detail,
			b.degree,c.duty,d.auth,e.company,f.position
			from member a
			left join degree b on a.degree_seq=b.degree_seq
			left join duty c on a.duty_seq=c.duty_seq
			left join auth d on a.auth_seq=d.auth_seq
			left join client e on a.client_seq=e.client_seq
			left join position f on a.position_seq=f.position_seq

			where a.del_yn='N' ";

		if($client_seq!="0" && $client_seq!=""){
			$sql.=" and a.client_seq='$client_seq' ";
		}
		if($member_seq!=""){
			$sql.=" and member_seq in ($member_seq)";
		}
//echo $sql;
		return $this->db->query($sql,array($client_seq))->result();

	}
	public function listMemberMailSelect($data) {

		$key =CKEY;

		$sql = "select a.*
			from member a
			where a.del_yn='N' and a.nop_yn='Y' order by a.degree_seq,a.member_name ";
		//echo $sql;
		return $this->db->query($sql)->result();

	}

}
?>