<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require 'vendor/autoload.php';
header('Access-Control-Allow-Origin: *');
header('Accept: application/json');
//header('Content-Type: application/x-www-form-urlencoded');
header('Content-Type: application/json');

require 'lib/Braintree.php';
use Braintree\Configuration;

class Api1 extends CI_Controller {
	public function __construct(){
		parent::__construct();
		date_default_timezone_set('America/Chicago');

		$this->load->model('Machine_model');
		$this->load->model('Log_model');
		$this->load->model('UserVerify_model');
		$this->load->model('User_model');
		$this->load->model('Model_model');
		$this->load->model('Program_model');
		$this->load->model('Day_model');		
		$this->load->model('Service_model');
		$this->load->model('Option_model');		
		$this->load->model('Job_model');
		$this->load->model('Sms_model');
		$this->load->model('JobFinished_model');		
		$this->load->model('Transaction_model');		
		$this->load->model('Special_model');
		$this->load->model('DayService_model');	
		$this->load->model('KioskIncome_model');
		$this->load->model('Promotion_model');		
		$this->load->model('Setting_model');	
		$this->load->model('ServiceDryer_model');

		$this->gateway = new Braintree_Gateway([
			'environment' => 'production',
			'merchantId' => 'kx8tqhyktyy34r5k',
			'publicKey' => 'wp88vcvrcqk7rbp6',
			'privateKey' => '0435f0c623151a8104289a964358f123'
		]);
	}

	public function ping_Server() {
		echo "1";
	}

	public function braintree_token()
	{
		$token = $this->input->post('token');
		$user = $this->checkToken($token);
		if($user==null)
			return $this->reply(400, 'invalid token, params=> token', null);

		$this->reply(200, $this->gateway->clientToken()->generate(), null);	
	}

	public function braintree_payment()
	{
		$token = $this->input->post('token');
		$user = $this->checkToken($token);
		if($user==null)
			return $this->reply(400, 'invalid token, params=> token', null);

		$amount = $this->input->post('amount');
		if($amount <=0 )
			return $this->reply(400, 'invalid amount', null);
		$nonce = $this->input->post('nonce');
		if($nonce =="" )
			return $this->reply(400, 'invalid nonce', null);
		
		$result = $this->gateway->transaction()->sale([
			'amount' => strval($amount),
			'paymentMethodNonce' => $nonce,
			'options' => [
				'submitForSettlement' => True
			]
		]);
		$this->reply(200, "", $result);	
	}

