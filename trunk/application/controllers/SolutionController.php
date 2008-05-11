<?php
/**
 * Solution for problems
 * 
 * 
 */
class SolutionController extends Colla_Controller_Action
{
	
	/**
	 * Zobrazi informácie o riešení
	 *
	 */
	public function viewAction()
	{
		// get solution
		$solutionId = $this->getRequest()->getParam('id');
		$sTable = new Solution();
		$rows = $sTable->find($solutionId);
		if (count($rows) != 1) {
			throw new Exception('No such solution'); 
		}
		$solution = $rows->current();
		
		
		
		$this->view->solution = $solution;
	}
	
	/**
	 * Create new solution
	 *
	 */
	public function createAction()
	{
		// check permission
		$this->checkAllowed('SOLUTION', 'CREATE');
		
		// to a problem
		$problemId = $this->getRequest()->getParam('id');
		
		// read problem
		$pTable = new Problem();
		$rows = $pTable->find($problemId);
		if (count($rows) != 1) {
			throw new Exception('Missing Id');
		}
		$problem = $rows->current();
		
		// now create a new form 
		$form = new Form_Solution(array(
			'problemId' => $problem->Id
		));
		
		// save it
		if ($this->getRequest()->isPost()) {
			if ($form->isValid($_POST)) {
				$sTable = new Solution();
				$solution = $sTable->createRow(array(
					'Name'			=> $form->getValue('Name'),
					'Definition'	=> $form->getValue('Definition'),
					'ProblemId' 	=> $problem->Id,
					'Created'		=> new Zend_Db_Expr('NOW()'),
					'CreatedBy'		=> Zend_Registry::get('User')->Id 
				));
				$solution->save();
				$this->_redirect('/problem/view/Id/'.$problem->Id);
			}	
		}
		$this->view->form = $form;
	}
	
	public function accept()
	{
		
	}
	
	public function reject()
	{
		
	}
	
	public function address()
	{
		
	}
	
	public function displayStatusAction()
	{
		$solutionId = $this->getRequest()->getParam('solutionId');
		$enableLayout = $this->getRequest()->getParam('enableLayout');
		if (!$enableLayout) {
			$this->_helper->layout()->disableLayout();
		}
		
		// zisti ci hlasoval
		$saTable = new SolutionAccept();
		$rows = $saTable->fetchAll($saTable->select()
			->where('SolutionId = ?', $solutionId)
			->where('UserId', Zend_Registry::get('User')->Id)
		);
		
		$this->view->solutionId = $solutionId;
		$this->view->solutionAccept = (count($rows) == 1) ?
			$rows->current() :
			$saTable->createRow(array(
				'SolutionId' => $solutionId,
				'UserId' => Zend_Registry::get('User')->Id
			));
	}
	
	public function voteAcceptYesAction()
	{
		$this->_vote(SolutionAccept::VOTE_YES);
	}
	
	public function voteAcceptNoAction()
	{
		$this->_vote(SolutionAccept::VOTE_NO);
	}
	
	public function voteAcceptIgnoreAction()
	{
		$this->_vote(SolutionAccept::VOTE_IGNORE);
	}
	
	private function _vote($vote)
	{
		$problemId = $this->getRequest()->getParam('solutionId');
		$user = Zend_Registry::get('User');
		$sa = new SolutionAccept();
		$row = $user->getSolutionAcceptVote($problemId);
		$row->Vote = $vote;
		$row->VoteTime = new Zend_Db_Expr('NOW()');
		$row->save();

		// no result
		exit();
	}
}
?>