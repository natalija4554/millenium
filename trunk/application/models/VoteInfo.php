<?php

/**
 * Retrieve voting informations
 *
 */
class VoteInfo 
{
	protected $_total;
	protected $_voted;
	protected $_votes = array();
	
	public function __construct($problemId)
	{
		$problemAccept = new ProblemAccept();
		$rows = $problemAccept->fetchAll($problemAccept->select()->where('ProblemId = ?', $problemId));
		
		// voted
		$this->_voted = count($rows);
		
		// options
		foreach ($rows as $row) {
			if (!isset($this->_votes[$row->Vote])) {
				$this->_votes[$row->Vote] = 0;
			}
			$this->_votes[$row->Vote] += 1;
		}
		// total posible voters -> ALL users with privilege PROBLEM ACCEPT_VOTE
		$roles = array();
		$roleTable = new AclRole();
		foreach ($roleTable->fetchAll() as $role) {
			if (Zend_Registry::get('Colla_Acl')->isAllowed($role->id, 'PROBLEM', 'ACCEPT_VOTE')) {
				$roles[] = $role->id;
			}
		}
		$userTable = new User();
		$select = $userTable->select();
		$select->from('users', array('Id'));
		foreach ($roles as $role) {
			$select->orWhere('RoleId = ?', $role);
		}
		$rows = $userTable->fetchAll($select);
		$this->_total = count($rows);	
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
				ProblemAccept::VOTE_YES => $this->getVote(ProblemAccept::VOTE_YES),
				ProblemAccept::VOTE_NO => $this->getVote(ProblemAccept::VOTE_NO),
				ProblemAccept::VOTE_IGNORE => $this->getVote(ProblemAccept::VOTE_IGNORE)
			)
		);
	}
}


?>