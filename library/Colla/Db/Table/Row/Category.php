<?php

class Colla_Db_Table_Row_Category extends Zend_Db_Table_Row_Abstract 
{
	
	/**
	 * Get childs of a actual element
	 *
	 * @return Colla_Db_Table_Rowset_Category
	 */
	public function getChilds()
	{
		// check if ParentId is set
		if (!$this->Id) {
			throw new Exception('Id Missing');
		}
		
		// find all childs
		return $this->getTable()->fetchAll($this->select()->where('ParentId = ?', $this->Id));
	}
	
	/**
	 * Remove recursively
	 */
	public function deleteRecursively()
	{
		foreach ($this->getChilds() as $child) {
			$child->deleteRecursively();
			$child->delete();
		}
		$this->delete();
	}
	
	
}

?>