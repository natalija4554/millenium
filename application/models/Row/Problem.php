<?php

class Row_Problem extends Zend_Db_Table_Row_Abstract 
{
	/**
	 * Vrati vsetky komentare k problemu
	 *
	 * @todo Zend_Db_Table_Rowset
	 */
	public function getComments()
	{
		// check if comment is loaded
		if (!$this->isConnected()) {
			throw new Exception('Not connected !');
		}
 
	} 
	
	/**
	 * Get a name for display purpose
	 *
	 * In form: "/parentcategory/subcategory"
	 */
	public function getFullCategoryName()
	{
		$output = '/';

		// get actual categbory
		$categoryTable = new Category();
		$rows = $categoryTable->find($this->CategoryId);
		$category = $rows->current();
		if (!$category) {
			return $output;
		}

		// 1 uroven
		$output .= $category->Name;
		return $output;	
	}
}

?>