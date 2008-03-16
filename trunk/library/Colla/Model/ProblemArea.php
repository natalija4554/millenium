<?php

/**
 * Model pre colla
 *
 */
class Colla_Model_ProblemArea extends Colla_Model_Abstract
{
	/**
	 * Protected
	 *
	 * @var Colla_Db_Table_ProblemArea
	 */
	protected $_table;
	
	
	/**
	 * Initialize db connection with problem area table
	 */
	public function __construct()
	{
		$this->_table = new Colla_Db_Table_ProblemArea();
	}
	
	/**
	 * Save problematic area into database
	 * 
	 * @param array $values data to save
	 * @todo save CreatedBy
	 */
	public function saveNew($values)
	{
		// required fields
		$required_fields = array(
			'name',
			'description'
		);
		
		// filter and check input array
		$data = array();
		foreach ($required_fields as $key) {
			if (!array_key_exists($key, $values)) {
				throw new Exception('Value '.$key.' not found!');
			}
			$data[$key] = $values[$key];
		}
		
		// extra fields to save
		$data['Created'] = new Zend_Db_Expr('NOW()');
		
		// save data
		$this->_table->insert($data);
	}
	
	/**
	 * Vrati zoznam problemovych oblasti
	 *
	 * @return array
	 */
	public function getProblemAreas()
	{ 
		$problemArea = new Colla_Db_Table_ProblemArea();
    	return $problemArea->fetchAll()->toArray();
	}
	
	/**
	 * Get problem are info
	 *
	 * @param int $id
	 * @return Zend_Db_Table_Row
	 */
	public function getProblemArea($id)
	{
		$pa = new Colla_Db_Table_ProblemArea();
		$rowset = $pa->find((int)$id);
		if (count($rowset) != 1) {
			throw new Exception('No such problem area');
		}
		return $rowset->current()->toArray();
	}
	
	/**
	 * Find out if problem are exists
	 *
	 * @param int $id
	 * @return bool
	 */
	public function hasProblemArea($id)
	{
		$pa = new Colla_Db_Table_ProblemArea();
		$rowset = $pa->find((int)$id);
		if (count($rowset) == 0) {
			return false;
		}		
		if (count($rowset) == 1) {
			return true;
		}
		throw new Exception('Error in retrieving rowset');
	}
	
	public function getProblems($ProblemAreaId)
	{
		$pt = new Colla_Db_Table_Problem();
		$where = $pt->select()->where('ProblemAreaId = ?', (int) $ProblemAreaId);
		$rowset = $pt->fetchAll($where);
		return $rowset->toArray();
	}
}
?>