	public function update_machine_status()
	{

		$token = $this->input->post('token');
		if($token!="WASHER_DRYER_MGR")
		{
			echo "[]";
			return;
		}		

		$data = $this->input->post('status');
		$machineStatus = json_decode($data);

		foreach($machineStatus as $state)
		{
			$statusCahnged = false;
			$machineId = 0;			

			$row = array(
				'machine_id'=>$state->id,
				'base_soft'=>$state->base_soft,
				'parameter_soft'=>$state->parameter_soft,
				'cpu_serial'=>$state->cpu_serial,
				'product_number'=>$state->product_number,
				'serial_number'=>$state->serial_number,
				'current_mode'=>$state->mode,
				'current_status1'=>$state->status1,
				'baud_rate'=>$state->baud_rate,
				'estimate_time'=>$state->estimate_time,
				'update_dt'=>date("Y-m-d H:i:s"));
			$machine = $this->Machine_model->getRow(array('machine_id'=>$state->id));				

			$prevMode = 0;	
			$prevStatus1 = 0;
			
			if($machine==null)
			{
				$row['total_time'] = $state->estimate_time;				
				$machineId = $this->Machine_model->insertData($row);
				$statusCahnged = true;
			}
			else
			{
				$prevMode = $machine->current_mode;	
				$prevStatus1 = $machine->current_status1;
	
				$machineId = $machine->Id;
				if($machine->current_mode != $state->mode || 
					$machine->current_status1!=$state->status1)
					$statusCahnged = true;
				$this->Machine_model->updateData(array('Id'=>$machine->Id), $row);
			}
			//update log table
			if($statusCahnged)
			{								
				$this->Log_model->insertData(array(
					'machine_id'=>$machineId,
					'dt'=>date("Y-m-d H:i:s"),
					'action'=>'',
					'mode'=>$state->mode,
					'status1'=>$state->status1,
					'program'=>$state->program,
					'options'=>$state->options,
					'estimate_time'=>$state->estimate_time,
					'extra_drying_time'=>$state->extra_drying_time,
					'weight'=>$state->weight,
				));
			}

			if($prevMode ==0 && $state->mode==1)
			{
				$this->Machine_model->setField('total_time',$state->estimate_time, array('Id'=>$machineId));
				//started
				$job = $this->Job_model->getRow(array('machine_id'=>$machineId,'status'=>'ready'));
				if($job!=null)
				{
					$this->Job_model->setField('status', 'busy', array('Id'=>$job->Id), TRUE);
				}
			}

			//if mode == idle and   state !=  machine occupy
			if($state->mode==0 &&  ($state->status1 & 8) ==0)
			{
				//finished
				$job = $this->Job_model->getRow(array('machine_id'=>$machineId,'status'=>'busy'));
				if($job!=null)
				{
					//insert finished job table
					$finishData = get_object_vars($job);
					unset($finishData['Id']);
					$finishData['finish_dt'] = date("Y-m-d H:i:s");
					$this->JobFinished_model->insertData($finishData);

					//remove from job table
					$this->Job_model->deleteRow(array('Id'=>$job->Id));

					//if finished dryer job and it's free service increase dryer_used_count
					if($job->machine_type==1 && $job->price==0)
					{
						$this->increaseFreeDryerUsedCount($job->user_id);
					}
				}
			}

			
			// else if($prevMode ==1 && $state->mode==0)
			// {
			// 	//finished
			// 	$job = $this->Job_model->getRow(array('machine_id'=>$machineId,'status'=>'busy'));
			// 	if($job!=null)
			// 	{
			// 		//insert finished job table
			// 		$finishData = get_object_vars($job);
			// 		unset($finishData['Id']);
			// 		$finishData['finish_dt'] = date("Y-m-d H:i:s");
			// 		$this->JobFinished_model->insertData($finishData);

			// 		//remove from job table
			// 		$this->Job_model->deleteRow(array('Id'=>$job->Id));

			// 		//if finished dryer job and it's free service increase dryer_used_count
			// 		if($job->machine_type==1 && $job->price==0)
			// 		{
			// 			$this->increaseFreeDryerUsedCount($job->user_id);
			// 		}
			// 	}
			// }


			if($state->mode==1 && $state->estimate_time==0)
			{
				$job = $this->Job_model->getRow(array('machine_id'=>$machineId,'status'=>'busy'));
				if($job!=null && $job->need_sms==1)
				{
					$user = $this->User_model->getRow('tbl_user', array('Id'=>$job->user_id));
					if($user!=null)
					{
						$smsCtx = "Your Wash at Washer Number ".$state->id." finished, please collect your Cloths";
						//send sms
						$this->Sms_model->insertData(array('phone'=>$user->phone, 'code'=>$smsCtx));
					}
					//remove send_sms flag
					$this->Job_model->setField('need_sms', 0, array('Id'=>$job->Id));
				}
			}
		}

		$data = array();
		//return job entries
		$activeJobs = $this->Job_model->getDatas(array('status'=>'ready'));
		foreach($activeJobs as $job)
		{
			$machine = $this->Machine_model->getRow(array('Id'=>$job->machine_id));
			if($machine==null) continue;
			$data[] = array('id'=>$machine->machine_id, 'prog'=>$job->program_id, 'opts'=>$job->options);
		}
		echo json_encode($data);
	}

	public function reply($status, $message, $data)
	{
		$result = array('status'=>$status, 'message'=>$message, 'data'=>$data);
		echo json_encode($result);
	}

	public function kiosk_login() {
		 $phone = $this->input->post('phone');
		 if($phone=="") 
		 	return $this->reply(400, 'invalid phone number param', null);

		$token = $this->input->post('token');			
		if($token!="kiosk_mgr_TTK") 
			return $this->reply(400, 'invalid uuid param', null);
			
		if (substr($phone, 0, 1) != '1')
		 	$phone = '1'.$phone;

		$user = $this->User_model->getRow('tbl_user', array('phone'=>$phone));
		if($user == null) 
			return $this->reply(400, 'invalid phone number/uuid', null);

		//register sms
		$code = rand(0,9).rand(0,9).rand(0,9).rand(0,9);

		$this->Sms_model->insertData(array('phone'=>$phone, 'code'=>$code));
		$this->reply(200, 'success', $code);
	}


