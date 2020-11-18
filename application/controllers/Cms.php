<?php
defined('BASEPATH') OR exit('No direct script access allowed');
include("assets/global/admin.global.php");
class Cms extends CI_Controller {

	public function __construct()
	{
		 parent::__construct();
		 date_default_timezone_set('America/Chicago');
		 		 
		 $this->load->model('User_model');
		 $this->load->model('Base_model');
		 $this->load->model('Machine_model');		 
		 $this->load->model('Model_model');
		 $this->load->model('Program_model');
		 $this->load->model('Define_model');
		 $this->load->model('Day_model');
		 $this->load->model('KioskIncome_model');
		 $this->load->model('Kiosk2Income_model');		 
		 $this->load->model('Transaction_model');		 
		 $this->load->model('Setting_model');
		 $this->load->model('CreditIncome_model');
		 $this->load->model('WashFold_model');
		 $this->load->model('Supply_model');		 
	}


	public function index()
	{
		if($this->logonCheck()) {
			redirect('Cms/dashboard/', 'refresh');
		} 
	}
	public function login(){
		$this->load->view("admin/view_login");
	}
	public function logout(){
		$this->session->sess_destroy();
		redirect('Cms/', 'refresh');
	}
	public function auth_user() {
		global $MYSQL;
		$email = $this->input->post('email');
		$password = $this->input->post('password');
		$conAry = array('email' => $email);
		$ret = $this->User_model->getRow($MYSQL['_adminDB'], $conAry);
		if(!empty($ret)){       
			if (password_verify($password, $ret->password)) {       
				$sess_data = array('user_id'=>$ret->Id, 'is_login'=>true);
				$this->session->set_userdata($sess_data);
				redirect('Cms/dashboard/', 'refresh');
			}
		} 
		redirect( 'Cms/login', 'refresh');
	}
	public function dashboard() {
		if($this->logonCheck()) {
			global $MYSQL;
			$param['uri'] = '';
			$param['kind'] = '';

			$this->load->view("admin/view_header", $param);	

			$data['guest_cnt'] = 0;
			$data['expert_cnt'] = 0;
			$data['user_cnt'] = 0;

			$data['quest_cnt'] = 0;
			$data['answer_cnt'] = 0;
			$data['comment_cnt'] = 0;
			$this->load->view("admin/view_dashboard", $data);
		}
	}
	public function updateAccount() {
		if($this->logonCheck()){
			global $MYSQL;
			$email = $this->input->post('email');
			$password = $this->input->post('password');
			$id = $this->session->userdata('user_id');
			$npass = password_hash($password, PASSWORD_DEFAULT);
			$updateAry = array('email'=>$email,
				'password'=>$npass,
				'modified'=>date('Y-m-d'));
			$ret = $this->User_model->updateData($MYSQL['_adminDB'], array('Id'=>$id), $updateAry);
			if($ret > 0) 
				$this->session->set_flashdata('messagePr', 'Update Account Successfully..');
			else
				$this->session->set_flashdata('messagePr', 'Unable to Update Account..');
			redirect('Cms/dashboard/', 'refresh');
		}
	}

	public function user() {
		global $MYSQL;
		if($this->logonCheck()) {
			$param['uri'] = 'user';
			$param['kind'] = 'table';
			$param['categories'] = $this->Category_model->categories();

			$data['company'] = $this->User_model->getDatas( $MYSQL['_companyDB'], array('isactive'=>'1', 'isdeleted'=>'0'));
			$this->load->view("admin/view_header", $param);	
			$this->load->view("admin/view_user", $data);
		}
	}

	public function terms() {
		if($this->logonCheck()) {
			global $MYSQL;
			$param['uri'] = 'terms';
			$param['kind'] = 'editor';
			$param['categories'] = $this->Category_model->categories();

			$ret = $this->User_model->getRow($MYSQL['_termsDB'], array('Id'=>'1'));
			$data['terms'] = $ret->terms;
			$this->load->view("admin/view_header", $param);	
			$this->load->view("admin/view_terms", $data);
		}	
	}
	
	public function models()
	{
		if(!$this->logonCheck()) return;
		$param['uri'] = 'models';
		$param['kind'] = 'table';
		$param['table'] = 'tbl_model';
		$this->load->view("admin/view_header", $param);
		$this->load->view("admin/view_models",$param);
	}

	public function washer_states()
	{
		if(!$this->logonCheck()) return;
		$param['uri'] = 'washer_states';
		$param['kind'] = '';
		$param['table'] = '';		
		$this->load->view("admin/view_header", $param);
		$this->load->view("admin/view_washer_states",$param);
	}

