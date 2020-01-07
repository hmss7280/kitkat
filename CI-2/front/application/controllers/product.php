<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Product extends MY_Controller{
	function __construct(){
		parent::__construct();
	}

	public function index(){

		$this->prod();

	}
	public function prod($code=""){
		$noArr=['original','new3','chunky'];
		$no=0;
		for($i=0;$i<count($noArr);$i++){
			if($noArr[$i]==$code){
				$no=$i+1;
				break;
			}
		}
		//original,chunky,new3
		$this->_view('prod/view_prod',array("code"=>$code,"no"=>$no));

	}	
	public function prod_view(){

		$this->_view('prod/view_prod_view');

	}	
	public function getProductView(){
		$code=escape($this->input->post("code",true));
		$this->load->view('prod/view_prod_'.$code);
	}			
}
