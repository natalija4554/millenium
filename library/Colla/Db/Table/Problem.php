<?php
class Colla_Db_Table_Problem extends Zend_Db_Table_Abstract
{
    protected $_name = 'problems';
    protected $_rowClass = 'Colla_Db_Table_Row_Problem';
    
    /**
     * Enter description here...
     *
     * @param ind $Id
     * @return Colla_Db_Table_Row_Problem
     */
    public function findProblem($Id)
    {
    	$rowset = $this->find((int)$Id);
		if (count($rowset) != 1) {
			throw new Exception('No such problem');
		}
		return $rowset->current();
    }
    
    
	/**
	 * Save problematic area into database
	 * 
	 * @param array $values data to save
	 * @todo save CreatedBy
	 */
	public function createNew($values)
	{
		// required fields
		$required_fields = array(
			'Name',
			'FullName',
			'Definition',
			'ProblemAreaId',
			'CategoryId',
			'CreatedBy'
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
		$data['Modified'] = new Zend_Db_Expr('NOW()');
		
		// save data
		$this->insert($data);
	}
}
