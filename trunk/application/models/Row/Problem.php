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
	
	/**
	 * Get the object for problem accept
	 *
	 * @return Row_ProblemAccept
	 */
    public function getAcceptVote($userId)
    {
    	$paTable = new ProblemAccept();
    	
    	// if user is not loaded return empty row
    	if (!$this->Id) {
    		throw new Exception('Object no initialized');
    	}
    	
    	// if no row exists, return empty one
    	$rows = $paTable->find($userId, $this->Id);
    	if (count($rows) != 1) {
    		return $paTable->createRow(array(
    			'ProblemId' => $this->Id,
    			'UserId'	=> $userId
    		));
    	}
    	return $rows->current();
    }
}

?>