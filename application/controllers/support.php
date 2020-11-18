<?php
defined('BASEPATH') OR exit('No direct script access allowed');
include("assets/global/admin.global.php");
class support extends CI_Controller {

	public function __construct()
	{
		 parent::__construct();
		 date_default_timezone_set('America/Chicago');
	}
	public function index()
	{
		$this->load->view("privacy_policy");
	}
}
