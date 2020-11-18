<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Define_model extends CI_Model {

	public function __construct()
	{
		parent::__construct();
	}

	public function getModeString($mode)
	{
		if($mode >=9) return "";
		switch($mode)
		{
			case 0:
				return "Idle";
			break;
			case 1:
				return "Process";
			break;
			case 0:
				return "Remote service";
			break;
			case 0:
				return "Manual service";
			break;
			case 0:
				return "Ext-flash";
			break;
			case 0:
				return "Boot";
			break;
			case 0:
				return "IdCPU wrting";
			break;
			case 0:
				return "LPM";
			break;
			case 0:
				return "Initialisation";
			break;
		}
		return "Idle";
	}

	public function getStatus1String($flag)
	{
		if (($flag & 0x01) != 0)
			return "Key lock for free start activated";
		else if (($flag & 0x02) != 0)
			return "New program selection (Start request)";
		else if (($flag & 0x04) != 0)
			return "Program selected";
		else if (($flag & 0x08) != 0)
			return "Machine occupied";
		else if (($flag & 0x10) != 0)
			return "Ongoing program";
		else if (($flag & 0x20) != 0)
			return "Service mode active";
		else if (($flag & 0x40) != 0)
			return "Machine in error mode";
		else if (($flag & 0x80) != 0)
			return "Batch ID manually feeded";

		else if (($flag & 0x0100) != 0)
			return "Regret time, for regret menu, ended";
		else if (($flag & 0x0200) != 0)
			return "Regret time ended";
		else if (($flag & 0x0400) != 0)
			return "Pause disabled";
		else if (($flag & 0x0800) != 0)
			return "Rapid advance disabled";
		else if (($flag & 0x1000) != 0)
			return "Program Change disabled";
		else if (($flag & 0x2000) != 0)
			return "Abort Program disabled";

		return "";
	}

	public function getBaudRate($rate)
	{
		switch($rate)
		{
			case 0:
				return "The best baud rate";
				break;
			case 5:
				return "2400 bps";
				break;
			case 7:
				return "9600 bps";
				break;
			case 10:
				return "38400 bps";
				break;
			case 13:
				return "115200 bps";
				break;
			case 15:
				return "15=> 230400 bps";
				break;
		}
		return "unknown";
	}

	public function getMachineType($type)
	{
		if($type==0)
			return "washer";
		return "dryer";
	}

	public function getOptionString($opt)
	{
		$strRet = "";
		if(($opt & 1 )!=0)
			$strRet = "Extra";
		if(($opt & 2 )!=0)
		{
			if($strRet!="")$strRet .="|";
			$strRet .= "Heavy";
		}
		return $strRet;
	}


	public function getDaysOfWeek()
	{
		$result = array();
		$result[] = array('Id'=>1, 'name'=>'Sunday');
		$result[] = array('Id'=>2, 'name'=>'Monday');
		$result[] = array('Id'=>3, 'name'=>'Tuesday');
		$result[] = array('Id'=>4, 'name'=>'Wednesday');
		$result[] = array('Id'=>5, 'name'=>'Thursday');
		$result[] = array('Id'=>6, 'name'=>'Friday');
		$result[] = array('Id'=>7, 'name'=>'Saturday');
		return $result;
	}

	public function getDayOfWeek($day)
	{
		switch($day)
		{
			case 1:
				return 'Sunday';
			break;
			case 2:
				return 'Monday';
			break;
			case 3:
				return 'Tuesday';
			break;
			case 4:
				return 'Wednesday';
			break;
			case 5:
				return 'Thursday';
			break;
			case 6:
				return 'Friday';
			break;
			case 7:
				return 'Saturday';
			break;
		}
		return "";
	}

}