	public function dryer_spins()
	{
		if(!$this->logonCheck()) return;
		$param['uri'] = 'dryer_spins';
		$param['kind'] = 'table';
		$param['table'] = 'tbl_service_dryer';		
		$param['models'] = $this->Model_model->getModelsForDisplay(array('type'=>0));

		$this->load->view("admin/view_header", $param);
		$this->load->view("admin/view_service_dryer",$param);
	}

	public function services()
	{
		if(!$this->logonCheck()) return;
		$param['uri'] = 'services';
		$param['kind'] = 'table';
		$param['table'] = 'tbl_service';
		
		$param['models'] = $this->Model_model->getModelsForDisplay();
		$param['programs'] = $this->Program_model->getDatas(null);
		$param['days'] = $this->Day_model->getDatas(null);

	
		$this->load->view("admin/view_header", $param);
		$this->load->view("admin/view_service",$param);
	}

	
	public function day_services()
	{
		if(!$this->logonCheck()) return;
		$param['uri'] = 'day_services';
		$param['kind'] = 'table';
		$param['table'] = 'tbl_service_day';
		
		$param['models'] = $this->Model_model->getModelsForDisplay();
		$param['programs'] = $this->Program_model->getDatas(null);
		$param['days'] = $this->Day_model->getDatas(null);
	
		$this->load->view("admin/view_header", $param);
		$this->load->view("admin/view_service_day",$param);
	}

	public function home_bank()
	{
		if(!$this->logonCheck()) return;
		$param['uri'] = 'home_bank';
		$param['kind'] = 'table';
		$param['table'] = 'tbl_home_bank';
		$param['admin_phone'] = $this->Setting_model->getAdminPhoneNumber();
		$param['hopper_flag'] = $this->Setting_model->getHopperFlag();		

		$this->load->view("admin/view_header", $param);
		$this->load->view("admin/view_home_bank",$param);
	}	


	public function promotion()
	{
		if(!$this->logonCheck()) return;
		$param['uri'] = 'promotion';
		$param['kind'] = 'table';
		$param['table'] = 'tbl_promotion';
	
		$this->load->view("admin/view_header", $param);
		$this->load->view("admin/view_promotion",$param);
	}	
	public function freesetting()
	{
		if(!$this->logonCheck()) return;
		$param['uri'] = 'freesetting';
		$param['kind'] = 'table';
		$param['table'] = 'tbl_free_setting';
		$param['models'] = $this->Model_model->getModelsForDisplay();
		
		$this->load->view("admin/view_header", $param);
		$this->load->view("admin/view_freesetting",$param);
	}	

	

	public function options()
	{
		if(!$this->logonCheck()) return;
		$param['uri'] = 'options';
		$param['kind'] = 'table';
		$param['table'] = 'tbl_option';
	
		$this->load->view("admin/view_header", $param);
		$this->load->view("admin/view_option",$param);
	}	
	public function soaps()
	{
		if(!$this->logonCheck()) return;
		$param['uri'] = 'soaps';
		$param['kind'] = 'table';
		$param['table'] = 'tbl_soap';
	
		$this->load->view("admin/view_header", $param);
		$this->load->view("admin/view_soap",$param);
	}	

	public function report()
	{
		if(!$this->logonCheck()) return;

		$param['uri'] = 'report';
		$param['kind'] = 'table';
		$param['table'] = 'tbl_transaction';
			
		$this->load->view("admin/view_header", $param);
		$this->load->view("admin/view_report",$param);
	}

	public function washer_transactions()
	{
		if(!$this->logonCheck()) return;

		$total = 0;
		$charge = 0;
		$startDate = $this->Setting_model->getWasherTransactionClearTime();
		$trs = $this->Transaction_model->getDatas(array('reason'=>'withdraw'));

		$cnt = 0;
		foreach($trs as $tr)
		{
			if($startDate!="" && $tr->dt < $startDate) continue;

			$charge += $tr->price;
			$total += $tr->org_price;
			$cnt++;
		}

		$param['uri'] = 'washer_transactions';
		$param['kind'] = 'table';
		$param['table'] = 'tbl_transaction';
		$param['total'] = $total;
		$param['charge'] = $charge;
		$param['counts'] = $cnt;

		$this->load->view("admin/view_header", $param);
		$this->load->view("admin/view_washer_transactions",$param);
	}

