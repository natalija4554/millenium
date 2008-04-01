<?php
class Colla_Db_Table_Category extends Zend_Db_Table_Abstract
{
    protected $_name = 'categories';
    protected $_rowClass = 'Colla_Db_Table_Row_Category';
    protected $_rowsetClass = 'Colla_Db_Table_Rowset_Category';

    /**
     * Get category row
     * 
     * @return Colla_Db_Table_Row_Category
     */
    public function findCategory($categoryId)
    {
    	$rows = $this->find((int) $categoryId);
    	if (count($rows) != 1) {
    		throw new Exception('Not found!');
    	}
    	return $rows->current();
    }
    
    /**
     * Get a parent rows (with ParentId set to null)
     *
     * @return Colla_Db_Table_Rowset_Category
     */
    public function getParentRows()
    {
    	$where = $this->select()->where('ParentId IS NULL');
    	return $this->fetchAll($where);
    }
    
    /**
     * Save new Category
     *
     * @param string[] $values
     */
    public function newCategory($values)
    {
    	// required fields
		$required_fields = array(
			'Name'
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
    
    public function newSubCategory($values)
    {
    	// required fields
		$required_fields = array(
			'Name',
			'ParentId'
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
    
    /**
     * Return array of nested lists
     *
     * @return array
     */
    public function getSelectList($parentId = null, $nested = 0)
    {
    	// output 
    	$output=array();
    	
    	// find all rows
    	$where = ($parentId == null) ? 
    		$this->select()->where('ParentId IS NULL') : 
    		$this->select()->where('ParentId = ?', $parentId);

    	foreach ($this->fetchAll($where) as $category) {
			$output[$category->Id] = str_repeat(' - ', $nested).$category->Name;
			$rows = $this->getSelectList($category->Id, $nested+1);
			foreach ($rows as $key=>$value) {
				$output[$key] = $value;
			}
    	}
    	return $output;
    }
}
