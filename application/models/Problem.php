<?php
class Problem extends Colla_Db_Table_Abstract
{
    protected $_name = 'problems';
    protected $_rowClass = 'Row_Problem';
    
    /**
     * States of a problem
     */
    const STATE_NEW 		= 'NEW';
    const STATE_APPROVED 	= 'APPROVED';
    const STATE_SOLVED 		= 'SOLVED';
    const STATE_CLOSED 		= 'CLOSED';
    const STATE_DELETED 	= 'DELETED';
    
    
    const EVENT_CREATE		= 'CREATE';
    const EVENT_APPROVE		= 'APPROVE';
    const EVENT_DECLINE		= 'DECLINE';
    
    /**
     * Enter description here...
     *
     * @param ind $Id
     * @return Row_Problem
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
	 */
	public function createNew($data)
	{
		// extra fields to save
		$data['Created'] 	= new Zend_Db_Expr('NOW()');
		$data['Modified'] 	= new Zend_Db_Expr('NOW()');
		
		// save them
		$this->getAdapter()->beginTransaction();		
		$data['ProblemId'] = $this->insert($this->_filterArray($data, array(
			'Name',
			'Definition',
			'ProblemAreaId',
			'CreatedBy',
			'Note',
			'Created',
			'CreatedBy',
			'Modified'
		)));
		$data['Event'] = 'CREATE';
		$hTable = new ProblemHistory();
		$hTable->insert($this->_filterArray($data, array(
			'ProblemId',
			'Event',
			'Note',
			'Created',
			'CreatedBy'
		)));		
		$this->getAdapter()->commit();
	}
}
