<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
|------------------------------------------------------------------------
| Author : 이현주
| Create-Date : 2014-07-10
|------------------------------------------------------------------------
*/

Class Model_test extends MY_Model {


	public function saveExcel($data) {
		$id=$data['id'];
		$pw=$data['pw'];
		$gbn=$data['gbn'];
		$status=$data['status'];

		$sql = "insert into test (id,pw,gbn,status) values (?,?,?,?) ";

		$this->db->trans_begin();
		$this->db->query($sql
								,array(
									 $id
									,$pw
									,$gbn
									,$status
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