	public function credit_incomes()
	{
		if(!$this->logonCheck()) return;
		$startDate = $this->Setting_model->getCreditSaleClearTime();
		$total_bill = 0;
		$bills = array();
		$incomes = $this->CreditIncome_model->getDatas(null);

		foreach($incomes as $income)
		{	
			if($startDate!="" && $income->dt < $startDate)
				continue;
			$total_bill += $income->price;
			$counter = 0;
			if(isset($bills[$income->method]))
				$counter = $bills[$income->method];
			$counter ++;
			$bills[$income->method] = $counter;
		}


		$param['uri'] = 'credit_incomes';
		$param['kind'] = 'table';
		$param['table'] = 'tbl_credit_income';
		$param['total_bill'] = $total_bill;
		$param['bills'] = $bills;

		$this->load->view("admin/view_header", $param);
		$this->load->view("admin/view_credit_incomes",$param);
	}

	public function k2_incomes()
	{
		if(!$this->logonCheck()) return;

		$startDate = $this->Setting_model->getHomeSaleClearTime();
		$total_bill = 0;
		$total_payout = 0;
		$bills = array();
		$payouts = array();
		$incomes = $this->Kiosk2Income_model->getDatas(null);

		foreach($incomes as $income)
		{	
			if($startDate!="" && $income->dt < $startDate)
				continue;
			if($income->type == "bill")
			{
				$total_bill += ($income->price * $income->cnt);
				$counter = 0;
				if(isset($bills[strval($income->price)]))
					$counter = $bills[strval($income->price)];
				$counter +=$income->cnt;
				$bills[strval($income->price)] = $counter;	
			}
			else
			{
				$total_payout += ($income->price * $income->cnt);
				$counter = 0;
				if(isset($payouts[strval($income->price)]))
					$counter = $payouts[strval($income->price)];
				$counter +=$income->cnt;
				$payouts[strval($income->price)] = $counter;
			}
		}


		$param['uri'] = 'k2_incomes';
		$param['kind'] = 'table';
		$param['table'] = 'tbl_kiosk_income';
		$param['total_bill'] = $total_bill;
		$param['bills'] = $bills;

		$param['total_payout'] = $total_payout;
		$param['payouts'] = $payouts;


		$this->load->view("admin/view_header", $param);
		$this->load->view("admin/view_k2_incomes",$param);
	}


	public function wash_fold()
	{
		if(!$this->logonCheck()) return;

		$startDate = $this->Setting_model->getWashFoldClearTime();
		$total = 0;
		$cash = 0;
		$credit = 0;

		$incomes = $this->WashFold_model->getDatas(null);
		foreach($incomes as $income)
		{	
			if($startDate!="" && $income->dt < $startDate)
				continue;
			if($income->payment == "Cash")
			{
				$cash += $income->total_price;
			}
			else
			{
				$credit += $income->total_price;
			}
			$total += $income->total_price;
		}


		$param['uri'] = 'wash_fold';
		$param['kind'] = 'table';
		$param['table'] = 'tbl_wash_fold';
		$param['total'] = $total;
		$param['cash'] = $cash;
		$param['credit'] = $credit;

		$this->load->view("admin/view_header", $param);
		$this->load->view("admin/view_wash_fold",$param);
	}


	public function incomes()
	{
		if(!$this->logonCheck()) return;

		$startDate = $this->Setting_model->getKioskIncomeClearTime();

		$total = 0;
		$bills = array();
		$incomes = $this->KioskIncome_model->getDatas(null);
		foreach($incomes as $income)
		{	
			if($startDate!="" && $income->dt < $startDate)
				continue;

			$total += ($income->price * $income->cnt);
			$counter = 0;
			if(isset($bills[strval($income->price)]))
				$counter = $bills[strval($income->price)];
			$counter +=$income->cnt;
			$bills[strval($income->price)] = $counter;
		}

		$param['uri'] = 'incomes';
		$param['kind'] = 'table';
		$param['table'] = 'tbl_kiosk_income';
		$param['total'] = $total;
		$param['bills'] = $bills;

		$this->load->view("admin/view_header", $param);
		$this->load->view("admin/view_incomes",$param);
	}

	public function transaction()
	{
		if(!$this->logonCheck()) return;
		$param['uri'] = 'transaction';
		$param['kind'] = 'table';
		$param['table'] = 'tbl_user';
		$this->load->view("admin/view_header", $param);
		$this->load->view("admin/view_transaction",$param);
	}