	public function kiosk_deposit() {
		$phone = $this->input->post('phone');
		if($phone=="") 
			return $this->reply(400, 'invalid phone number param', null);

	   $token = $this->input->post('token');			
	   if($token!="kiosk_mgr_TTK") 
		   return $this->reply(400, 'invalid token', null);

	   $balance = $this->input->post('balance');
		if($balance <=0) 
		   return $this->reply(400, 'invalid balance', null);

		$bills = $this->input->post('bills');
		if($bills=="")
			return $this->reply(400, 'invalid bills', null);
	

	   if (substr($phone, 0, 1) != '1')
		   $phone = '1'.$phone;

		$lang = $this->input->post('lang');

		$user = $this->User_model->getRow('tbl_user', array('phone'=>$phone));
		if($user == null) 
			return $this->reply(400, 'invalid phone number', null);

		//find bonus
		$bonus = $this->Promotion_model->getBonus($balance);

		//update user table
		$user->balance = $user->balance + $balance + $bonus;
		$this->User_model->setField('balance', $user->balance, array('Id'=>$user->Id), FALSE);	

		//update transaction table
		$data = array('user_id'=>$user->Id, 'org_price'=>$balance,
					'price'=>$balance + $bonus, 
					'balance'=>$user->balance, 'reason'=>'deposit', 'dt'=>date("Y-m-d H:i:s"));
		$this->Transaction_model->insertData($data);

		//sms
		if($lang=="eng")		
			$this->Sms_model->insertData(array('phone'=>$phone, 'code'=>"New balance is ".$user->balance));
		else
			$this->Sms_model->insertData(array('phone'=>$phone, 'code'=>"El nuevo saldo es de ".$user->balance));

		//log //$bills
		$billEntries = explode(',',$bills);
		foreach($billEntries as $billEntry)
		{
			$entry = explode('-',$billEntry);
			if(count($entry) != 2)continue;
			$this->KioskIncome_model->insertData(array('user_id'=>$user->Id, 
				'price'=>$entry[0],'cnt'=>$entry[1], 'dt'=>date("Y-m-d H:i:s")));
		}
	    $this->reply(200, 'success', null);
   }


	public function login() {


		// $phone = $this->input->post('phone');
		// if($phone=="") 
		// 	return $this->reply(400, 'invalid phone number param', null);

		$uuid = $this->input->post('uuid');			
		if($uuid=="") 
			return $this->reply(400, 'invalid uuid param', null);
			
		// if (substr($phone, 0, 1) != '1')
		// 	$phone = '1'.$phone;

		//$user = $this->User_model->getRow('tbl_user', array('phone'=>$phone, 'uuid'=>$uuid));
		$user = $this->User_model->getRow('tbl_user', array('uuid'=>$uuid));
		if($user == null) 
			return $this->reply(400, 'invalid phone number/uuid', null);

		$token = $user->Id.'_'.$user->phone.'_'.rand(2000, 6000);
		//update token
		$this->User_model->setField('token', $token, array('Id'=>$user->Id), TRUE);

		$this->reply(200, 'success', 
			array('Id'=>$user->Id, 'phone'=>$user->phone, 'token'=>$token,
				  'balance'=>$user->balance));
	}

	public function get_user() {

		$token = $this->input->post('token');
		if($token=="") 
			return $this->reply(400, 'invalid token param', null);
		$user = $this->checkToken($token);

		if($user == null)
			return $this->reply(400, 'invalid user', null);
		$this->reply(200, 'success', 
			array('Id'=>$user->Id, 'phone'=>$user->phone, 'token'=>$token,
				  'balance'=>$user->balance));
	}

	public function send_sms() {
		$phone = $this->input->post('phone');
		if($phone=="") 
			return $this->reply(400, 'invalid phone number', null);

		if (substr($phone, 0, 1) != '1')
			$phone = '1'.$phone;

		$this->UserVerify_model->deleteByField('phone', $phone);
		$code = rand(0,9).rand(0,9).rand(0,9).rand(0,9);
		$this->UserVerify_model->insertData(array('phone'=>$phone, 
			'verify_code'=>$code, 'dt'=>date('Y-m-d h:i:s')));

		//update sms table.
		//$this->regster_sms($phone, $code);
		//update request table
		$this->Sms_model->insertData(array('phone'=>$phone, 'code'=>$code));
		$this->reply(200, 'send',null);
	}

