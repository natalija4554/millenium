<?php
class Solution extends Colla_Db_Table_Abstract
{
	protected $_name = 'solutions';
	
	protected $_referenceMap = array(
    	'Problem' => array(
    		'columns'		=> array('ProblemId'),
    		'refTableClass' => 'Problem'
    	)
    );
    
	/**
	 * Get the object for problem accept
	 *
	 * @return Row_ProblemAccept
	 */
    public function getAcceptVote($userId)
    {
    	$sTable = new SolutionAccept();
    	
    	// if user is not loaded return empty row
    	if (!$this->Id) {
    		throw new Exception('Object no initialized');
    	}
    	
    	// if no row exists, return empty one
    	$rows = $sTable->find($userId, $this->Id);
    	if (count($rows) != 1) {
    		return $sTable->createRow(array(
    			'SolutionId' => $this->Id,
    			'UserId'	=> $userId
    		));
    	}
    	return $rows->current();
    }
}


?>