	public function specials()
	{
		if(!$this->logonCheck()) return;
		$param['uri'] = 'specials';
		$param['kind'] = 'table';
		$param['table'] = 'tbl_special';
		$this->load->view("admin/view_header", $param);
		$this->load->view("admin/view_special",$param);
	}

	public function machines()
	{
		if(!$this->logonCheck()) return;
		$param['uri'] = 'machines';
		$param['kind'] = 'table';
		$param['table'] = 'tbl_machine';
		$this->load->view("admin/view_header", $param);
		$this->load->view("admin/view_machines",$param);
	}

	public function jobs()
	{
		if(!$this->logonCheck()) return;
		$param['uri'] = 'jobs';
		$param['kind'] = 'table';
		$param['table'] = 'tbl_job';
		$this->load->view("admin/view_header", $param);
		$this->load->view("admin/view_jobs",$param);
	}

	public function washer_time_detail()
	{
		if(!$this->logonCheck()) return;
		$param['uri'] = 'washer_time_detail';
		$param['kind'] = 'table';
		$param['washers'] = $this->Machine_model->getDatas(null);
		$this->load->view("admin/view_header", $param);
		$this->load->view("admin/view_washer_time_details",$param);
	}

	public function washer_time_summary()
	{
		if(!$this->logonCheck()) return;
		$param['uri'] = 'washer_time_summary';
		$param['kind'] = 'table';
		$param['washers'] = $this->Machine_model->getDatas(null);
		$this->load->view("admin/view_header", $param);
		$this->load->view("admin/view_washer_time_summary",$param);
	}
	public function dryer_activity_details()
	{
		if(!$this->logonCheck()) return;
		$param['uri'] = 'dryer_activity_details';
		$param['kind'] = 'table';
		$param['washers'] = $this->Machine_model->getDatas(null);
		$this->load->view("admin/view_header", $param);
		$this->load->view("admin/view_dryer_activity_details",$param);
	}

	public function dryer_details()
	{
		if(!$this->logonCheck()) return;
		$param['uri'] = 'dryer_details';
		$param['kind'] = 'table';
		$param['washers'] = $this->Machine_model->getDatas(null);
		$this->load->view("admin/view_header", $param);
		$this->load->view("admin/view_dryer_details",$param);
	}	

	public function dryer_summary()
	{
		if(!$this->logonCheck()) return;
		$param['uri'] = 'dryer_summary';
		$param['kind'] = 'table';
		$param['washers'] = $this->Machine_model->getDatas(null);
		$this->load->view("admin/view_header", $param);
		$this->load->view("admin/view_dryer_id_summary",$param);
	}	

	public function dryer_time_detail()
	{
		if(!$this->logonCheck()) return;
		$param['uri'] = 'dryer_time_detail';
		$param['kind'] = 'table';
		$param['washers'] = $this->Machine_model->getDatas(null);
		$this->load->view("admin/view_header", $param);
		$this->load->view("admin/view_dryer_time_details",$param);
	}

	public function dryer_time_summary()
	{
		if(!$this->logonCheck()) return;
		$param['uri'] = 'dryer_time_summary';
		$param['kind'] = 'table';
		$param['washers'] = $this->Machine_model->getDatas(null);
		$this->load->view("admin/view_header", $param);
		$this->load->view("admin/view_dryer_time_summary",$param);
	}


	public function supplys()
	{
		if(!$this->logonCheck()) return;
		$param['uri'] = 'supplys';
		$param['kind'] = 'table';
		$param['table'] = 'tbl_supply';
		$this->load->view("admin/view_header", $param);
		$this->load->view("admin/view_supplys",$param);
	}	

	public function supplyTransaction()
	{
		if(!$this->logonCheck()) return;

		$startDate = $this->Setting_model->getSupplyTrxClearTime();
		$total = 0;
		$cash = 0;
		$credit = 0;

		$rows = $this->Base_model->getDatas('tbl_supply_transaction', ['dt >=' => $startDate, 'action'=>'sold']);
		foreach($rows as $row)
		{	
			if($row->type == "cash")
			{
				$cash += $row->price * $row->count;
			}
			else
			{
				$credit += $row->price * $row->count;
			}
		}

		$param['uri'] = 'supplyTransaction';
		$param['kind'] = 'table';
		$param['table'] = 'tbl_supply_transaction';
		$param['total'] = $cash + $credit;
		$param['cash'] = $cash;
		$param['credit'] = $credit;

		$this->load->view("admin/view_header", $param);
		$this->load->view("admin/view_supply_transaction",$param);
	}

}
