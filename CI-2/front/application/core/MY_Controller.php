<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_Controller extends CI_Controller {
	function __construct() {
		parent::__construct();

	}
	function _view($view, $array="") {

			$this->load->view("include/top");
			$this->load->view("include/header");
			$this->load->view($view, $array);
			$this->load->view("include/footer");

	}	
}
?>