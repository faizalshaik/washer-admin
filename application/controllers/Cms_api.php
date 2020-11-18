<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cms_api extends CI_Controller {
	public function __construct()
	{
		parent::__construct();
		date_default_timezone_set('America/Chicago');
				
		$this->load->model('User_model');
		$this->load->model('Base_model');
		$this->load->model('Machine_model');
		$this->load->model('Model_model');		
		$this->load->model('Define_model');
		$this->load->model('Program_model');				
		$this->load->model('Service_model');		
		$this->load->model('Option_model');				
		$this->load->model('Day_model');
		$this->load->model('Job_model');
		$this->load->model('Special_model');		
		$this->load->model('DayService_model');	
		$this->load->model('KioskIncome_model');			
		$this->load->model('Kiosk2Income_model');					
		$this->load->model('Transaction_model');				
		$this->load->model('Promotion_model');
		$this->load->model('Setting_model');	
		$this->load->model('HomeBank_model');
		$this->load->model('HomeBankState_model');		
		$this->load->model('ServiceDryer_model');
		$this->load->model('CreditIncome_model');		
		$this->load->model('Soap_model');	
		$this->load->model('WashFold_model');
		$this->load->model('FreeSetting_model');

		$this->load->model('Supply_model');							
	}

	public function ajaxDel() {
		if($this->logonCheck()) {
			global $MYSQL;
			$Id = $this->input->post('Id');
			$tbl_Name = $this->input->post('tbl_Name');
			if($tbl_Name !='') {
				$conAry = array('Id' => $Id);
				$updateAry = array('isdeleted'=>'1');
				$this->Base_model->updateData($tbl_Name, $conAry, $updateAry);
				echo json_encode(array("status" => TRUE));	
			} else {
				echo json_encode(array("status" => FALSE));	
			}
		}
	}
	public function delUser() {
		if($this->logonCheck()) {
			global $MYSQL;
			$Id = $this->input->post('Id');
			$tbl_Name = $this->input->post('tbl_Name');
			if($tbl_Name !='') {
				$this->Base_model->deleteByField($tbl_Name, "Id", $Id);
				echo json_encode(array("status" => TRUE));	
			} else {
				echo json_encode(array("status" => FALSE));	
			}
		}
	}
	public function getDataById() {
		$this->logonCheck();

		$Id = $this->input->post("Id");
		$tableName = $this->input->post("tbl_Name");
		$ret = $this->Base_model->getRow($tableName, array('Id'=>$Id));
		echo json_encode($ret);
	}
	public function delData()
	{
		$this->logonCheck();
		$Id = $this->input->post("Id");
		$tableName = $this->input->post("tbl_Name");
		$ret = $this->Base_model->deleteRow($tableName, array('Id'=>$Id));
		echo "1";
	}

	public function getUser() {
		if($this->logonCheck()) {
			global $MYSQL;
			$select = ' a.*, b.name as company';
			$conAry = array('a.isdeleted'=>'0');
			$column_order = array(null, null, 'fname', 'email', null, null, null); 
			$column_search = array('fname', 'email');
			$travel_cate = $this->User_model->getTableDatas($MYSQL['_userDB'].' a', $conAry, $column_search, 
				$column_order, 'user', $select);
			$data = array();
			$no = $_POST['start'];
			foreach ($travel_cate as $item) {
				$row = array();
				$no++;
				$active = "<span >Yes</span>";
				if($item->isactive == 0)
					$active = "<span style='color:red'>No</span>";
				$strAction = '<a href="javascript:void(0)" class="on-default edit-row" onclick="EditUser('.$item->Id.')" title="Edit" ><i class="fa fa-pencil"></i></a> <a href="javascript:void(0)" class="on-default remove-row" onclick="RemoveUser('.$item->Id.')" title="Remove" ><i class="fa fa-trash-o"></i></a>';
				$row[] = $no;
				$row[] = $item->company;
				$row[] = $item->fname;
				$row[] = $item->email;
				$row[] = $item->pass;
				$row[] = $active;
				$row[] = $item->created;
				$row[] = $strAction;
				$data[] = $row;
			}

			$output = array(
					"draw" => $_POST['draw'],
					"recordsTotal" => $this->User_model->getCounts($MYSQL['_userDB'].' a', $conAry),
					"recordsFiltered" => $this->User_model->count_filtered($MYSQL['_userDB'].' a', $conAry, 
						$column_search, $column_order,'user', $select),
					"data" => $data,
			);
			//output to json format
			echo json_encode($output);
		}
	}
	public function resetUserPWD() {
		if($this->logonCheck()) {
			global $MYSQL;
			$Id = $this->input->post('Id');
			$conAry = array('Id' => $Id);		
			$ret = $this->User_model->getRow($MYSQL['_userDB'], $conAry);
			$newPWD = password_hash($ret->email, PASSWORD_DEFAULT);
			$updateAry = array('password'=>$newPWD);
			$this->User_model->updateData($MYSQL['_userDB'], $conAry, $updateAry);
			echo json_encode(array("status" => TRUE));
		}
	}
	public function addEditUser() {
		global $MYSQL;
		$userId = $this->input->post('user_id');
		$companyId = $this->input->post('company_id');
		$fname = $this->input->post('fname');
		$lname = '';
		$email = $this->input->post('email');
		$email = strtolower($email);
		$password = $this->input->post('password');
		$userType = $this->input->post('user_type');
		$about = $this->input->post('about');
		$avatar = '';

		if(isset($_FILES["avatar"])) {
			$tmpPicName = $_FILES["avatar"]["name"]; // The file name
			$tmpPic = $_FILES["avatar"]["tmp_name"]; // File in the PHP tmp folder

			$picExt = pathinfo($tmpPicName, PATHINFO_EXTENSION);
			$tmpPicNewName = time().".".$picExt; 			
			if ($tmpPic) { // if file not chosen
				if(move_uploaded_file($tmpPic, 'uploads/avatar/'.$tmpPicNewName)){
					$avatar = $tmpPicNewName;
				}
			}
		} else {
			$avatar = $this->input->post('oldAvatar');
		}


		$encPass = password_hash($password, PASSWORD_DEFAULT);
		if($userId != '') {
			$ret = $this->User_model->getRow($MYSQL['_userDB'], array('email'=>$email, 'isdeleted'=>'0', 'Id !='=>$userId));
			if(empty($ret)) {
				$updateAry = array('company_id'=>$companyId, 'fname'=>$fname, 'lname'=>$lname, 
					'email'=>$email, 'pass'=>$password, 'password'=>$encPass,  
					'user_type'=>$userType, 'about'=>$about, 'avatar'=>$avatar,
					'modified'=>date('Y-m-d'));
				$ret1 = $this->User_model->updateData($MYSQL['_userDB'], array('Id'=>$userId), $updateAry);
				if($ret1 > 0) {
					$this->NotifyEmail($email, $fname, $lname, '', $password);
					$this->session->set_flashdata('messagePr', 'Update User Successfully..');
				} else {
					$this->session->set_flashdata('messagePr', 'Unable to Update User..');
				}
			} else {
				$this->session->set_flashdata('messagePr', 'Unable to Update User.. Same Email is existed!');
			}
		} else {
			$ret2 = $this->User_model->getRow($MYSQL['_userDB'], array('email'=>$email, 'isdeleted'=>'0'));
			if(empty($ret2)) {
				$insertAry  = array('company_id'=>$companyId, 'fname'=>$fname, 'lname'=>$lname, 'email'=>$email, 'pass'=>$password, 
					'password'=>$encPass, 'isactive'=>'1', 'isdeleted'=>'0', 'created'=>date('Y-m-d'), 
					'user_type'=>$userType, 'about'=>$about,'avatar'=>$avatar,
					'modified'=>date('Y-m-d'));
				$ret3 = $this->User_model->insertData($MYSQL['_userDB'], $insertAry);
				if($ret3) {
					$this->NotifyEmail($email, $fname, $lname, '', $password);
					$this->session->set_flashdata('messagePr', 'Insert User Successfully..');
				} else {
					$this->session->set_flashdata('messagePr', 'Unable to Insert User..');
				}
			} else {
				$this->session->set_flashdata('messagePr', 'Unable to Insert User.. Same Email is existed!');
			}
		}
		redirect('Cms/user/', 'refresh');
	}

	public function clearWasherTransactionHistory()
	{
		$this->logonCheck();
		$this->Setting_model->setWasherTransactionClearTime(date("Y-m-d H:i:s"));
	}	

	public function clearWashFoldHistory()
	{
		$this->logonCheck();
		$this->Setting_model->setWashFoldClearTime(date("Y-m-d H:i:s"));
	}	

	public function clearIncomeHistory()
	{
		$this->logonCheck();
		$this->Setting_model->setKioskIncomeClearTime(date("Y-m-d H:i:s"));
	}

	public function clearHomeSaleHistory()
	{
		$this->logonCheck();
		$this->Setting_model->setHomeSaleClearTime(date("Y-m-d H:i:s"));
	}

	public function clearCreditHistory()
	{
		$this->logonCheck();
		$this->Setting_model->setCreditSaleClearTime(date("Y-m-d H:i:s"));
	}


	public function washer_states()
	{
		$this->logonCheck();
		$row = array();

		$machines = $this->Machine_model->getDatas(null, 'machine_id');
		foreach($machines as $machine)
		{
			$mins = abs(strtotime(date("Y-m-d H:i:s")) - strtotime($machine->update_dt));
			$mins = $mins/60;
			if($mins >= 2) continue;

			$model = $this->Model_model->getRow(array('model'=>$machine->product_number));
			if($model==null || $model->type!=0) continue;

			$busy = false;
			if($machine->current_mode!=0) 
				$busy = true;
			else
			{
				$job = $this->Job_model->getRow(array('machine_id'=>$machine->Id, 'status'=>'ready'));
				if($job!=null) 
					$busy = true;
				else
				{
					$job = $this->Job_model->getRow(array('machine_id'=>$machine->Id, 'status'=>'busy'));
					if($job!=null) 
						$busy = true;
				}
			}
			$row[] = array('id'=>$machine->machine_id, 'busy'=>$busy, 'weight'=>$model->weight);
		}
		echo json_encode($row);
	}


	public function saveBalance()
	{
		$this->logonCheck();		
		$userId = $this->input->post('userId');
		if($userId==0)
		{
			echo "0";
			return;
		}
		
		$balance = $this->input->post('balance');
		$this->User_model->setField('balance', $balance, array('Id'=>$userId), FALSE);
		echo "1";
	}

	public function getWasherTransactionsByTrId()
	{
		$this->logonCheck();

		$result = array();

		$trId = $this->input->post('Id');
		$tr = $this->Transaction_model->getRow(array('Id'=>$trId));
		if($tr!=null)
		{
			$trs = $this->Transaction_model->getDatas(array('user_id'=>$tr->user_id, 'dt'=>$tr->dt, 'method'=>$tr->method, 'reason'=>$tr->reason));
			foreach($trs as $tr)
			{
				$machine = $this->Machine_model->getRow(array('Id'=>$tr->machine_id));
				if($machine== null) continue;

				$model = $this->Model_model->getRow(array('model'=>$machine->product_number));
				if($model==null ) continue;

				$program = "";
				if($model->type==0)
				{
					switch($tr->program_id)
					{
						case 1:
							$program = "HOT";
						break;
						case 2:
							$program = "WARM";
						break;
						case 3:
							$program = "COLD";
						break;
					}
				}

				$option = "";
				if($tr->options !=null)
				{
					switch($tr->options)
					{
						case 1:
							$option = "Havey";
						break;
						case 2:
							$option = "Extra";
						break;
						case 3:
							$option = "Havey|Extra";
						break;
					}					
				}


				$row = array();
				if($tr->user_id==1)$row['accType'] = 'Inhouse';
				else $row['accType'] = 'App';
				$row['machine'] = $machine->machine_id;
				$row['program'] = $program;
				$row['option'] = $option;				
				$row['dt'] = $tr->dt;
				$row['method'] = $tr->method;
				$row['price'] = $tr->price;				
				$result[]=$row;	
			}
		}
		echo json_encode($result);
		
	}

	public function get_report($start='', $end='')
	{
		//$this->logonCheck();
		if($start==null || $start=='' || $end==null || $end=='')
		{
			$output = array(
				"draw" => "",
				"recordsTotal" => 0,
				"recordsFiltered" => 0,
				"data" => [],
			);
			echo json_encode($output);
			return;
		}

		$start = str_replace("%20", " ", $start);
		$end = str_replace("%20", " ", $end);

		$data = array();
		//payout, bill in kiosk
		$payout = 0.0;
		$total = 0.0;
		$kioskBill = array();
		$hopperCoins = 0;
		$rows = $this->Kiosk2Income_model->getDatas(null, 'dt');
		foreach($rows as $row)
		{
			if($row->dt < $start || $row->dt > $end)
				continue;
			if($row->type=='payout')
			{
				$payout += $row->price * $row->cnt;
				if($hopperCoins==0)
					$hopperCoins = $row->hopper_coins;
			}
			else if($row->type=='bill')
			{
				$price = number_format($row->price,2);
				if(isset($kioskBill[$price]))
					$kioskBill[$price] += $row->cnt * $row->price;
				else $kioskBill[$price] = $row->cnt * $row->price;
			}
		}

		//get home banks state
		$rows = $this->HomeBankState_model->getDatas(null, 'dt');
		$added = 0.0;
		$prevCoins = 0;
		foreach($rows as $row)
		{
			if($row->dt < $start || $row->dt > $end)
				continue;
			if($prevCoins==0)
				$prevCoins = $row->prev_count;
			$added += ($row->new_count - $row->prev_count) * $row->coin;
		}

		if($prevCoins >0 && $prevCoins < $hopperCoins)
			$hopperCoins = $prevCoins;

		$data[] = array('1', 'Balance Forward (from Hooper)', '$ '.number_format($hopperCoins * 0.25,2));
		$data[] = array('2', 'Add in hopper', '$ '.number_format($added, 2));
		$data[] = array('3', 'Payout from hopper', '$ '.number_format($payout, 2));

		$no = 4;
		$units = array('1.00', '5.00', '10.00', '20.00');
		foreach($units as $unit)
		{
			if(!isset($kioskBill[$unit]) || $kioskBill[$unit]==0) continue;
			$data[] = array($no, '$ '.$unit.' Bills in kiosk'  , '$ '.number_format($kioskBill[$unit], 2));
			$total += $kioskBill[$unit];
			$no++;
		}
		$data[] = array($no, 'Total'  , '$ '.number_format($total, 2));
		$no++;

		//card charge
		$kioskCard = 0.0;
		$appCard = 0.0;
		$rows = $this->Transaction_model->getDatas(array('reason'=>'withdraw', 'method'=>'CardReader'));
		foreach($rows as $row)
		{
			if($row->dt < $start || $row->dt > $end)
				continue;
			if($row->user_id==1)
				$kioskCard += $row->price;
			else
				$appCard += $row->price;
		}

		$rows = $this->Transaction_model->getDatas(array('reason'=>'deposit', 'method'=>'cash'));
		foreach($rows as $row)
		{
			if($row->dt < $start || $row->dt > $end)
				continue;
			if($row->user_id==1)
				$kioskCard += $row->price;
			else
				$appCard += $row->price;
		}


		
		$data[] = array($no, ''  , '');		$no++;
		$data[] = array($no, 'Credit card charge Kisok'  , '$ '.number_format($kioskCard, 2));
		$no++;
		$data[] = array($no, 'Credit card charge App'  , '$ '.number_format($appCard, 2));
		$no++;
		$data[] = array($no, 'Total Credit Card Charge'  , '$ '.number_format($appCard + $kioskCard, 2));

		$output = array(
			"draw" => "",
			"recordsTotal" => 0,
			"recordsFiltered" => 0,
			"data" => $data
		);
		echo json_encode($output);
	}

	public function getWasherTransactions()
	{
		$this->logonCheck();
		$startDate = $this->Setting_model->getWasherTransactionClearTime();
		$trs = $this->Transaction_model->getDatasDesc(array('reason'=>'withdraw'), 'dt');
		$data = array();

		$curTrID = 0;
		$curTrDT = "";
		$curUserId = 0;
		$curTrMethod = "";
		$curTrCounter = 0;
		$curTrAmount = 0.0;

		foreach($trs as $tr)
		{
			if($startDate!="" && $tr->dt < $startDate)
				continue;
			
			if($curTrDT!=$tr->dt || $curUserId!=$tr->user_id || $curTrMethod!=$tr->method)
			{
				if($curTrCounter>0)
				{
					$row = array();
					$row[] = $curTrDT;
					$row[] = $curTrID;
					$row[] = $curTrCounter;

					if($curUserId==1)$row[] = "Inhouse";
					else $row[] = "App";

					$row[] = $curTrMethod;
					$row[] = $curTrAmount;
					$strAction = '<a href="javascript:void(0)" class="on-default edit-row" onclick="viewTR('.$curTrID.')" title="Edit" ><i class="fa fa-pencil"></i></a>';
					$row[] = $strAction;
					$data[]= $row;		
				}

				$curTrID = $tr->Id;
				$curTrDT = $tr->dt;
				$curUserId=$tr->user_id;
				$curTrMethod = $tr->method;
				$curTrCounter = 1;
				$curTrAmount = $tr->price;

			}
			else
			{
				$curTrCounter++;
				$curTrAmount += $tr->price;
			}

			// $machine = $this->Machine_model->getRow(array('Id'=>$tr->machine_id));
			// if($machine== null) continue;

			// $model = $this->Model_model->getRow(array('model'=>$machine->product_number));
			// if($model==null ) continue;

			// $program = "";
			// if($model->type==0)
			// {
			// 	switch($tr->program_id)
			// 	{
			// 		case 1:
			// 			$program = "HOT";
			// 		break;
			// 		case 2:
			// 			$program = "WARM";
			// 		break;
			// 		case 3:
			// 			$program = "COLD";
			// 		break;
			// 	}
			// }
			// else if($model->type==1)
			// {
			// 	switch($tr->program_id)
			// 	{
			// 		case 1:
			// 			$program = "HIGH";
			// 		break;
			// 		case 2:
			// 			$program = "MEDIUM";
			// 		break;
			// 		case 3:
			// 			$program = "LOW";
			// 		break;
			// 	}
			// }

			//$program = $this->Program_model->getRow(array('Id'=>$tr->program_id));
			//if($program== null) continue;

			// $mType = "Washer";
			// if($model->type==1)
			// 	$mType = "Dryer";

			// $phone = "Unknown";
			// $user = $this->User_model->getRow('tbl_user', array('Id'=>$tr->user_id));
			// if($user!=null)
			// 	$phone = $user->phone;

			// $row = array();
			// $row[] = $tr->dt;
			// $row[] = $phone;
			// $row[] = $machine->machine_id."-".$mType.'-'.$model->weight.'Lb';
			// $row[] = $program;
			// $row[] = $this->Define_model->getOptionString($tr->options);
			// $row[] = '$ '.$tr->org_price;
			// $row[] = '$ '.$tr->price;
			// $data[]= $row;
		}

		if($curTrCounter>0)
		{
			$row = array();
			$row[] = $curTrDT;
			$row[] = $curTrID;
			$row[] = $curTrCounter;

			if($curUserId==1)$row[] = "Inhouse";
			else $row[] = "App";

			$row[] = $curTrMethod;
			$row[] = $curTrAmount;
			$strAction = '<a href="javascript:void(0)" class="on-default edit-row" onclick="viewTR('.$curTrID.')" title="Edit" ><i class="fa fa-pencil"></i></a>';
			$row[] = $strAction;

			$data[]= $row;
		}		



		$output = array(
			"draw" => "",
			"recordsTotal" => count($data),
			"recordsFiltered" => count($data),
			"data" => $data,
		);
		echo json_encode($output);
	}

	public function getUserTransaction($userId=0)
	{
		$this->logonCheck();
		$user = null;
		if($userId!=0)
			$user = $this->User_model->getRow('tbl_user', array('Id'=>$userId));
		if($user==null)
		{
			$output = array(
				"draw" => "",
				"recordsTotal" => 0,
				"recordsFiltered" => 0,
				"data" => array(),
			);
			echo json_encode($output);
			return;
		}

		$trs = $this->Transaction_model->getDatasDesc(array('user_id'=>$user->Id), 'dt');
		$data = array();
		foreach($trs as $tr)
		{
			if($tr->reason == 'withdraw')
			{
				$machine = $this->Machine_model->getRow(array('Id'=>$tr->machine_id));
				if($machine== null) continue;
				$program = $this->Program_model->getRow(array('Id'=>$tr->program_id));
				if($program== null) continue;

				$row = array();
				$row[] = $tr->dt;
				$row[] = $machine->machine_id;
				$row[] = $program->name;
				$row[] = '$ '.$tr->price;
				$row[] = '$ '.$tr->balance;
				$data[]= $row;
			}
			else
			{
				$row = array();
				$row[] = $tr->dt;
				$row[] = "";
				$row[] = "Deposit";
				$row[] = $tr->price;
				$row[] = $tr->balance;
				$data[]= $row;
			}			
		}

		$output = array(
			"draw" => "",
			"recordsTotal" => count($data),
			"recordsFiltered" => count($data),
			"data" => $data,
		);
		echo json_encode($output);
	}

	public function getUserBalance()
	{
		$this->logonCheck();
		$users = $this->User_model->getDatas(null);
		$data = array();
		foreach($users as $user)
		{
			$row = array();
			$row[] = $user->phone;
			$row[] = $user->balance;
			$strAction = '<a href="javascript:void(0)" class="on-default edit-row" '.
			'onclick="EditBalance('.$user->Id.')" title="Edit" ><i class="fa fa-pencil"></i></a>'.
			'<a href="javascript:void(0)" class="on-default remove-row" '.
			'onclick="Viewtransaction('.$user->Id.')" title="View" ><i class="ti ti-eye"></i></a>';
			$row[] = $strAction;

			$data[] = $row;
		}
		$output = array(
			"draw" => "",
			"recordsTotal" => count($data),
			"recordsFiltered" => count($data),
			"data" => $data,
		);
		echo json_encode($output);
	}

	public function getKioskIncomes(/*$start="", $end=""*/)
	{
		$this->logonCheck();
		$incomes = $this->KioskIncome_model->getDatas(null);
		$startDate = $this->Setting_model->getKioskIncomeClearTime();

		$data = array();
		foreach($incomes as $income)
		{
			if($startDate != "")
				if($income->dt < $startDate) continue;
			// if($end !="")
			// 	if($income->dt > $end) continue;
			$user = $this->User_model->getRow('tbl_user', array('Id'=>$income->user_id));

			$row = array();
			if($user!=null)
				$row[] = $user->phone;
			else
				$row[] = "";
			$row[] = '$ '.$income->price;
			$row[] = $income->cnt;
			$row[] = $income->dt;
			
			$data[]= $row;
		}

		$output = array(
			"draw" => "",
			"recordsTotal" => count($data),
			"recordsFiltered" => count($data),
			"data" => $data,
		);
		echo json_encode($output);
	}


	public function getWashFold()
	{
		$this->logonCheck();
		$incomes = $this->WashFold_model->getDatas(null);
		$startDate = $this->Setting_model->getWashFoldClearTime();

		$data = array();
		foreach($incomes as $income)
		{
			if($startDate != "")
				if($income->dt < $startDate) continue;

			$row = array();

			$row[] = $income->weight;
			if($income->soap==0)
				$row[] ="Powder";
			else
				$row[] ="Liquid";

			$row[] = $income->price_lbs;
			$row[] = $income->total_price;
			$row[] = $income->payment;
			$row[] = $income->dt;
			$data[]= $row;
		}

		$output = array(
			"draw" => "",
			"recordsTotal" => count($data),
			"recordsFiltered" => count($data),
			"data" => $data,
		);
		echo json_encode($output);
	}


	public function getCreditIncomes()
	{
		$this->logonCheck();
		$incomes = $this->CreditIncome_model->getDatas(null);
		$startDate = $this->Setting_model->getCreditSaleClearTime();

		$data = array();
		foreach($incomes as $income)
		{
			if($startDate != "")
				if($income->dt < $startDate) continue;
			$user = $this->User_model->getRow('tbl_user', array('Id'=>$income->user_id));
			if($user==null) continue;

			$row = array();
			$row[] = $income->method;
			$row[] = $user->phone;
			$row[] = $income->name;
			$row[] = $income->email;
			$row[] = '$ '.$income->price;
			$row[] = $income->dt;			
			$data[]= $row;
		}

		$output = array(
			"draw" => "",
			"recordsTotal" => count($data),
			"recordsFiltered" => count($data),
			"data" => $data,
		);
		echo json_encode($output);
	}

	public function getKiosk2Incomes(/*$start="", $end=""*/)
	{
		$this->logonCheck();
		$incomes = $this->Kiosk2Income_model->getDatas(null);
		$startDate = $this->Setting_model->getHomeSaleClearTime();

		$data = array();
		foreach($incomes as $income)
		{
			if($startDate != "")
				if($income->dt < $startDate) continue;

			$row = array();
			$row[] = $income->type;
			$row[] = '$ '.$income->price;
			$row[] = $income->cnt;
			$row[] = $income->dt;			
			$data[]= $row;
		}

		$output = array(
			"draw" => "",
			"recordsTotal" => count($data),
			"recordsFiltered" => count($data),
			"data" => $data,
		);
		echo json_encode($output);
	}

	
	public function getImageAsBase64($strfilePath){
		
		$file = file_get_contents( $strfilePath );

		if( $file ){
			
			$file = base64_encode( $file );
		}
		
		return $file;	
	}
	public function updateContent() {
		if($this->logonCheck()) {
			global $MYSQL;
			$content = $this->input->post('content');
			$tbl_Name = $this->input->post('tbl_Name');
			$redirectUrl = 'Cms/terms/';
			$updateAry = array('terms'=>$content, 'modified'=>date('Y-m-d'));
			$ret = $this->User_model->updateData($tbl_Name, array('Id'=>'1'), $updateAry);
			if($ret > 0) {
				$this->session->set_flashdata('messagePr', 'Update Successfully..');
			} else {
				$this->session->set_flashdata('messagePr', 'Unable to Update ..');
			}
			redirect($redirectUrl, 'refresh');
		}	
	}
	
	public function get_models() {
		$this->logonCheck();
		$models = $this->Model_model->getDatas(null);
		$data = array();
		foreach($models as $model)
		{
			$row = array();
			$row[] = $model->model;
			$row[] = $this->Define_model->getMachineType($model->type);
			$row[] = $model->weight;
			$strAction = '<a href="javascript:void(0)" class="on-default edit-row" '.
				'onclick="EditModel('.$model->Id.')" title="Edit" ><i class="fa fa-pencil"></i></a>'.
				'<a href="javascript:void(0)" class="on-default remove-row" '.
				'onclick="RemoveModel('.$model->Id.')" title="Remove" ><i class="fa fa-trash-o"></i></a>';
			$row[] = $strAction;
			$data[] = $row;
		}
		$output = array(
			"draw" => null,
			"recordsTotal" => count($data),
			"recordsFiltered" => count($data),
			"data" => $data,
		);
		//output to json format
		echo json_encode($output);
	}	


	public function get_jobs(){
		$this->logonCheck();
		$jobs = $this->Job_model->getDatas(null);

		$data = array();
		foreach($jobs as $job)
		{
			$user = $this->User_model->getRow('tbl_user', array('Id'=>$job->user_id));
			if($user==null) continue;
			$machine = $this->Machine_model->getRow(array('Id'=>$job->machine_id));
			if($machine == null) continue;

			$program = $this->Program_model->getRow(array('Id'=>$job->program_id));
			if($program == null) continue;

			$row = array();			
			$row[] = $user->phone;
			$row[] = $machine->machine_id;
			$row[] = $program->name;
			$row[] = $this->Define_model->getOptionString($job->options);
			$row[] = $job->status;
			$row[] = $job->dt;

			$strAction = '<a href="javascript:void(0)" class="on-default remove-row" '.
				'onclick="Removejob('.$job->Id.')" title="Remove" ><i class="fa fa-trash-o"></i></a>';
			$row[] = $strAction;
			$data[] = $row;
		}
		$output = array(
			"draw" => null,
			"recordsTotal" => count($data),
			"recordsFiltered" => count($data),
			"data" => $data,
		);
		//output to json format
		echo json_encode($output);
	}

	public function get_machines() {
		$this->logonCheck();
		$machines = $this->Machine_model->getDatas(null);

		$data = array();
		foreach($machines as $machine)
		{
			$model = $this->Model_model->getRow(array('model'=>$machine->product_number));

			$row = array();
			$row[] = $machine->machine_id;
			if($model)
			{
				$row[] = $model->weight.' Lbs';
				$row[] = $this->Define_model->getMachineType($model->type);
			}
			else
			{
				$row[] = 'unknown';
				$row[] = 'unknown';
			}
			$row[] = $machine->product_number;
			$row[] = $machine->serial_number;
			$row[] = $this->Define_model->getModeString($machine->current_mode);
			$row[] = $this->Define_model->getStatus1String($machine->current_status1);			

			$strAction = '<a href="javascript:void(0)" class="on-default edit-row" '.
				'onclick="EditMachine('.$machine->Id.')" title="Edit" ><i class="fa fa-pencil"></i></a>'.
				'<a href="javascript:void(0)" class="on-default remove-row" '.
				'onclick="RemoveMachine('.$machine->Id.')" title="Remove" ><i class="fa fa-trash-o"></i></a>';
			$row[] = "";//$strAction;
			$data[] = $row;
		}
		$output = array(
			"draw" => null,
			"recordsTotal" => count($data),
			"recordsFiltered" => count($data),
			"data" => $data,
		);
		//output to json format
		echo json_encode($output);
	}

	public function get_specials() {
		$this->logonCheck();
		$options = $this->Special_model->getDatas(null);

		$data = array();
		foreach($options as $option)
		{
			$row = array();
			$row[] = $option->name;
			$strAction = '<a href="javascript:void(0)" class="on-default edit-row" '.
				'onclick="EditSpecial('.$option->Id.')" title="Edit" ><i class="fa fa-pencil"></i></a>'.
				'<a href="javascript:void(0)" class="on-default remove-row" '.
				'onclick="RemoveSpecial('.$option->Id.')" title="Remove" ><i class="fa fa-trash-o"></i></a>';
			$row[] = $strAction;
			$data[] = $row;
		}
		$output = array(
			"draw" => null,
			"recordsTotal" => count($data),
			"recordsFiltered" => count($data),
			"data" => $data,
		);
		//output to json format
		echo json_encode($output);
	}

	public function edit_special()
	{
		$this->logonCheck();		
		$Id = $this->input->post('specialId');
		$name = $this->input->post('sp_name');

		$data = array('name'=>$name);
		if($Id=="")
		{
			$this->Special_model->insertData($data);
		}
		else
		{
			$this->Special_model->updateData(array('Id'=>$Id), $data);
		}

		redirect('Cms/specials', 'refresh');
	}
	
	public function get_coins()
	{
		$this->logonCheck();
		$coins = $this->HomeBank_model->getDatas(null);
		$data = array();
		foreach($coins as $coin)
		{
			$row = array();
			$row[] = '$ ' .$coin->coin;
			$row[] = $coin->count;
			$row[] = $coin->limit_cnt;
			$row[] = $coin->cnt_for_set;

			$strAction = '<a href="javascript:void(0)" class="on-default edit-row" '.
				'onclick="EditCoin('.$coin->Id.')" title="Edit" ><i class="fa fa-pencil"></i></a>'.
				'<a href="javascript:void(0)" class="on-default remove-row" '.
				'onclick="RemoveCoin('.$coin->Id.')" title="Remove" ><i class="fa fa-trash-o"></i></a>';
			$row[] = $strAction;
			$data[] = $row;
		}
		$output = array(
			"draw" => null,
			"recordsTotal" => count($data),
			"recordsFiltered" => count($data),
			"data" => $data,
		);
		//output to json format
		echo json_encode($output);
	}

	public function edit_hopper_set_count_flag()
	{
		$this->logonCheck();
		$hopper_flag = $this->input->post('hopper_flag');
		if($hopper_flag)
			$this->Setting_model->setHopperFlag("1");
		else
			$this->Setting_model->setHopperFlag("0");
		redirect('Cms/home_bank', 'refresh');
	}


	public function edit_admin_phone()
	{
		$this->logonCheck();
		$phone = $this->input->post('admin_phone');
		$this->Setting_model->setAdminPhoneNumber($phone);
		redirect('Cms/home_bank', 'refresh');
	}

	public function edit_coin()
	{
		$this->logonCheck();
		$Id = $this->input->post('coinId');
		$coin = $this->input->post('coin');
		$limit = $this->input->post('limit_cnt');
		$cnt_for_set = $this->input->post('cnt_for_set');

		$prevCnt = 0;
		$prevEntry = $this->HomeBank_model->getRow(array('Id'=>$Id));
		if($prevEntry!=null)
			$prevCnt = $prevEntry->count;

		$this->HomeBankState_model->insertData(array('coin'=>$coin,'prev_count'=>$prevCnt, 'new_count'=>$cnt_for_set + $prevCnt, 'dt'=>date("Y-m-d H:i:s") ));

		$this->HomeBank_model->setField('limit_cnt', $limit, array('Id'=>$Id));
		$this->HomeBank_model->setField('cnt_for_set', $cnt_for_set, array('Id'=>$Id));
		redirect('Cms/home_bank', 'refresh');
	}

	public function get_promotions()
	{
		$this->logonCheck();
		$promos = $this->Promotion_model->getDatas(null);
		$data = array();
		foreach($promos as $promo)
		{
			$row = array();
			$row[] = '$ ' .$promo->price;
			$row[] = $promo->bonus;
			$strAction = '<a href="javascript:void(0)" class="on-default edit-row" '.
				'onclick="EditPromotion('.$promo->Id.')" title="Edit" ><i class="fa fa-pencil"></i></a>'.
				'<a href="javascript:void(0)" class="on-default remove-row" '.
				'onclick="RemovePromotion('.$promo->Id.')" title="Remove" ><i class="fa fa-trash-o"></i></a>';
			$row[] = $strAction;
			$data[] = $row;
		}
		$output = array(
			"draw" => null,
			"recordsTotal" => count($data),
			"recordsFiltered" => count($data),
			"data" => $data,
		);
		//output to json format
		echo json_encode($output);
	}


	public function get_freesettings()
	{
		$this->logonCheck();
		$promos = $this->FreeSetting_model->getDatas(null);
		$data = array();
		foreach($promos as $promo)
		{
			$model = $this->Model_model->getRow(array('Id'=>$promo->model_id));
			if($model==null) continue;

			$row = array();
			$modelName = "";
			if($model->type==0) $modelName= "Washer(".$model->weight." lbs): ".$model->model;
			else $modelName = "Dryer(".$model->weight." lbs): ".$model->model;

			$row[] = $modelName;
			$row[] = $promo->consume_cnt;
			$strAction = '<a href="javascript:void(0)" class="on-default edit-row" '.
				'onclick="EditFreeSetting('.$promo->Id.')" title="Edit" ><i class="fa fa-pencil"></i></a>'.
				'<a href="javascript:void(0)" class="on-default remove-row" '.
				'onclick="RemoveFreeSetting('.$promo->Id.')" title="Remove" ><i class="fa fa-trash-o"></i></a>';
			$row[] = $strAction;
			$data[] = $row;
		}
		$output = array(
			"draw" => null,
			"recordsTotal" => count($data),
			"recordsFiltered" => count($data),
			"data" => $data,
		);
		//output to json format
		echo json_encode($output);
	}

	
	public function edit_bonus()
	{
		$this->logonCheck();		
		$Id = $this->input->post('promotionId');
		$price = $this->input->post('price');
		$bonus = $this->input->post('bonus');

		$data = array('price'=>$price, 'bonus'=>$bonus);
		if($Id=="")
		{
			$this->Promotion_model->insertData($data);
		}
		else
		{
			$this->Promotion_model->updateData(array('Id'=>$Id), $data);
		}
		redirect('Cms/promotion', 'refresh');
	}
		
	public function edit_freesetting()
	{
		$this->logonCheck();		
		$Id = $this->input->post('freesettingId');
		$modelId = $this->input->post('model');
		$consume = $this->input->post('consume');

		$data = array('model_id'=>$modelId, 'consume_cnt'=>$consume);
		if($Id=="")
		{
			$this->FreeSetting_model->insertData($data);
		}
		else
		{
			$this->FreeSetting_model->updateData(array('Id'=>$Id), $data);
		}
		redirect('Cms/freesetting', 'refresh');
	}


	public function get_options() {
		$this->logonCheck();
		$options = $this->Option_model->getDatas(null);

		$data = array();
		foreach($options as $option)
		{
			$row = array();
			$row[] = $option->name;
			$row[] = $option->price;
			$strAction = '<a href="javascript:void(0)" class="on-default edit-row" '.
				'onclick="EditOption('.$option->Id.')" title="Edit" ><i class="fa fa-pencil"></i></a>'.
				'<a href="javascript:void(0)" class="on-default remove-row" '.
				'onclick="RemoveOption('.$option->Id.')" title="Remove" ><i class="fa fa-trash-o"></i></a>';
			$row[] = $strAction;
			$data[] = $row;
		}
		$output = array(
			"draw" => null,
			"recordsTotal" => count($data),
			"recordsFiltered" => count($data),
			"data" => $data,
		);
		//output to json format
		echo json_encode($output);
	}
	public function edit_option()
	{
		$this->logonCheck();		
		$Id = $this->input->post('optionId');
		$name = $this->input->post('optionName');
		$price = $this->input->post('price');

		$data = array('name'=>$name, 'price'=>$price);
		if($Id=="")
		{
			$this->Option_model->insertData($data);
		}
		else
		{
			$this->Option_model->updateData(array('Id'=>$Id), $data);
		}

		redirect('Cms/options', 'refresh');
	}

	public function get_soaps() {
		$this->logonCheck();
		$soaps = $this->Soap_model->getDatas(null);

		$data = array();
		foreach($soaps as $soap)
		{
			$row = array();
			$row[] = $soap->name;
			$row[] = $soap->price;
			$strAction = '<a href="javascript:void(0)" class="on-default edit-row" '.
				'onclick="EditOption('.$soap->Id.')" title="Edit" ><i class="fa fa-pencil"></i></a>'.
				'<a href="javascript:void(0)" class="on-default remove-row" '.
				'onclick="RemoveOption('.$soap->Id.')" title="Remove" ><i class="fa fa-trash-o"></i></a>';
			$row[] = $strAction;
			$data[] = $row;
		}
		$output = array(
			"draw" => null,
			"recordsTotal" => count($data),
			"recordsFiltered" => count($data),
			"data" => $data,
		);
		//output to json format
		echo json_encode($output);
	}

	public function edit_soap()
	{
		$this->logonCheck();		
		$Id = $this->input->post('soapId');
		$name = $this->input->post('soapName');
		$price = $this->input->post('price');

		$data = array('name'=>$name, 'price'=>$price);
		if($Id=="")
		{
			$this->Soap_model->insertData($data);
		}
		else
		{
			$this->Soap_model->updateData(array('Id'=>$Id), $data);
		}

		redirect('Cms/soaps', 'refresh');
	}	
	public function get_dryer_spins(){
		$this->logonCheck();
		$services = $this->ServiceDryer_model->getDatas(null);

		$data = array();
		foreach($services as $service)
		{
			$model = $this->Model_model->getRow(array('Id'=>$service->model_id));
			if($model ==null) continue;

			$row = array();
			$row[] = $this->Define_model->getMachineType($model->type).' ('. $model->weight.' Lbs)';
			$row[] = $model->model;
			$row[]= $service->dryers;

			$strAction = '<a href="javascript:void(0)" class="on-default edit-row" '.
				'onclick="EditService('.$service->Id.')" title="Edit" ><i class="fa fa-pencil"></i></a>'.
				'<a href="javascript:void(0)" class="on-default remove-row" '.
				'onclick="RemoveService('.$service->Id.')" title="Remove" ><i class="fa fa-trash-o"></i></a>';
			$row[] = $strAction;
			$data[] = $row;
		}
		$output = array(
			"draw" => null,
			"recordsTotal" => count($data),
			"recordsFiltered" => count($data),
			"data" => $data,
		);
		//output to json format
		echo json_encode($output);
	}

	public function get_services() {
		$this->logonCheck();
		$services = $this->Service_model->getDatas(null);

		$data = array();
		foreach($services as $service)
		{
			$model = $this->Model_model->getRow(array('Id'=>$service->model_id));
			if($model ==null) continue;

			$row = array();
			$row[] = $this->Define_model->getMachineType($model->type).' ('. $model->weight.' Lbs)';
			$row[] = $model->model;

			$program = $this->Program_model->getRow(array('Id'=>$service->program_id));
			if($program!=null)
				$row[] = $program->name;
			else
				$row[] = "";
			$row[]= $service->price;

			$strAction = '<a href="javascript:void(0)" class="on-default edit-row" '.
				'onclick="EditService('.$service->Id.')" title="Edit" ><i class="fa fa-pencil"></i></a>'.
				'<a href="javascript:void(0)" class="on-default remove-row" '.
				'onclick="RemoveService('.$service->Id.')" title="Remove" ><i class="fa fa-trash-o"></i></a>';
			$row[] = $strAction;
			$data[] = $row;
		}
		$output = array(
			"draw" => null,
			"recordsTotal" => count($data),
			"recordsFiltered" => count($data),
			"data" => $data,
		);
		//output to json format
		echo json_encode($output);
	}

	

	public function edit_service_dryer()
	{
		$this->logonCheck();		
		$Id = $this->input->post('serviceId');
		$model = $this->input->post('model');
		$dryers = $this->input->post('dryers');

		$data = array('model_id'=>$model, 'dryers'=>$dryers);
		if($Id=="")
		{
			$this->ServiceDryer_model->insertData($data);
		}
		else
		{
			$this->ServiceDryer_model->updateData(array('Id'=>$Id), $data);
		}

		redirect('Cms/dryer_spins', 'refresh');
	}



	public function edit_service()
	{
		$this->logonCheck();		
		$Id = $this->input->post('serviceId');
		$model = $this->input->post('model');
		$program = $this->input->post('program');
		$price = $this->input->post('price');

		$data = array('model_id'=>$model, 'program_id'=>$program,			
			'price'=>$price);
		if($Id=="")
		{
			$this->Service_model->insertData($data);
		}
		else
		{
			$this->Service_model->updateData(array('Id'=>$Id), $data);
		}

		redirect('Cms/services', 'refresh');
	}

	public function get_day_services() {
		$this->logonCheck();
		$services = $this->DayService_model->getDatas(null);

		$data = array();
		foreach($services as $service)
		{
			$model = $this->Model_model->getRow(array('Id'=>$service->model_id));
			if($model ==null) continue;
			$program = $this->Program_model->getRow(array('Id'=>$service->program_id));
			if($program ==null) continue;

			$row = array();
			$row[] = $this->Define_model->getMachineType($model->type).' ('. $model->weight.' Lbs)';
			$row[] = $model->model;
			$row[] = $program->name;
			$row[] = $this->Define_model->getDayOfWeek($service->start_day);
			$row[] = $service->start_time;
			$row[] = $this->Define_model->getDayOfWeek($service->end_day);
			$row[] = $service->end_time;
			$row[]= $service->price;

			$strAction = '<a href="javascript:void(0)" class="on-default edit-row" '.
				'onclick="EditService('.$service->Id.')" title="Edit" ><i class="fa fa-pencil"></i></a>'.
				'<a href="javascript:void(0)" class="on-default remove-row" '.
				'onclick="RemoveService('.$service->Id.')" title="Remove" ><i class="fa fa-trash-o"></i></a>';
			$row[] = $strAction;
			$data[] = $row;
		}
		$output = array(
			"draw" => null,
			"recordsTotal" => count($data),
			"recordsFiltered" => count($data),
			"data" => $data,
		);
		//output to json format
		echo json_encode($output);
	}

	public function edit_day_service()
	{
		$this->logonCheck();		
		$Id = $this->input->post('serviceId');
		$model = $this->input->post('model');
		$program = $this->input->post('program');
		$start_day = $this->input->post('start_day');		
		$start_time = $this->input->post('start_time');
		$end_day = $this->input->post('end_day');
		$end_time = $this->input->post('end_time');
		$price = $this->input->post('price');

		$data = array('model_id'=>$model, 'program_id'=>$program,
			'start_day' => $start_day,	'start_time' => $start_time,
			'end_day' => $end_day,	'end_time' => $end_time,
			'price'=>$price);
		if($Id=="")
		{
			$this->DayService_model->insertData($data);
		}
		else
		{
			$this->DayService_model->updateData(array('Id'=>$Id), $data);
		}

		redirect('Cms/day_services', 'refresh');
	}

	public function edit_model() {
		$this->logonCheck();
		$modelId = $this->input->post('modelId');
		$model = $this->input->post('model');
		$typeId = $this->input->post('typeId');
		$weight = $this->input->post('weight');

		$data = array('model'=>$model, 'type'=>$typeId,'weight' => $weight);

		if($modelId=='')
		{
			$this->Model_model->insertData($data);
		}
		else
			$this->Model_model->updateData(array('Id'=>$modelId), $data);

		redirect('Cms/models', 'refresh');
	}


	public function edit_machine() {
		$this->logonCheck();
		$machine_id = $this->input->post('machine_id');
		$max_weight = $this->input->post('max_weight');
		if($machine_id=='')
			return;
		$machine = $this->Machine_model->getRow(array('Id'=>$machine_id));
		if($machine == null) 
			return;
		$this->Machine_model->updateData(array('Id'=>$machine_id), array('max_weight'=>$max_weight));
	}

	public function get_washer_time_details($washerId=0, $fromDate='', $toDate='')
	{
		$this->logonCheck();
		if($washerId==0)
		{
			$output = array("draw" => null,
				"recordsTotal" => 0,
				"recordsFiltered" => 0,
				"data" => array(),
			);
			//output to json format
			echo json_encode($output);
			return;
		}
		$logs = $this->Log_model->getLogs($washerId, $fromDate, $toDate);				

		$data = array();
		foreach($logs as $log)
		{
			$row = array();
			$row[] = $log->dt;
			if($log->mode !=1) continue;
			$row[] = $log->program;
			$row[] = $log->estimate_time;
			$data[] = $row;
		}
		$output = array(
			"draw" => null,
			"recordsTotal" => count($data),
			"recordsFiltered" => count($data),
			"data" => $data,
		);
		//output to json format
		echo json_encode($output);
	}

	public function get_washer_time_summary($fromDate='', $toDate='')
	{
		// $this->logonCheck();
		// if($washerId==0)
		// {
		// 	$output = array("draw" => null,
		// 		"recordsTotal" => 0,
		// 		"recordsFiltered" => 0,
		// 		"data" => array(),
		// 	);
		// 	//output to json format
		// 	echo json_encode($output);
		// 	return;
		// }
		// $logs = $this->Log_model->getLogs($washerId, $fromDate, $toDate);				

		// $data = array();
		// foreach($logs as $log)
		// {
		// 	$row = array();
		// 	$row[] = $log->dt;
		// 	if($log->mode !=1) continue;
		// 	$row[] = $log->program;
		// 	$row[] = $log->estimate_time;
		// 	$data[] = $row;
		// }
		// $output = array(
		// 	"draw" => null,
		// 	"recordsTotal" => count($data),
		// 	"recordsFiltered" => count($data),
		// 	"data" => $data,
		// );
		// //output to json format
		// echo json_encode($output);
	}

	public function get_dryer_time_details($washerId=0, $fromDate='', $toDate='')
	{
		$this->logonCheck();
		if($washerId==0)
		{
			$output = array("draw" => null,
				"recordsTotal" => 0,
				"recordsFiltered" => 0,
				"data" => array(),
			);
			//output to json format
			echo json_encode($output);
			return;
		}
		$logs = $this->Log_model->getLogs($washerId, $fromDate, $toDate);				

		$data = array();
		foreach($logs as $log)
		{
			$row = array();
			$row[] = $log->dt;
			if($log->mode !=1) continue;
			$row[] = $log->program;
			$row[] = $log->estimate_time;
			$data[] = $row;
		}
		$output = array(
			"draw" => null,
			"recordsTotal" => count($data),
			"recordsFiltered" => count($data),
			"data" => $data,
		);
		//output to json format
		echo json_encode($output);
	}	

	public function get_dryer_time_summary($fromDate='', $toDate='')
	{
		// $this->logonCheck();
		// if($washerId==0)
		// {
		// 	$output = array("draw" => null,
		// 		"recordsTotal" => 0,
		// 		"recordsFiltered" => 0,
		// 		"data" => array(),
		// 	);
		// 	//output to json format
		// 	echo json_encode($output);
		// 	return;
		// }
		// $logs = $this->Log_model->getLogs($washerId, $fromDate, $toDate);				

		// $data = array();
		// foreach($logs as $log)
		// {
		// 	$row = array();
		// 	$row[] = $log->dt;
		// 	if($log->mode !=1) continue;
		// 	$row[] = $log->program;
		// 	$row[] = $log->estimate_time;
		// 	$data[] = $row;
		// }
		// $output = array(
		// 	"draw" => null,
		// 	"recordsTotal" => count($data),
		// 	"recordsFiltered" => count($data),
		// 	"data" => $data,
		// );
		// //output to json format
		// echo json_encode($output);
	}

	
	

	public function get_dryer_activity_details($fromDate='', $toDate='')
	{
		// $this->logonCheck();
		// if($washerId==0)
		// {
		// 	$output = array("draw" => null,
		// 		"recordsTotal" => 0,
		// 		"recordsFiltered" => 0,
		// 		"data" => array(),
		// 	);
		// 	//output to json format
		// 	echo json_encode($output);
		// 	return;
		// }
		// $logs = $this->Log_model->getLogs($washerId, $fromDate, $toDate);				

		// $data = array();
		// foreach($logs as $log)
		// {
		// 	$row = array();
		// 	$row[] = $log->dt;
		// 	if($log->mode !=1) continue;
		// 	$row[] = $log->program;
		// 	$row[] = $log->estimate_time;
		// 	$data[] = $row;
		// }
		// $output = array(
		// 	"draw" => null,
		// 	"recordsTotal" => count($data),
		// 	"recordsFiltered" => count($data),
		// 	"data" => $data,
		// );
		// //output to json format
		// echo json_encode($output);
	}

	public function get_dryer_id_details($washerId=0, $fromDate='', $toDate='')
	{
		$this->logonCheck();
		if($washerId==0)
		{
			$output = array("draw" => null,
				"recordsTotal" => 0,
				"recordsFiltered" => 0,
				"data" => array(),
			);
			//output to json format
			echo json_encode($output);
			return;
		}
		$logs = $this->Log_model->getLogs($washerId, $fromDate, $toDate);				

		$data = array();
		foreach($logs as $log)
		{
			$row = array();
			$row[] = $log->dt;
			if($log->mode !=1) continue;
			$row[] = $log->program;
			$row[] = $log->estimate_time;
			$data[] = $row;
		}
		$output = array(
			"draw" => null,
			"recordsTotal" => count($data),
			"recordsFiltered" => count($data),
			"data" => $data,
		);
		//output to json format
		echo json_encode($output);
	}

	public function get_dryer_id_summary($fromDate='', $toDate='')
	{
		// $this->logonCheck();
		// if($washerId==0)
		// {
		// 	$output = array("draw" => null,
		// 		"recordsTotal" => 0,
		// 		"recordsFiltered" => 0,
		// 		"data" => array(),
		// 	);
		// 	//output to json format
		// 	echo json_encode($output);
		// 	return;
		// }
		// $logs = $this->Log_model->getLogs($washerId, $fromDate, $toDate);				

		// $data = array();
		// foreach($logs as $log)
		// {
		// 	$row = array();
		// 	$row[] = $log->dt;
		// 	if($log->mode !=1) continue;
		// 	$row[] = $log->program;
		// 	$row[] = $log->estimate_time;
		// 	$data[] = $row;
		// }
		// $output = array(
		// 	"draw" => null,
		// 	"recordsTotal" => count($data),
		// 	"recordsFiltered" => count($data),
		// 	"data" => $data,
		// );
		// //output to json format
		// echo json_encode($output);
	}	





	public function delete_supply()
	{
		$this->logonCheck();
		$Id = $this->input->post("Id");		
		$this->Supply_model->deleteRow(['Id'=>$Id]);
		echo json_encode("1");
	}
	public function get_supplies()
	{
		$this->logonCheck();
		$datas = [];
		$rows = $this->Supply_model->getDatas(null);
		foreach($rows as $item)
		{
			$row = [];
			$row[] = $item->Id;
			$row[] = $item->name;
			$row[] = $item->price;
			$row[] = $item->qty;
			$strAction = '<a href="javascript:void(0)" class="on-default edit-row m-r-10" '.
				'onclick="onEdit('.$item->Id.')" title="Edit" ><i class="fa fa-pencil"></i></a>'.
				'<a href="javascript:void(0)" class="on-default remove-row m-r-10" '.
				'onclick="onRemove('.$item->Id.')" title="Remove" ><i class="fa fa-trash-o text-danger"></i></a>';
			$row[] = $strAction;	
			$datas[] = $row;
		}

		$output = array(
			"draw" => null,
			"recordsTotal" => count($datas),
			"recordsFiltered" => count($datas),
			"data" => $datas,
		);
		//output to json format
		echo json_encode($output);
	}
	
	private function saveImage($imgString)
	{
		$idx = strpos($imgString, ',');
		if($idx <0)return '';
		$headerStr = substr ( $imgString , 0, $idx );

		$idx1 = strpos($headerStr, '/');
		$idx2 = strpos($headerStr, ';');
		if($idx1 <0 || $idx2 <0) return '';
		$ext = substr($headerStr, $idx1+1, $idx2 - $idx1-1);

		$tmpfileName = time().'.'.$ext; 
		if(!is_dir("uploads/image/supply")) {
			mkdir("uploads/image/supply/");
		}

		$filePath = 'uploads/image/supply/'.$tmpfileName;
		$myfile = fopen($filePath, "w");
		fwrite( $myfile, base64_decode( substr ( $imgString , $idx+1 ) ));
		fclose( $myfile );
		return $filePath;
	}

	public function addEdit_supply()
	{
		$this->logonCheck();
		$Id = $this->input->post("Id");
		$name = $this->input->post("name");
		$price = $this->input->post("price");
		$img = $this->input->post("img");

		$data = ['name'=>$name, 'price'=>$price];
		if($Id!='' && $Id >0)
		{
			if($img!="")
			{
				$orgData = $this->Supply_model->getRow(['Id'=>$Id]);
				if($orgData->img !="" && file_exists(base_url($orgData->img)))
				{
					unlink(base_url($orgData->img));
				}
				$newFile = $this->saveImage($img);				
				$data['img'] = $newFile;
			}
			$this->Supply_model->updateData(['Id'=>$Id], $data);
		}
		else
		{
			if($img!="")
			{
				$newFile = $this->saveImage($img);				
				$data['img'] = $newFile;
			}
			$this->Supply_model->insertData($data);
		}		
		echo json_encode("1");
	}

	
	public function charge_supply()
	{
		$this->logonCheck();
		$Id = $this->input->post("Id");
		$charge = $this->input->post("charge");

		if($Id =='' || $Id ==0 || $charge <= 0)
		{
			echo json_encode("0");
			return;
		}

		$supply = $this->Base_model->getRow('tbl_supply', ['Id'=>$Id]);
		if($supply == null)
		{
			echo json_encode("0");
			return;
		}

		//update supply transaction table
		$this->Base_model->insertData('tbl_supply_transaction', 
			['dt'=>date('Y-m-d H:i:s'), 
			'supply_id'=> $supply->Id, 
			'price' => $supply->price,  
			'count' => $charge,
			'action' => 'charge',
			'org_qty'=> $supply->qty,
			'new_qty' => $supply->qty + $charge,
			'type' => 'none',
		]);

		//update supply table
		$this->Base_model->updateData('tbl_supply', ['Id'=>$Id], ['qty'=>$supply->qty + $charge]);
		echo json_encode("1");
	}



	public function clearSupplyTransactionHistory()
	{
		$this->logonCheck();
		$this->Setting_model->setSupplyTrxClearTime(date("Y-m-d H:i:s"));
	}

	private function find_data($lst, $id)
	{
		foreach($lst as $item)
		{
			if($item->Id == $id)
			{
				return $item;
			}
		}
		return null;
	}
	public function getSupplyTransaction()
	{
		$this->logonCheck();
		$startDate = $this->Setting_model->getSupplyTrxClearTime();

		$supplies = $this->Base_model->getDatas('tbl_supply', null);
		$rows = $this->Base_model->getDatas('tbl_supply_transaction', ['dt >='=>$startDate], 'dt');
		$res = [];
		foreach($rows as $row)
		{
			$supply = $this->find_data($supplies, $row->supply_id);
			if($supply == null) continue;
			$data = [];
			$data[] = $supply->name;
			$data[] = $row->price;
			$data[] = $row->count;
			$data[] = $row->action;
			$data[] = $row->org_qty;
			$data[] = $row->new_qty;
			$data[] = $row->type;
			$data[] = $row->dt;

			$res[]= $data;
		}
		$output = array(
			"draw" => "",
			"recordsTotal" => count($res),
			"recordsFiltered" => count($res),
			"data" => $res,
		);
		echo json_encode($output);
	}	




	

}