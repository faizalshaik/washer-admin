<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Log_model extends CI_Model {

	public function __construct()
	{
		parent::__construct();
		$this->tblName = 'tbl_log';
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

	public function deleteRow( $conArry ) 
	{
		if(!empty($conArry)) {
			$this->db->where($conArry);
			$this->db->delete($this->tblName);
		}
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
	
	public function getLogs($Id, $fromDate, $toDate)
	{
		$this->db->from($this->tblName);
		$where = "Id=".Id." ";
		if($fromDate=='')
			$fromDate = date('Y-m-d');
		$where .= "and dt >=".$fromDate." ";
		
		if($toDate !='')
			$where .= "and dt <=".$toDate." ";

		$this->db->where($where);
		$ret = $this->db->get()->result();
		return $ret;
	}
}


