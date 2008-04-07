<?php
class Colla_Db_Table_Problem extends Colla_Db_Table_Abstract
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
	public function createNew($data)
	{
		// extra fields to save
		$data['Created'] 	= new Zend_Db_Expr('NOW()');
		$data['Modified'] 	= new Zend_Db_Expr('NOW()');
		$data['UserId'] 		= $this->_getLogedUserId();
		$data['ApproveUserId']	= $this->_getLogedUserId();
		
		// choose witch one to save
		$problemData = $this->_filterArray($data, array(
			'Name',
			'FullName',
			'Definition',
			'ProblemAreaId',
			'CategoryId',
			'CreatedBy',
			'Created',
			'Modified'
		));	
		
		// change data
		$changeData = $this->_filterArray($data, array(
			'Name',
			'FullName',
			'Definition',
			'Created',
			'UserId',
			'ApproveUserId'
		));		
		
		// begin transaction
		$this->getAdapter()->beginTransaction();
		
		// Save problem
		$problemId = $this->insert($problemData);
		
		// Save problem change
		$changeTable = new Colla_Db_Table_ProblemChange();
		$changeData['ProblemId'] = $problemId;
		$changeTable->insert($changeData);
		
		// commit transaction
		$this->getAdapter()->commit();
	}
	
	/**
	 * Change the definition of a problem
	 *
	 * @param int $problemId
	 * @param array $data
	 */
	public function changeDefinition($problemId, $data)
	{
		
	}
}