	public function verify_user()
	{
		$phone = $this->input->post('phone');
		if($phone=="") 
			return $this->reply(400, 'invalid phone number', null);
		if (substr($phone, 0, 1) != '1')
			$phone = '1'.$phone;

		$uuid = $this->input->post('uuid');			
		if($uuid=="") 
			return $this->reply(400, 'invalid uuid param', null);	

		$code = $this->input->post('code');
		if($code=="") 
			return $this->reply(400, 'invalid param [code]', null);
		
		$verify = $this->UserVerify_model->getRow(array('phone'=>$phone));
		if($verify==null)	
			return $this->reply(400, "didn't send sms!", null);
		if($verify->verify_code!=$code)
			return $this->reply(400, "invalid  verify code!", null);

		$this->UserVerify_model->deleteByField('phone', $phone);

		$user = $this->User_model->getRow('tbl_user', array('phone'=>$phone));
		if($user!=null)
			$this->User_model->setField('uuid', $uuid, array('Id'=>$user->Id), TRUE);
		else
			$this->User_model->insertData('tbl_user', array('phone'=>$phone, 'uuid'=>$uuid, 'balance'=>0));

		$user = $this->User_model->getRow('tbl_user', array('phone'=>$phone));
		if($user == null) 
			return $this->reply(400, 'invalid phone number', null);

		$token = $user->Id.'_'.$user->phone.'_'.rand(2000, 6000);
		//update token
		$this->User_model->setField('token', $token, array('Id'=>$user->Id), TRUE);
	
		$this->reply(200, 'success', 
			array('Id'=>$user->Id, 'phone'=>$user->phone, 'token'=>$token,
				  'balance'=>$user->balance));
	}

	public function checkToken($token)
	{
		if($token==null || $token=="") return null;
		$datas = explode("_" ,$token);
		if(count($datas) < 2) return null;
		
		$user = $this->User_model->getRow('tbl_user', 
			array('Id'=>$datas[0], 'phone'=>$datas[1], 'token'=>$token));
		if($user==null) return null;
		return $user;
	}

	public function save_profile()
	{
		$token = $this->input->post('token');
		$user = $this->checkToken($token);
		if($user==null)
			return $this->reply(400, 'invalid token, params=> token', null);

		$cardNum = $this->input->post('cardNum');
		$expDate = $this->input->post('expDate');
		$address = $this->input->post('address');
		$city = $this->input->post('city');
		$state = $this->input->post('state');
		$zipCode = $this->input->post('zipCode');
		$cardName = $this->input->post('cardName');

		$data = array('card_num'=>$cardNum, 'card_exp_date'=>$expDate, 
			'address'=>$address, 'city'=>$city, 'state'=>$state, 
			'zip_code'=>$zipCode, 'card_name'=>$cardName);
		$this->User_model->updateData('tbl_user', array('Id'=>$user->Id), $data);
		$this->reply(200, "", null);
	}

	public function ephemeral_keys()
	{
		echo "AAABBB";
	}

	public function base_infos()
	{
		$token = $this->input->post('token');
		$user = $this->checkToken($token);
		if($user==null)
			return $this->reply(400, 'invalid token, params=> token', null);

		//mahines
		$machines = $this->Machine_model->getDatas(null);
		$mas = array();
		foreach($machines as $machine)		
		{
			$model = $this->Model_model->getRow(array('model'=>$machine->product_number));
			if($model==null) continue;

			$row = array();
			$row['id'] = $machine->Id;
			$row['machine_id'] = $machine->machine_id;
			$row['model_id'] = $model->Id;
			$row['product_number'] = $machine->product_number;

			if($model->type==0)
				$row['machine_type'] = "Washer";
			else
				$row['machine_type'] = "Dryer";
			$row['max_weight'] = $model->weight;

			$mas[] = $row;
		}

		//opts
		$options = $this->Option_model->getDatas(null);

		//progs
		$programs = $this->Program_model->getDatas(null);

		//days
		$days = $this->Day_model->getDatas(null);

		//svcs
		$service  = $this->Service_model->getDatas(null);

		//profile
		$profile = array('cardNum'=>$user->card_num, 'expDate'=>$user->card_exp_date,
			'address'=>$user->address, 'city'=>$user->city, 'state'=>$user->state,
			'zipCode'=>$user->zip_code, 'cardName'=>$user->card_name);

		$this->reply(200, 'ok', array('machines'=>$mas, 'days'=>$days, 
							'progs'=>$programs,'opts'=>$options, 'svcs'=>$service,
						'profile'=>$profile));
	}

