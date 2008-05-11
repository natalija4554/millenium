<?php

/**
 * Retrieve voting informations
 *
 */
class SolutionVoteInfo 
{
	protected $_total;
	protected $_voted;
	protected $_votes = array();
	
	public function __construct($solutionId)
	{
		$solutionAccept = new SolutionAccept();
		$rows = $solutionAccept->fetchAll($solutionAccept->select()->where('SolutionId = ?', $solutionId));
		
		// voted
		$this->_voted = count($rows);
		
		// options
		foreach ($rows as $row) {
			if (!isset($this->_votes[$row->Vote])) {
				$this->_votes[$row->Vote] = 0;
			}
			$this->_votes[$row->Vote] += 1;
		}
		
		// total posible voters -> ALL users with privilege SOLUTION ACCEPT_VOTE
		$roles = array();
		$roleTable = new AclRole();
		foreach ($roleTable->fetchAll() as $role) {
			if (Zend_Registry::get('Colla_Acl')->isAllowed($role->id, 'SOLUTION', 'ACCEPT_VOTE')) {
				$roles[] = $role->id;
			}
		}
		if (count($roles) > 0) {
			$userTable = new User();
			$select = $userTable->select();
			$select->from('users', array('Id'));
			foreach ($roles as $role) {
				$select->orWhere('RoleId = ?', $role);
			}
			$rows = $userTable->fetchAll($select);
			$this->_total = count($rows);
		} else {
			$this->_total = 0;
		}	
	}
	
	public function getTotal()
	{
		return $this->_total;
	}
	
	public function getVoted()
	{
		return $this->_voted;
	}
	
	public function getVote($vKey)
	{
		if (!isset($this->_votes[$vKey])) {
			return 0;
		}
		return $this->_votes[$vKey];
	}
	
	public function toArray()
	{
		return array(
			'total' => $this->getTotal(),
			'voted'	=> $this->getVoted(),
			'votes' => array(
				SolutionAccept::VOTE_YES => $this->getVote(SolutionAccept::VOTE_YES),
				SolutionAccept::VOTE_NO => $this->getVote(SolutionAccept::VOTE_NO),
				SolutionAccept::VOTE_IGNORE => $this->getVote(SolutionAccept::VOTE_IGNORE)
			)
		);
	}
}


?>