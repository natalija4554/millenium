<?php

class Colla_Db_Table_Row_ProblemArea extends Zend_Db_Table_Row_Abstract 
{
	/**
	 * Get changes of current object
	 *
	 */
	public function getChanges()
	{
		// initialize change table
		$changeTable = new Colla_Db_Table_ProblemAreaChange();
		
		// fetch informations
		$select = $changeTable->select();
		$select->where('ProblemAreaId = ?', $this->Id);
		$select->order('Created DESC');
		$rows = $changeTable->fetchAll($select);
		
		return $rows;
	}
	
}

?>