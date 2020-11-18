<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require 'vendor/autoload.php';
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type');
header('Accept: application/json');
//header('Content-Type: application/x-www-form-urlencoded');
header('Content-Type: application/json');

class KioskApi extends CI_Controller {
	public function __construct(){
		parent::__construct();
		date_default_timezone_set('America/Chicago');
				
		$this->load->model('Base_model');		
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
		$this->load->model('Kiosk2Income_model');
		$this->load->model('Promotion_model');		
		$this->load->model('Setting_model');	
		$this->load->model('HomeBank_model');		
		$this->load->model('CreditIncome_model');
		$this->load->model('Soap_model');
		$this->load->model('WashFold_model');		
		$this->load->model('FreeSetting_model');		
		$this->load->model('Track_model');
		$this->load->model('Supply_model');

		$this->token = "kiosk_mgr_TTK";
	}

	public function ping_Server() {
		echo '{"status":"1"}';
	}

	public function reply($status, $message, $data)
	{
		$result = array('status'=>$status, 'message'=>$message, 'data'=>$data);
		echo json_encode($result);
	}

	public function hopperCounts()
	{
		$token = $this->input->post('token');
		if($token!=$this->token)
			return $this->reply(400, 'invalid token, params=> token', null);
		
		$flag = $this->Setting_model->getHopperFlag();
		if($flag =="0")
			return $this->reply(201, 'no flag', null);

		$data = array();
		$coins = $this->HomeBank_model->getDatas(null);
		foreach($coins as $coin)
		{
			if($coin->cnt_for_set==0) 
				continue;
			$row = array('coin'=>$coin->coin, 'count'=>$coin->cnt_for_set + $coin->count);
			$data[] = $row;
		}
		return $this->reply(200, 'no flag', $data);
	}

	public function clearHopperFlag()
	{
		$token = $this->input->post('token');
		if($token!=$this->token)
			return $this->reply(400, 'invalid token, params=> token', null);
		$flag = $this->Setting_model->setHopperFlag("0");
		return $this->reply(200, 'ok', null);
	}



	public function base_infos()
	{
		$token = $this->input->post('token');
		if($token!=$this->token)
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

		//soaps
		$soaps = $this->Soap_model->getDatas(null);

		//supply
		$supplies = $this->Supply_model->getDatas(null);
		for($i=0; $i<count($supplies); $i++)
		{
			$supplies[$i]->img = base_url($supplies[$i]->img);
		}

		$freesettings =$this->FreeSetting_model->getDatas(null);

		$models = $this->Model_model->getDatas(null);

		$this->reply(200, 'ok', array('machines'=>$mas, 'days'=>$days, 
							'progs'=>$programs,'opts'=>$options, 'svcs'=>$service, 'soaps'=>$soaps,
							'supplies'=>$supplies,
							'freesettings'=>$freesettings, 'models'=>$models));
	}


