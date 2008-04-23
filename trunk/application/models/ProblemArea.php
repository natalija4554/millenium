<?php
/**
 * Table ProblemArea
 *
 */
class ProblemArea extends Colla_Db_Table_Abstract
{
	/**
	 * Table name
	 *
	 * @var string
	 */
    protected $_name = 'problemareas';
    protected $_rowClass = 'Row_ProblemArea';
    protected $_dependentTables = array('ProblemAreaChange');

    /**
	 * Save problematic area into database
	 * 
	 * @param array $values data to save
	 * @todo save CreatedBy
	 */
	public function saveNew($data)
	{
		// extra fields
		$data['Created'] = new Zend_Db_Expr('NOW()');
		
		// Begin transaction
		$this->getAdapter()->beginTransaction();
		
		// save problem area
		$problemData = $this->_filterArray($data, array('Name', 'Definition', 'Created'));
		$problemAreaId = $this->insert($problemData);
		
		// save problem area change
		$data['ChangedBy'] = $data['CreatedBy'];
		$data['ProblemAreaId'] = $problemAreaId;
		$changeData = $this->_filterArray($data, array('Name', 'Definition', 'ChangedBy', 'ProblemAreaId', 'Created'));
		$changeTable = new ProblemAreaChange();
		$changeTable->insert($changeData);
		
		// commit transaction
		$this->getAdapter()->commit();	
	}
	
	/**
	 * Change the definition of problem area
	 *
	 * @param int $problemAreaId
	 * @param array $data
	 */
	public function changeDefinition($problemAreaId, $data)
	{
		// check data for presend of keys
		$data = $this->_filterArray($data, array('Name', 'Definition'));
		
		// Begin transaction
		$this->getAdapter()->beginTransaction();
		
		// Update this row
		$problemArea = $this->getProblemArea($problemAreaId);
		$problemArea->Name = $data['Name'];
		$problemArea->Definition = $data['Definition'];
		$problemArea->save();
		
		// create change set
		$problemChangeTable = new ProblemAreaChange();
		$changeRow = $problemChangeTable->createRow();
		$changeRow->Name 			= $data['Name'];
		$changeRow->Definition 		= $data['Definition'];
		$changeRow->ProblemAreaId 	= $problemAreaId;
		$changeRow->Created			= new Zend_Db_Expr('NOW()');
		$changeRow->ChangedBy		= $this->_getLogedUserId(); 		
		$changeRow->save();
		
		// commit
		$this->getAdapter()->commit();		
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
	 * @return Row_ProblemArea
	 */
	public function getProblemArea($id)
	{
		$rowset = $this->find((int)$id);
		if (count($rowset) != 1) {
			throw new Exception('No such problem area');
		}
		return $rowset->current();
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
		$Problems = new Problem();
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
