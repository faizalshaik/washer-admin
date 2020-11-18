<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Setting_model extends CI_Model {

	public function __construct()
	{
		parent::__construct();
		$this->tblName = 'tbl_setting';
	}

	public function getDatas($conAry, $orderBy='') 
	{
		$this->db->from($this->tblName);

		if(!empty($conAry))
			$this->db->where( $conAry );
		if($orderBy !='') {
			$this->db->order_by($orderBy, 'ASC');
		}    	
		$ret = $this->db->get()->result();
		return $ret;
	}

	public function updateData($conAry, $updateAry) 
	{
		if(!empty($updateAry)) {
			$this->db->update($this->tblName, $updateAry, $conAry);
		}
		return $this->db->affected_rows();
	}

	public function deleteRow( $conArry ) 
	{
		if(!empty($conArry)) {
			$this->db->where($conArry);
			$this->db->delete($this->tblName);
		}
	}	

	public function deleteByField($field, $value ) {
		$this->db->where($field, $value);
        $this->db->delete($this->tblName);
	}

	public function getCounts($conAry) {
    	$this->db->from($this->tblName);
		if(!empty($conAry))
			$this->db->where( $conAry );
		return $this->db->count_all_results();
    }

    public function insertData($data)
    {
        $this->db->insert($this->tblName, $data);
        return $this->db->insert_id();
    }

	public function getRow($conAry) 
	{
    	$this->db->from($this->tblName);
    	$this->db->where($conAry);
        $query = $this->db->get();
        return $query->row();
    }

    public function setField($field, $value, $conAry, $valueString=FALSE) {
    	$this->db->from($this->tblName);
		$this->db->set($field, $value, $valueString);
		$this->db->where($conAry);
		$this->db->update();
    }
    public function getDataById($Id)
    {
        $this->db->from($this->tblName);
        $this->db->where('Id',$Id);
        $query = $this->db->get();
        return $query->row();
	}

    public function getKioskIncomeClearTime()
    {
		$row = $this->getRow(array('keyword'=>'kiosk_income_clear_time'));
		if($row==null)
			return null;
		return $row->value;
	}
    public function setKioskIncomeClearTime($clearTime)
    {
		$this->setField('value', $clearTime, array('keyword'=>'kiosk_income_clear_time'), TRUE);
	}

    public function getWasherTransactionClearTime()
    {
		$row = $this->getRow(array('keyword'=>'washer_transaction_clear_time'));
		if($row==null)
			return null;
		return $row->value;
	}
    public function setWasherTransactionClearTime($clearTime)
    {
		$this->setField('value', $clearTime, array('keyword'=>'washer_transaction_clear_time'), TRUE);
	}

	

	public function getHomeSaleClearTime()
	{
		$row = $this->getRow(array('keyword'=>'kiosk_home_sale_clear_time'));
		if($row==null)
			return null;
		return $row->value;
	}
	
	public function setHomeSaleClearTime($clearTime)
	{
		$this->setField('value', $clearTime, array('keyword'=>'kiosk_home_sale_clear_time'), TRUE);
	}

	public function getCreditSaleClearTime()
	{
		$row = $this->getRow(array('keyword'=>'credit_sale_clear_time'));
		if($row==null)
			return null;
		return $row->value;
	}
	
	public function setCreditSaleClearTime($clearTime)
	{
		$this->setField('value', $clearTime, array('keyword'=>'credit_sale_clear_time'), TRUE);
	}

	public function getAdminPhoneNumber()
	{
		$row = $this->getRow(array('keyword'=>'admin_phone'));
		if($row==null)
			return null;
		return $row->value;
	}

	public function setAdminPhoneNumber($phone)
	{
		$this->setField('value', $phone, array('keyword'=>'admin_phone'), TRUE);
	}

	public function getHopperFlag()
	{
		$row = $this->getRow(array('keyword'=>'hopper_set_count_flag'));
		if($row==null)
			return null;
		return $row->value;
	}

	public function setHopperFlag($flag)
	{
		$this->setField('value', $flag, array('keyword'=>'hopper_set_count_flag'), TRUE);
	}

	public function setWashFoldClearTime($clearTime)
	{
		$this->setField('value', $clearTime, array('keyword'=>'wash_fold_time'), TRUE);
	}

	public function getWashFoldClearTime()
	{
		$row = $this->getRow(array('keyword'=>'wash_fold_time'));
		if($row==null)
			return null;
		return $row->value;
	}

	public function setPointsPerUsd($point)
	{
		$this->setField('value', $point, array('keyword'=>'track_points_per_usd'), TRUE);
	}

	public function getPointsPerUsd()
	{
		$row = $this->getRow(array('keyword'=>'track_points_per_usd'));
		if($row==null)
			return null;
		return $row->value;
	}


	public function setMaxRedeem($point)
	{
		$this->setField('value', $point, array('keyword'=>'redeem_points'), TRUE);
	}

	public function getMaxRedeem()
	{
		$row = $this->getRow(array('keyword'=>'redeem_points'));
		if($row==null)
			return null;
		return $row->value;
	}


	public function setSupplyTrxClearTime($clearTime)
	{
		$this->setField('value', $clearTime, array('keyword'=>'supply_trx_time'), TRUE);
	}

	public function getSupplyTrxClearTime()
	{
		$row = $this->getRow(array('keyword'=>'supply_trx_time'));
		if($row==null)
			return null;
		return $row->value;
	}	

}


