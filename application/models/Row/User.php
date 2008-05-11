<?php

class Row_User extends Zend_Db_Table_Row_Abstract 
{
	/**
	 * Get full name of a user
	 *
	 * @return string
	 */
	public function getFullname()
	{
		return $this->FullName;		
	}
	
	/**
	 * Get the object for problem accept
	 *
	 * @return Row_ProblemAccept
	 */
    public function getAcceptVote($problemId)
    {
    	$paTable = new ProblemAccept();
    	
    	// if user is not loaded return empty row
    	if (!$this->Id) {
    		throw new Exception('Object no initialized');
    	}
    	
    	// if no row exists, return empty one
    	$rows = $paTable->find($this->Id, $problemId);
    	if (count($rows) != 1) {
    		return $paTable->createRow(array(
    			'UserId' => $this->Id,
    			'ProblemId' =>$problemId
    		));
    	}    	
    	return $rows->current();
    }
    
    public function getSolutionAcceptVote($solutionId)
    {
    	$saTable = new SolutionAccept();
    	
    	// if user is not loaded return empty row
    	if (!$this->Id) {
    		throw new Exception('Object no initialized');
    	}
    	
    	// if no row exists, return empty one
    	$rows = $saTable->find($this->Id, $solutionId);
    	if (count($rows) != 1) {
    		return $saTable->createRow(array(
    			'UserId' => $this->Id,
    			'SolutionId' =>$solutionId
    		));
    	}    	
    	return $rows->current();
    }
}

?>