	public function machine_is_idle()
	{
		$token = $this->input->post('token');
		if($token!=$this->token)
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

	public function getServicePriceOfDay()
	{
		$token = $this->input->post('token');
		if($token!=$this->token)
			return $this->reply(400, 'invalid token, params=> token', null);

		$modelId = $this->input->post('modelId');
		if($modelId==0)
			return $this->reply(400, 'invalid params [modelId]', null);

		$programId = $this->input->post('programId');
		if($programId==0)
			return $this->reply(400, 'invalid params [programId]', null);	
			
		$price = $this->getDayServicePrice($modelId, $programId);			
		return $this->reply(200, strval($price), null);
	}

	public function getServicePricesOfDay()
	{
		$token = $this->input->post('token');
		if($token!=$this->token)
			return $this->reply(400, 'invalid token, params=> token', null);

		$modelId = $this->input->post('modelId');
		if($modelId==0)
			return $this->reply(400, 'invalid params [modelId]', null);

		$data = array();
		$data[] = $this->getDayServicePrice($modelId, 1);
		$data[] = $this->getDayServicePrice($modelId, 2);
		$data[] = $this->getDayServicePrice($modelId, 3);
		return $this->reply(200, 'ok', $data);
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

	private function registerSMS($phone, $code)
	{
		$url= "http://api.edtsystems.com/home?key=R456&pwd=R@321&cell=".$phone."&sms=".$code;
		$curlOption = 	array(
		    CURLOPT_RETURNTRANSFER => true,     // return web page
		    CURLOPT_HEADER         => false,    // don't return headers
		    CURLOPT_FOLLOWLOCATION => true,     // follow redirects
		    CURLOPT_ENCODING       => "",       // handle all encodings
		    CURLOPT_USERAGENT      => "spider", // who am i
		    CURLOPT_AUTOREFERER    => true,     // set referer on redirect
		    CURLOPT_CONNECTTIMEOUT => 120,      // timeout on connect
		    CURLOPT_TIMEOUT        => 120,      // timeout on response
		    CURLOPT_MAXREDIRS      => 10,       // stop after 10 redirects
		    CURLOPT_SSL_VERIFYPEER => false     // Disabled SSL Cert checks
		);

		$ch = curl_init($url);
		curl_setopt_array( $ch, $curlOption );
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		// curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		// curl_setopt($ch, CURLOPT_USERPWD, $vgoApiKey); //Your credentials goes here
		$output = curl_exec($ch);
		curl_close($ch);	
		

		if(stristr($output, "Success")!=false) 
			return true;
		return false;
	}


	public function updateCoinCounts()
	{
		$token = $this->input->post('token');
		if($token!=$this->token)
			return $this->reply(400, 'invalid token, params=> token', null);

		$coins = $this->input->post('coins');
		if($coins=="")
			return $this->reply(400, 'invalid coins, params=> coins', null);

		//log //$bills
		$coinsEntries = explode(',',$coins);

		$bNeedSMS = false;
		foreach($coinsEntries as $coin)
		{
			$entry = explode('-',$coin);
			if(count($entry) != 2)continue;

			$coinEntry = $this->HomeBank_model->getRow(array('coin'=>$entry[0]));
			if($coinEntry != null && $entry[1] < $coinEntry->limit )
				$bNeedSMS = true;
			$this->HomeBank_model->setField('count', $entry[1], array('coin'=>$entry[0]), FALSE);
		}
		
		if($bNeedSMS)
		{
			$adminPhonoe = $this->Setting_model->getAdminPhoneNumber();
			if($adminPhonoe!="")
			{
				if (substr($adminPhonoe, 0, 1) != '1')
				$adminPhonoe = '1'.$adminPhonoe;
				//$this->registerSMS($adminPhonoe, 'Home bank is empty!');
			}
		}

		if($bNeedSMS)
			return $this->reply(201, 'ok', null);

		return $this->reply(200, 'ok', null);
	}

	public function registerJobHomeSaleViaCredit()
	{
		$token = $this->input->post('token');
		if($token!=$this->token)
			return $this->reply(400, 'invalid token, params=> token', null);

		$customs = $this->input->post('customs');
		if($customs=="")
			return $this->reply(400, 'invalid customs, params=> customs', null);

		$details = $this->input->post('details');
		if($details=="")
			return $this->reply(400, 'invalid details, params=> details', null);

		$name = $this->input->post('name');
		if($name=="")		
			return $this->reply(400, 'invalid name, params=> name', null);

		$email = $this->input->post('email');
		if($email=="")
			return $this->reply(400, 'invalid email, params=> email', null);

		$phone = $this->input->post('phone');
		$tracking = $this->input->post('tracking');
			
		$totalCharged = 0;
		//log //$bills
		$sales = explode(',',$customs);
		foreach($sales as $sale)
		{
			$datas = explode('-',$sale);
			if(count($datas) != 7) continue;

			$machineId = intval($datas[0]);
			if($machineId ==0) continue;

			$machine = $this->Machine_model->getRow(array('machine_id'=>$machineId));
			if($machine == null)continue;
			if($machine->current_mode!=0)continue;
			$job = $this->Job_model->getRow(array('machine_id'=>$machine->Id, 'status'=>'ready'));
			if($job == null ) 
				$job = $this->Job_model->getRow(array('machine_id'=>$machine->Id, 'status'=>'busy'));
			if($job != null)continue;

			$programId = intval($datas[1]);
			if($programId ==0) continue;

			$options = intval($datas[2]);
			$price = floatval($datas[3]);
			$charge = floatval($datas[4]);
	
			$totalCharged += $charge;
			//update job table
			$data = array('user_id'=>1, 'machine_id'=>$machine->Id, 
				'program_id'=>$programId, 'options'=>$options,
				'price'=>$charge, 'need_sms'=>0, 'dt'=>date("Y-m-d H:i:s"));
			$this->Job_model->insertData($data);

			if($datas[6]!="True")
			{
				//register transaction		
				unset($data['need_sms']);
				$data['reason'] = 'withdraw';
				$data['org_price'] = $price;
				$data['balance'] = 0;
				$data['method'] = 'CardReader';
				$this->Transaction_model->insertData($data);
			}
		}

		//if traking mode 
		if($tracking=="1" && $phone!="")
		{
			$tracks = $this->input->post('tracks');
			$usedstates = explode(',',$tracks);
			foreach($usedstates as $usedstate)
			{
				$datas = explode('-',$usedstate);
				if(count($datas) != 2) continue;

				$cond = array('phone'=>$phone, 'model_id'=>$datas[0]);
				$row = $this->Track_model->getRow($cond);
				if($row!=null)
					$this->Track_model->updateData($cond, array('used'=>$datas[1]));
				else
				{
					$cond['used'] = $datas[1];
					$this->Track_model->insertData($cond);
				}
			}			
		}		

		$this->CreditIncome_model->insertData(array('user_id'=>1, 'name'=>$name, 'email'=>$email,
			'method'=>'CardReader', 'price'=>$totalCharged, 'dt'=>date("Y-m-d H:i:s"), 'details'=>$details));

		return $this->reply(200, "successful booked", null);
	}

	public function registerJobHomeSale()
	{
		$token = $this->input->post('token');
		if($token!=$this->token)
			return $this->reply(400, 'invalid token, params=> token', null);

		$customs = $this->input->post('customs');
		if($customs=="")
			return $this->reply(400, 'invalid customs, params=> customs', null);

		$bills = $this->input->post('bills');
		if($bills=="")
			 return $this->reply(400, 'invalid bills, params=> bills', null);
		if($bills=="none") $bills= "";

		$payouts = $this->input->post('payouts');
	
		$phone = $this->input->post('phone');
		$tracking = $this->input->post('tracking');
	
		//log //$bills
		$sales = explode(',',$customs);
		foreach($sales as $sale)
		{
			$datas = explode('-',$sale);
			if(count($datas) != 7) continue;

			$machineId = intval($datas[0]);
			if($machineId ==0) continue;

			$machine = $this->Machine_model->getRow(array('machine_id'=>$machineId));
			if($machine == null)continue;
			if($machine->current_mode!=0)continue;
			$job = $this->Job_model->getRow(array('machine_id'=>$machine->Id, 'status'=>'ready'));
			if($job == null ) 
				$job = $this->Job_model->getRow(array('machine_id'=>$machine->Id, 'status'=>'busy'));
			if($job != null)continue;


			$programId = intval($datas[1]);
			if($programId ==0) continue;

			$options = intval($datas[2]);
			$price = floatval($datas[3]);
			$charge = floatval($datas[4]);
	
			//update job table
			$data = array('user_id'=>1, 'machine_id'=>$machine->Id, 
				'program_id'=>$programId, 'options'=>$options,
				'price'=>$charge, 'need_sms'=>0, 'dt'=>date("Y-m-d H:i:s"));
			$this->Job_model->insertData($data);
						
			//register transaction
			if($datas[6]!="True") // free none job
			{
				unset($data['need_sms']);
				$data['reason'] = 'withdraw';
				$data['org_price'] = $price;
				$data['balance'] = 0;
				$data['method'] = 'Acceptor';
				$this->Transaction_model->insertData($data);
			}
		}

		//log //$bills
		$billEntries = explode(',',$bills);
		foreach($billEntries as $billEntry)
		{
			$entry = explode('-',$billEntry);
			if(count($entry) != 2)continue;
			$this->Kiosk2Income_model->insertData(array('type'=>'bill', 'price'=>$entry[0],'cnt'=>$entry[1], 'dt'=>date("Y-m-d H:i:s")));
		}

		$billEntries = explode(',',$payouts);
		foreach($billEntries as $billEntry)
		{
			$entry = explode('-',$billEntry);
			if(count($entry) != 2)continue;

			$homeBkEntry = $this->HomeBank_model->getRow(array('coin'=>$entry[0]));

			$hopperCoins = 0;
			if($homeBkEntry!=null && $homeBkEntry->count > $entry[1])
				$hopperCoins = $homeBkEntry->count - $entry[1];

			$this->Kiosk2Income_model->insertData(array('type'=>'payout', 'price'=>$entry[0],
										'cnt'=>$entry[1], 'dt'=>date("Y-m-d H:i:s"), 'hopper_coins'=>$hopperCoins));
		}

		//if traking mode 
		if($tracking=="1" && $phone!="")
		{
			$tracks = $this->input->post('tracks');
			$usedstates = explode(',',$tracks);
			foreach($usedstates as $usedstate)
			{
				$datas = explode('-',$usedstate);
				if(count($datas) != 2) continue;

				$cond = array('phone'=>$phone, 'model_id'=>$datas[0]);
				$row = $this->Track_model->getRow($cond);
				if($row!=null)
					$this->Track_model->updateData($cond,array('used'=>$datas[1]));
				else
				{
					$cond['used'] = $datas[1];
					$this->Track_model->insertData($cond);
				}
			}		
	
		}

		return $this->reply(200, "successful booked", null);
	}

	public function registerJobForDryer()
	{
		$token = $this->input->post('token');
		if($token!=$this->token)
			return $this->reply(400, 'invalid token, params=> token', null);

		$customs = $this->input->post('customs');
		if($customs=="")
			return $this->reply(400, 'invalid customs, params=> customs', null);

		$bills = $this->input->post('bills');
		$payouts = $this->input->post('payouts');				
	
		//log //$bills
		$sales = explode(',',$customs);
		foreach($sales as $sale)
		{
			$datas = explode('-',$sale);
			if(count($datas) != 5) continue;

			$machineId = intval($datas[0]);
			if($machineId ==0) continue;

			$machine = $this->Machine_model->getRow(array('machine_id'=>$machineId));
			if($machine == null)continue;
			if($machine->current_mode!=0)continue;
			$job = $this->Job_model->getRow(array('machine_id'=>$machine->Id, 'status'=>'ready'));
			if($job == null ) 
				$job = $this->Job_model->getRow(array('machine_id'=>$machine->Id, 'status'=>'busy'));
			if($job != null)continue;


			$programId = intval($datas[1]);
			if($programId ==0) continue;

			$options = intval($datas[2]);
			$price = floatval($datas[3]);
			$charge = floatval($datas[4]);
	
			//update job table
			$data = array('user_id'=>1, 'machine_id'=>$machine->Id, 
				'program_id'=>$programId, 'options'=>$options,
				'price'=>$charge, 'need_sms'=>0, 'dt'=>date("Y-m-d H:i:s"));
			$this->Job_model->insertData($data);

			if($charge ==0) 
				continue;
				
			//register transaction		
			unset($data['need_sms']);
			$data['reason'] = 'withdraw';
			$data['org_price'] = $price;
			$data['method'] = 'Acceptor';
			$data['balance'] = 0;
			$this->Transaction_model->insertData($data);			
		}

		//log //$bills
		$billEntries = explode(',',$bills);
		foreach($billEntries as $billEntry)
		{
			$entry = explode('-',$billEntry);
			if(count($entry) != 2)continue;
			$this->Kiosk2Income_model->insertData(array('type'=>'bill', 'price'=>$entry[0],'cnt'=>$entry[1], 'dt'=>date("Y-m-d H:i:s")));
		}

		$billEntries = explode(',',$payouts);
		foreach($billEntries as $billEntry)
		{
			$entry = explode('-',$billEntry);
			if(count($entry) != 2)continue;
			$this->Kiosk2Income_model->insertData(array('type'=>'payout', 'price'=>$entry[0],'cnt'=>$entry[1], 'dt'=>date("Y-m-d H:i:s")));
		}

		return $this->reply(200, "successful booked", null);
	}

	public function registerWashFold()
	{
		$token = $this->input->post('token');
		if($token!=$this->token)
			return $this->reply(400, 'invalid token, params=> token', null);

		$bills = $this->input->post('bills');
		$payouts = $this->input->post('payouts');

		$weight = $this->input->post('weight');		
		$soap = $this->input->post('soap');		
		$price_lbs = $this->input->post('price_lbs');
		$total_price = $this->input->post('total_price');
		if($weight ==0 || $total_price==0)
			return $this->reply(400, 'invalid param, weight, total_price', null);
		$payment = $this->input->post('payment');
	
		$pay = "Cash";
		//log //$bills
		if($payment =="cash")
		{
			$billEntries = explode(',',$bills);
			foreach($billEntries as $billEntry)
			{
				$entry = explode('-',$billEntry);
				if(count($entry) != 2)continue;
				$this->Kiosk2Income_model->insertData(array('type'=>'bill', 'price'=>$entry[0],'cnt'=>$entry[1], 'dt'=>date("Y-m-d H:i:s")));
			}
	
			$billEntries = explode(',',$payouts);
			foreach($billEntries as $billEntry)
			{
				$entry = explode('-',$billEntry);
				if(count($entry) != 2)continue;
				$this->Kiosk2Income_model->insertData(array('type'=>'payout', 'price'=>$entry[0],'cnt'=>$entry[1], 'dt'=>date("Y-m-d H:i:s")));
			}
		}
		else
			$pay = "Credit Card";

		$this->WashFold_model->insertData(array('weight'=>$weight, 'dt'=>date("Y-m-d H:i:s"), 
			'soap'=>$soap, 'price_lbs'=>$price_lbs, 'total_price'=>$total_price, 'payment'=>$pay));
		return $this->reply(200, "successful", null);
	}

	public function kiosk_check_track()
	{
		$token = $this->input->post('token');
		if($token!=$this->token)
			return $this->reply(400, 'invalid token, params=> token', null);
		
		$phone = $this->input->post('phone');
		if($phone=="")
			return $this->reply(400, 'invalid phone, params=> phone', null);
		if (substr($phone, 0, 1) != '1')$phone = '1'.$phone;

		$tracks = $this->Track_model->getDatas(array('phone'=>$phone));
		if(count($tracks)==0)
			return $this->reply(401, 'not registered', null);

		$used = array();
		foreach($tracks as $track)	
		{
			$used[]=array('model_id'=>$track->model_id, 'used'=>$track->used);
		}
		return $this->reply(200, "ok", array('phone'=>$phone,'used'=>$used));
	}


	public function registerSupplySale()
	{
		$token = $this->input->post('token');
		if($token!=$this->token)
			return $this->reply(400, 'invalid token, params=> token', null);

		$customs = $this->input->post('customs');
		if($customs=="")
			return $this->reply(400, 'invalid customs, params=> customs', null);

		$bills = $this->input->post('bills');
		if($bills=="")
			 return $this->reply(400, 'invalid bills, params=> bills', null);
		if($bills=="none") $bills= "";

		$payouts = $this->input->post('payouts');
	
		//log //$bills
		$sales = explode(',',$customs);
		foreach($sales as $sale)
		{
			$datas = explode('-',$sale);
			if(count($datas) != 3) continue;

			$supplyId = intval($datas[0]);
			$count = intval($datas[2]);
			if($supplyId ==0 || $count <= 0) continue;

			$supply = $this->Supply_model->getRow(array('Id'=>$supplyId));
			if($supply == null)continue;

			//check count
			if($supply->qty < $count)continue;

			//update supply transaction table
			$this->Base_model->insertData('tbl_supply_transaction', 
				['dt'=>date('Y-m-d H:i:s'), 
				'supply_id'=> $supply->Id, 
				'price' => $supply->price,  
				'count' => $count,
				'action' => 'sold',
				'org_qty'=> $supply->qty,
				'new_qty' => $supply->qty - $count,
				'type' => 'cash',
				]);

			//update supply qty
			$this->Supply_model->updateData(['Id'=>$supply->Id], ['qty'=>$supply->qty - $count]);
		}

		//log //$bills
		$billEntries = explode(',',$bills);
		foreach($billEntries as $billEntry)
		{
			$entry = explode('-',$billEntry);
			if(count($entry) != 2)continue;
			$this->Kiosk2Income_model->insertData(array('type'=>'bill', 'price'=>$entry[0],'cnt'=>$entry[1], 'dt'=>date("Y-m-d H:i:s")));
		}

		$billEntries = explode(',',$payouts);
		foreach($billEntries as $billEntry)
		{
			$entry = explode('-',$billEntry);
			if(count($entry) != 2)continue;

			$homeBkEntry = $this->HomeBank_model->getRow(array('coin'=>$entry[0]));

			$hopperCoins = 0;
			if($homeBkEntry!=null && $homeBkEntry->count > $entry[1])
				$hopperCoins = $homeBkEntry->count - $entry[1];

			$this->Kiosk2Income_model->insertData(array('type'=>'payout', 'price'=>$entry[0],
										'cnt'=>$entry[1], 'dt'=>date("Y-m-d H:i:s"), 'hopper_coins'=>$hopperCoins));
		}
		return $this->reply(200, "successful purchase", null);
	}

	public function registerSupplySaleViaCredit()
	{
		$token = $this->input->post('token');
		if($token!=$this->token)
			return $this->reply(400, 'invalid token, params=> token', null);

		$customs = $this->input->post('customs');
		if($customs=="")
			return $this->reply(400, 'invalid customs, params=> customs', null);

		$details = $this->input->post('details');
		if($details=="")
			return $this->reply(400, 'invalid details, params=> details', null);

		$name = $this->input->post('name');
		if($name=="")		
			return $this->reply(400, 'invalid name, params=> name', null);

		$email = $this->input->post('email');
		if($email=="")
			return $this->reply(400, 'invalid email, params=> email', null);

		$totalCharged = 0;
		//log //$bills
		$sales = explode(',',$customs);
		foreach($sales as $sale)
		{
			$datas = explode('-',$sale);
			if(count($datas) != 3) continue;

			$supplyId = intval($datas[0]);
			$count = intval($datas[2]);
			if($supplyId ==0 || $count <= 0) continue;

			$supply = $this->Supply_model->getRow(array('Id'=>$supplyId));
			if($supply == null)continue;

			//check count
			if($supply->qty < $count)continue;

			//update supply transaction table
			$this->Base_model->insertData('tbl_supply_transaction', 
				['dt'=>date('Y-m-d H:i:s'), 
				'supply_id'=> $supply->Id, 
				'price' => $supply->price,  
				'count' => $count,
				'action' => 'sold',
				'org_qty'=> $supply->qty,
				'new_qty' => $supply->qty - $count,
				'type' => 'credit',
				]);
			//update supply qty
			$this->Supply_model->updateData(['Id'=>$supply->Id], ['qty'=>$supply->qty - $count]);
			$totalCharged += ($supply->price * $count);
		}		
		$this->CreditIncome_model->insertData(array('user_id'=>1, 'name'=>$name, 'email'=>$email,
			'method'=>'CardReader', 'price'=>$totalCharged, 'dt'=>date("Y-m-d H:i:s"), 'details'=>$details));
		return $this->reply(200, "successful purchase", null);
	}		

}