	public function machines()
	{
		$token = $this->input->post('token');
		$user = $this->checkToken($token);
		if($user==null)
			return $this->reply(400, 'invalid token, params=> token', null);

		$type = $this->input->post('type');

		$machines = $this->Machine_model->getDatas(null);
		$data = array();
		foreach($machines as $machine)		
		{
			$model = $this->Model_model->getRow(array('model'=>$machine->product_number));
			if($model==null) continue;

			$row = array();
			$row['id'] = $machine->Id;
			$row['machine_id'] = $machine->machine_id;
			$row['model_id'] = $model->Id;
			$row['product_number'] = $machine->product_number;

			if($model->type==0)
				$row['machine_type'] = "Washer";
			else
				$row['machine_type'] = "Dryer";
			$row['max_weight'] = $model->weight;

			$data[] = $row;
		}
		$this->reply(200, 'ok', $data);
	}


	public function options()
	{
		$token = $this->input->post('token');
		$user = $this->checkToken($token);
		if($user==null)
			return $this->reply(400, 'invalid token, params=> token', null);

		$options = $this->Option_model->getDatas(null);
		$this->reply(200, 'ok', $options);
	}	
	
	public function programs()
	{
		$token = $this->input->post('token');
		$user = $this->checkToken($token);
		if($user==null)
			return $this->reply(400, 'invalid token, params=> token', null);

		$programs = $this->Program_model->getDatas(null);
		$this->reply(200, 'ok', $programs);
	}
	public function days()
	{
		$token = $this->input->post('token');
		$user = $this->checkToken($token);
		if($user==null)
			return $this->reply(400, 'invalid token, params=> token', null);

		$days = $this->Day_model->getDatas(null);
		$this->reply(200, 'ok', $days);		
	}

	public function priceList()
	{
		$token = $this->input->post('token');
		$user = $this->checkToken($token);
		if($user==null)
			return $this->reply(400, 'invalid token, params=> token', null);

		$service  = $this->Service_model->getDatas(null);
		if($service==null)
			return $this->reply(400, 'no exist service', null);

		$this->reply(200, 'ok', $service);
	}



