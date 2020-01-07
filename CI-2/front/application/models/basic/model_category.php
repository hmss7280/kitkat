<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
|------------------------------------------------------------------------
| Author : 이현주
| Create-Date : 2014-07-10
|------------------------------------------------------------------------
*/

Class Model_category extends MY_Model {

	
	public function saveCategory($data) {
		$category=$data['category'];
		
		$sql = "insert into category (category,order_no,ins_date,upd_date) SELECT ?,count(category_seq)+1,NOW(),NOW() FROM category ";	
		
		$this->db->trans_begin();
		$this->db->query($sql
								,array(
									 $category
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
	public function modifyCategory($data) {
		$category=$data['category'];
		$order_no=$data['order_no'];
		$category_seq=$data['category_seq'];
		
		$sql = "update category set category=?,order_no=?,upd_date=NOW() where category_seq=? ";	
		
		$this->db->trans_begin();
		$this->db->query($sql
								,array(
									 $category
									 ,$order_no
									 ,$category_seq
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
	public function removeCategory($data) {

		$category_seq=$data['category_seq'];
		
		$sql = "delete from category  where category_seq=? ";	
		
		$this->db->trans_begin();
		$this->db->query($sql
								,array(
									 $category_seq
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
	public function listCategory() {
		
		$sql = "select * from category order by order_no";	
	
		return $this->db->query($sql)->result();

	}

}
?>