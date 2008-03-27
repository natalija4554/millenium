<?php
/**
 * Table ProblemArea
 *
 */
class Colla_Db_Table_ProblemArea extends Zend_Db_Table_Abstract
{
	/**
	 * Table name
	 *
	 * @var string
	 */
    protected $_name = 'ProblemAreas';
    
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
		$this->insert($data);
	}
	
	/**
	 * Vrati zoznam problemovych oblasti
	 *
	 * @return array
	 */
	public function getProblemAreas()
	{ 
		return $this->fetchAll()->toArray();
	}
	
	/**
	 * Get problem are info
	 *
	 * @param int $id
	 * @return array
	 */
	public function getProblemArea($id)
	{
		$rowset = $this->find((int)$id);
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
		$rowset = $this->find((int)$id);
		if (count($rowset) == 0) {
			return false;
		}		
		if (count($rowset) == 1) {
			return true;
		}
		throw new Exception('Error in retrieving rowset');
	}
	
	/**
	 * Get a list off all problems
	 *
	 * @param int $ProblemAreaId
	 * @return array
	 */
	public function getProblems($ProblemAreaId)
	{
		$Problems = new Colla_Db_Table_Problem();
		$where = $Problems->select()->where('ProblemAreaId = ?', (int) $ProblemAreaId);
		$rowset = $Problems->fetchAll($where);
		return $rowset->toArray();
	}
	
	/**
	 * Get default problem area 
	 * - the one set as Default
	 * - if only 1 is present, return this one
	 * 
	 * @return int | false 
	 */
	public function getDefaultProblemArea()
	{
		$where = $this->select()->from($this->_name, array('Id', 'Default'));
		$rows = $this->fetchAll($where);
		
		if (count($rows) == 1) {
			return $rows->current()->Id;
		}
		foreach ($rows as $row) {
			if ($row->Default == '1') {
				return $row->Id;			}
		}
		return false;
	}
    
}