	public function transactions()
	{
		$token = $this->input->post('token');
		$user = $this->checkToken($token);
		if($user==null)
			return $this->reply(400, 'invalid token, params=> token', null);
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
				$row['dt'] = $tr->dt;
				$row['machine'] = $machine->machine_id;
				$row['program'] = $program->name;
				$row['price'] = '$ '.$tr->price;
				$row['balance'] = '$ '.$tr->balance;
				$data[]= $row;
			}
			else
			{
				$row = array();
				$row['dt'] = $tr->dt;
				$row['machine'] = "";
				$row['program'] = "Deposit";
				$row['price'] = $tr->price;
				$row['balance'] = $tr->balance;

				$data[]= $row;
			}			
		}
		$this->reply(200, 'ok', $data);
	}

	public function busyMachines()
	{
		$token = $this->input->post('token');
		$user = $this->checkToken($token);
		if($user==null)
			return $this->reply(400, 'invalid token, params=> token', null);

		$data = array();
		$machines  = $this->Machine_model->getDatas(array('current_mode'=>1));
		foreach($machines as $machine)
		{
			$mins = abs(strtotime(date("Y-m-d H:i:s")) - strtotime($machine->update_dt));
			$mins = $mins/60;
			if($mins >= 2) 
				continue;
			//check if machine is for user.
			$job = $this->Job_model->getRow(array('user_id'=>$user->Id, 'machine_id'=>$machine->Id));
			if($job ==null)
				continue;
				
			$row = array();
			$row['machine_id']	= $machine->machine_id;
			$row['left']	= $machine->estimate_time. " min";
			$total = $machine->total_time;
			if($total==0)
				$total = 25;
			if($total < $machine->estimate_time)
				$total = $machine->estimate_time;

			$percent = ( ($total - $machine->estimate_time) / $total) * 100;
			if($percent > 100)
				$percent = 100;			
			$row['pecent']	= $percent.'*, '.(100-$percent).'*';
			$data[] = $row;
		}
		$this->reply(200, 'ok', $data);
	}

	

	public function machine_is_idle()
	{
		$token = $this->input->post('token');
		$user = $this->checkToken($token);
		if($user==null)
			return $this->reply(400, 'invalid token, params=> token', null);
		$machineId = $this->input->post('machineId');
		if($machineId==0)
			return $this->reply(400, 'invalid params [machineId]', null);

		$machine = $this->Machine_model->getRow(array('machine_id'=>$machineId));
		if($machine == null)
			return $this->reply(400, "This washer is busy or you entered a wrong washer number please try again", null);
		if($machine->current_mode!=0)
			return $this->reply(400, "This washer is busy or you entered a wrong washer number please try again", null);
		//check updated time

		$mins = abs(strtotime(date("Y-m-d H:i:s")) - strtotime($machine->update_dt));
		$mins = $mins/60;
		if($mins >= 2)
			return $this->reply(400, "System is showing this washer is out of service", null);

			
		$job = $this->Job_model->getRow(array('machine_id'=>$machine->Id, 'status'=>'ready'));
		if($job == null ) 
			$job = $this->Job_model->getRow(array('machine_id'=>$machine->Id, 'status'=>'busy'));

		if($job != null)
			return $this->reply(400, 'This washer is busy or you entered a wrong washer number please try again', null);

		return $this->reply(200, "idle", null);
	}

	public function specials()
	{
		$token = $this->input->post('token');
		$user = $this->checkToken($token);
		if($user==null)
			return $this->reply(400, 'invalid token, params=> token', null);

		$specs = $this->Special_model->getDatas(null);
		return $this->reply(200, "idle", $specs);
	}

	public function getServicePriceOfDay()
	{
		$token = $this->input->post('token');
		$user = $this->checkToken($token);
		if($user==null)
			return $this->reply(300, 'invalid token, params=> token', null);
		$modelId = $this->input->post('modelId');
		if($modelId==0)
			return $this->reply(400, 'invalid params [modelId]', null);

		$programId = $this->input->post('programId');
		if($programId==0)
			return $this->reply(400, 'invalid params [programId]', null);	
			
		$price = $this->getDayServicePrice($modelId, $programId);
		
		// $date = date("Y-m-d H:i:s");
		// $curDay = date('w', strtotime($date))+1;
		// $curHour = date('H',strtotime($date));

		// $price = 0;
		// $svcs = $this->DayService_model->getDatas(array('model_id'=>$modelId, 'program_id'=>$programId));
		// foreach($svcs as $svc)
		// {
		// 	//check day and time.
		// 	if($curDay < $svc->start_day) continue;
		// 	if($curDay > $svc->end_day) continue;

		// 	if($svc->start_time < $svc->end_time)
		// 	{
		// 		if($curHour < $svc->start_time) continue;
		// 		if($curHour >= $svc->end_time) continue;	
		// 	}
		// 	else
		// 	{
		// 		$curHour += 24;
		// 		if($curHour < $svc->start_time) continue;
		// 		if($curHour >= $svc->end_time + 24) continue;
		// 	}

		// 	$price = $svc->price;
		// 	break;
		// }
		//return $this->reply(200, strval($price), null);
		return $this->reply(200, strval($price), null);		
	}	

	public function getServicePricesOfDay()
	{
		$token = $this->input->post('token');
		$user = $this->checkToken($token);
		if($user==null)
			return $this->reply(300, 'invalid token, params=> token', null);
		$modelId = $this->input->post('modelId');
		if($modelId==0)
			return $this->reply(400, 'invalid params [modelId]', null);
		
		$date = date("Y-m-d H:i:s");
		$curDay = date('w', strtotime($date))+1;
		$curHour = date('H',strtotime($date));

		$prices = array();
		$prices[] = $this->getDayServicePrice($modelId, 1);
		$prices[] = $this->getDayServicePrice($modelId, 2);
		$prices[] = $this->getDayServicePrice($modelId, 3);
		return $this->reply(200, "", $prices);
	}	

	public function getDayServicePrice($modelNumber, $programId)
	{
		$price = 0;
		$model = $this->Model_model->getRow(array("model"=>$modelNumber));
		if($model==null) return 0;

		$date = date("Y-m-d H:i:s");
		$curDay = date('w', strtotime($date))+1;
		$curHour = date('H',strtotime($date));

		$svcs = $this->DayService_model->getDatas(array('model_id'=>$model->Id, 'program_id'=>$programId));
		foreach($svcs as $svc)
		{
			//check day and time.
			if($curDay < $svc->start_day) continue;
			if($curDay > $svc->end_day) continue;

			if($svc->start_time < $svc->end_time)
			{
				if($curHour < $svc->start_time) continue;
				if($curHour >= $svc->end_time) continue;	
			}
			else
			{
				if($curHour < $svc->start_time) continue;				
				if($curDay == $svc->end_day) continue;

				if($curHour >= $svc->end_time + 24) continue;
			}

			$price = $svc->price;
			break;
		}
		return $price;
	}

	public function registerJob()
	{
		$token = $this->input->post('token');
		$user = $this->checkToken($token);
		if($user==null)
			return $this->reply(300, 'invalid token, params=> token', null);
		$machineId = $this->input->post('machineId');
		if($machineId==0)
			return $this->reply(400, 'invalid params [machineId]', null);

		$programId = $this->input->post('programId');
		if($programId==0)
			return $this->reply(400, 'invalid params [programId]', null);	

		$options = $this->input->post('options');
		
		$price = $this->input->post('price');
		if($price ==0)
			return $this->reply(400, 'invalid params [price]', null);
		$needSms = $this->input->post('needSms');
		if($needSms ==null)
			$needSms =0;

		//check machien status
		$machine = $this->Machine_model->getRow(array('machine_id'=>$machineId));
		if($machine == null)
			return $this->reply(402, "This washer is busy or you entered a wrong washer number please try again", null);		
		if($machine->current_mode!=0)
			return $this->reply(403, "This washer is busy or you entered a wrong washer number please try again", null);

		$dayService = $this->getDayServicePrice($machine->product_number, $programId);

		$actualPrice = $price;
		$price -= $dayService;

		//check price.
		if($user->balance < $price)
			return $this->reply(401, 'Unsufficient Funds for this transaction please add more Funds', null);

		//check if machine is occupied
		$job = $this->Job_model->getRow(array('machine_id'=>$machine->Id, 'status'=>'ready'));
		if($job == null ) 
			$job = $this->Job_model->getRow(array('machine_id'=>$machine->Id, 'status'=>'busy'));
		if($job != null)
		{
			if($job->user_id == $user->Id)
			{
				if($job->status=='ready')
					return $this->reply(404, 'This washer is busy or you entered a wrong washer number please try again', null);
				else
					return $this->reply(405, 'This washer is busy or you entered a wrong washer number please try again', null);
			}
			else
				return $this->reply(406, 'This washer is busy or you entered a wrong washer number please try again', null);
		}		

		//update job table
		$data = array('user_id'=>$user->Id, 'machine_id'=>$machine->Id, 
			'program_id'=>$programId, 'options'=>$options,
			'price'=>$price, 'need_sms'=>$needSms, 'dt'=>date("Y-m-d H:i:s"));
		$this->Job_model->insertData($data);

		//update balance
		$user->balance = $user->balance - $price;
		$this->User_model->updateData('tbl_user', array('Id'=>$user->Id), $user);

		//register transaction		
		unset($data['need_sms']);
		$data['reason'] = 'withdraw';
		$data['org_price'] = $actualPrice;
		$data['balance'] = $user->balance;
		$this->Transaction_model->insertData($data);

		return $this->reply(200, "successful booked", null);
	}

	public function increaseFreeDryerUsedCount($userId)
	{
		$critiaDate = date("Y-m-d H:i:s", (strtotime(date("Y-m-d H:i:s")) - 3600)); 
		$condition = "user_id=".$userId."and dryer_used < 2 and finish_dt > '".$critiaDate."'";
		$washerJobs = $this->JobFinished_model->getDatasByCondition($condition);
		if(count($washerJobs) ==0) return;
		$this->JobFinished_model->setField('dryer_used', $washerJobs->dryer_used +1, 
			array('Id'=>$washerJobs[0]->Id));
	}


	public function machineStatus($machineId)
	{
		$machine  = $this->Machine_model->getRow(array('Id'=>$machineId));
		if($machine == null)
			return null;

		$row = array();		
		$row['machine_id']	= $machine->machine_id;
		if($machine->current_mode==1)
			$row['busy'] = 1;
		else
			$row['busy'] = 0;
		$row['left']	= $machine->estimate_time. " min";
		$total = $machine->total_time;
		if($total==0)
			$total = 25;
		if($total < $machine->estimate_time)
			$total = $machine->estimate_time;

		$percent = ( ($total - $machine->estimate_time) / $total) * 100;
		if($percent > 100)
			$percent = 100;			
		$row['pecent']	= $percent.'*, '.(100-$percent).'*';
		return $row;
	}

	public function freeDryerStatus($userId)
	{
		//first get finished washer job  in 60 min
		$critiaDate = date("Y-m-d H:i:s", (strtotime(date("Y-m-d H:i:s")) - 3600)); 
		$condition = "user_id=".$userId." and machine_type=0 and finish_dt > '".$critiaDate."'";
		$washerJobs = $this->JobFinished_model->getDatasByCondition($condition);

		$totalPossible = 0;
		//calculate possible dryer spins
		foreach($washerJobs as $washerJob)
		{
			$machine = $this->Machine_model->getRow(array('Id'=>$washerJob->mahine_id));
			if($machine ==null) continue;
			$model = $this->Model_model->getRow(array('model'=>$machine->product_number));
			if($model == null) continue;

			$dryerSpin = $this->ServiceDryer_model->getRow(array('model_id'=>$model->Id));
			if($dryerSpin == null) continue;
		
			$totalPossible += $dryerSpin->dryers;
		}

		//make sum for useed dryers
		//$totalPossible = count($washerJobs) * 2;
		
		//make sum for made dryer job count
		$condition = "user_id=".$userId." and dt > '".$critiaDate."' and machine_type=1 and price=0";
		$dryerJobs = $this->Job_model->getDatasByCondition($condition);

		$jobStates = array();
		foreach($dryerJobs as $job)
		{
			$status = $this->machineStatus($job->machine_id);
			if($status==null )continue;
			$jobStates[]= $status;
		}					
		$inProgress = count($jobStates);


		if($inProgress>= $totalPossible)
			$inProgress = $totalPossible;
		$leftCnt = $totalPossible - $inProgress;

		//used dryers
		$usedDryers = 0;
		foreach($washerJobs as $job)
		{
			$usedDryers += $job->dryer_used;
		}
		if($usedDryers >= $leftCnt)
			$usedDryers = $leftCnt;
		$leftCnt -= $usedDryers;
		$rests = array();
		for($i =0; $i<$leftCnt; $i++)
			$rests[] = $i;

		return array('total'=>$totalPossible, 'used'=>$usedDryers, 'rest'=>$rests, 'states'=>$jobStates);
	}


	public function getFreeDryerStatus()
	{
		$token = $this->input->post('token');
		$user = $this->checkToken($token);
		if($user==null)
			return $this->reply(400, 'invalid token, params=> token', null);

		$state = $this->freeDryerStatus($user->Id);
		return $this->reply(200, "ok", $state);
	}

	public function registerJobForDryer()
	{
		$token = $this->input->post('token');
		$user = $this->checkToken($token);
		if($user==null)
			return $this->reply(300, 'invalid token, params=> token', null);
		$machineId = $this->input->post('machineId');
		if($machineId==0)
			return $this->reply(400, 'invalid params [machineId]', null);

		$programId = $this->input->post('programId');
		if($programId==0)
			return $this->reply(400, 'invalid params [programId]', null);	
	
		$price = $this->input->post('price');

		if($price ==0)
		{
			$freeStatus = $this->freeDryerStatus($user->Id);
			if($freeStatus['total'] == 0)
				return $this->reply(400, 'No exist free dryer service', null);
		}
			 
		$needSms = $this->input->post('needSms');
		if($needSms ==null)
			$needSms =0;

		//check machien status
		$machine = $this->Machine_model->getRow(array('machine_id'=>$machineId));
		if($machine == null)
			return $this->reply(402, "This washer is busy or you entered a wrong washer number please try again", null);		
		if($machine->current_mode!=0)
			return $this->reply(403, "This washer is busy or you entered a wrong washer number please try again", null);

		//$dayService = $this->getDayServicePrice($machine->product_number, $programId);
		// $actualPrice = $price;
		// $price -= $dayService;

		//check price.
		if($user->balance < $price)
			return $this->reply(401, 'Unsufficient Funds for this transaction please add more Funds', null);

		//check if machine is occupied
		$job = $this->Job_model->getRow(array('machine_id'=>$machine->Id, 'status'=>'ready'));
		if($job == null ) 
			$job = $this->Job_model->getRow(array('machine_id'=>$machine->Id, 'status'=>'busy'));
		if($job != null)
		{
			if($job->user_id == $user->Id)
			{
				if($job->status=='ready')
					return $this->reply(404, 'This washer is busy or you entered a wrong washer number please try again', null);
				else
					return $this->reply(405, 'This washer is busy or you entered a wrong washer number please try again', null);
			}
			else
				return $this->reply(406, 'This washer is busy or you entered a wrong washer number please try again', null);
		}		

		//update job table
		$data = array('user_id'=>$user->Id, 'machine_id'=>$machine->Id,  'machine_type'=>1,
			'program_id'=>$programId,
			'price'=>$price, 'need_sms'=>$needSms, 'dt'=>date("Y-m-d H:i:s"));
		$this->Job_model->insertData($data);

		//update balance
		if($price >0)
		{
			$user->balance = $user->balance - $price;
			$this->User_model->updateData('tbl_user', array('Id'=>$user->Id), $user);	

			//register transaction		
			unset($data['need_sms']);
			$data['reason'] = 'withdraw';
			$data['org_price'] = $price;
			$data['balance'] = $user->balance;
			$this->Transaction_model->insertData($data);
		}

		return $this->reply(200, "successful booked", null);
	}


}