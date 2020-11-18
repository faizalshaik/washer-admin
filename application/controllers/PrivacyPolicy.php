<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class PrivacyPolicy extends CI_Controller {

	public function __construct()
	{
		 parent::__construct();
		 $this->load->model('User_model');
		 $this->load->model('Gener_model');		 
	}
	public function index()
	{
		$this->load->view("admin/view_privacy");
	}

	public function geners() {
		$this->logonCheck();
		$param['uri'] = 'geners';
		$param['kind'] = 'table';
		$param['table'] = 'tbl_gener';
		$this->load->view("admin/view_header", $param);	
		$this->load->view("admin/view_geners", $param);
	}	


}
