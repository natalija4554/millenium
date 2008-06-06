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
		
		// get problem
		$pTable = new Problem();
		$problem = $pTable->find($solution->ProblemId)->current();
		
		// dependencies
		$dTable = new SolutionDependency();
		$dependencies = $dTable->fetchAll($dTable->select()
			->setIntegrityCheck(false)
			->from(array('d' => 'solution_dependency'), array('*'))
			->join(array('p' => 'problems'), 'p.Id = d.ProblemId', array('p.*'))
			->where('d.SolutionId = ?', $solutionId)
		);
		
		$this->view->problem 	= $problem;
		$this->view->solution 	= $solution;
		$this->view->dependencies = $dependencies;
		$this->view->comments = $solution->findComment();
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
				$sTable->getAdapter()->beginTransaction();
				
				// save solution
				$solution = $sTable->createRow(array(
					'Name'			=> $form->getValue('Name'),
					'Definition'	=> $form->getValue('Definition'),
					'ProblemId' 	=> $problem->Id,
					'Created'		=> new Zend_Db_Expr('NOW()'),
					'CreatedBy'		=> Zend_Registry::get('User')->Id 
				));
				$solutionId = $solution->save();

				// save history
				$hTable = new SolutionHistory();
				$history = $hTable->createRow(array(
					'SolutionId' 	=> $solutionId,
					'Event'		=> Solution::EVENT_CREATE,
					'Note'		=> $form->getValue('Note'),
					'Created'	=> new Zend_Db_Expr('NOW()'),
					'CreatedBy'	=> Zend_Registry::get('User')->Id
				));
				$history->save();
				
				$sTable->getAdapter()->commit();
				$this->_redirect('/problem/view/Id/'.$problem->Id);
			}	
		}
		$this->view->problem = $problem;
		$this->view->problemId = $problemId;
		$this->view->form = $form;
	}
	
	public function acceptAction()
	{
		$solutionId = $this->getRequest()->getParam('solutionId');
		$sTable = new Solution();
		$solution = $sTable->find($solutionId)->current();
		if (!$solution) {
			throw new Exception('No such solution !');
		}
		
		// problem state need to be NEW
		if ($solution->State != Solution::STATE_NEW) {
			$this->_helper->FlashMessenger->addMessage('Je mozne akceptovat len nové riešenie!');
			$this->_redirect('/solution/view/id/'.$solutionId);
		}
		
		// Assert permission
		$this->checkAllowed('SOLUTION', 'ACCEPT');

		// begin transaction
		$adapter = $sTable->getAdapter();
		$adapter->beginTransaction();
		
		// save problem
		$solution->State = Solution::STATE_APPROVED;
		$solution->save();
			
		// save comment
		if ($this->getRequest()->getParam('comment-add')) {
			$cTable = new Comment();
			$comment = $cTable->createRow(array(
				'ProblemId' 	=> $solution->ProblemId,
				'SolutionId'	=> $solution->Id,
				'UserId'		=> Zend_Registry::get('User')->Id,
				'Title'			=> $_POST['comment-title'],
				'System'		=> 1,
				'Event'			=> Solution::EVENT_APPROVE,	
				'Body'			=> $_POST['comment-body'],
				'Created'		=> new Zend_Db_Expr('NOW()')
			));
			$comment->save();
		}
		
		// save history
		$hTable = new SolutionHistory();
		$history = $hTable->createRow(array(
			'SolutionId'		=> $solution->Id,
			'Event'				=> Solution::EVENT_APPROVE, 
			'Note'				=> $_POST['note'],
			'Created'			=> new Zend_Db_Expr('NOW()'),
			'CreatedBy'			=> Zend_Registry::get('User')->Id
		));
		$history->save();
		
		// decline others
		if ($this->getRequest()->getParam('decline-others')) {
			$solutions = $sTable->fetchAll($sTable->select()
				->where('State = ?', Solution::STATE_NEW)
				->where('ProblemId = ?', $solution->ProblemId)
				->where('Id != ?', $solution->Id)
			);
			foreach ($solutions as $solution) {
				$solution->decline(
					true,
					$_POST['comment-decline-title'],
					$_POST['comment-decline-body'],
					$_POST['note']
				);
			}
		}
		
		// commit transaction
		$adapter->commit();
		$this->_helper->FlashMessenger->addMessage('Riešenie bolo akceptované.');
		$this->_redirect('/solution/view/id/'.$solutionId);
	}
	
	public function declineAction()
	{
		$solutionId = $this->getRequest()->getParam('solutionId');
		$sTable = new Solution();
		$solution = $sTable->find($solutionId)->current();
		if (!$solution) {
			throw new Exception('No such solution !');
		}
		
		// problem state need to be NEW
		if ($solution->State != Solution::STATE_NEW) {
			$this->_helper->FlashMessenger->addMessage('Je mozne akceptovat len nové riešenie!');
			$this->_redirect('/solution/view/id/'.$solutionId);
		}
		
		// Assert permission
		$this->checkAllowed('SOLUTION', 'ACCEPT');
		
		// decline
		$adapter = $sTable->getAdapter();
		$adapter->beginTransaction();
		$solution->decline(
			isset($_POST['comment-add']),
			$_POST['comment-title'],
			$_POST['comment-body'],
			$_POST['note']
		);
		$adapter->commit();
		
		$this->_helper->FlashMessenger->addMessage('Riešenie bolo zavrhnuté.');
		$this->_redirect('/solution/view/id/'.$solutionId);
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
			->where('UserId = ?', Zend_Registry::get('User')->Id)
		);
		
		$this->view->solutionId = $solutionId;
		$this->view->solutionAccept = (count($rows) == 1) ?
			$rows->current() :
			$saTable->createRow(array(
				'SolutionId' => $solutionId,
				'UserId' => Zend_Registry::get('User')->Id
			));
	}
	
	public function displayAcceptStatusAction()
	{
		$solutionId = $this->getRequest()->getParam('solutionId');
		$enableLayout = $this->getRequest()->getParam('enableLayout');
		if (!$enableLayout) {
			$this->_helper->layout()->disableLayout();
		}
		$this->view->voteInfo 	= new SolutionVoteInfo($solutionId);
	}
	public function displayAcceptAction()
	{
		// disable layout
		$this->_helper->layout()->disableLayout();
		$solutionId = $this->getRequest()->getParam('solutionId');
		$this->view->solutionId = $solutionId;
	}
	public function displayDeclineAction()
	{
		// disable layout
		$this->_helper->layout()->disableLayout();
		$solutionId = $this->getRequest()->getParam('solutionId');
		$this->view->solutionId = $solutionId;
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
		$solutionId = $this->getRequest()->getParam('solutionId');
		$user = Zend_Registry::get('User');
		$sa = new SolutionAccept();
		$row = $user->getSolutionAcceptVote($solutionId);
		$row->Vote = $vote;
		$row->VoteTime = new Zend_Db_Expr('NOW()');
		$row->save();
		exit();
	}
	
	public function dependencyAction()
	{
		$solutionId = $this->getRequest()->getParam('solutionId');
		
		$sTable = new Solution();
		$rows = $sTable->find($solutionId);
		if (count($rows) != 1) {
			throw new Exception('No such solution'); 
		}
		$solution = $rows->current();

		// crate dependency
		if (isset($_POST['createDependency'])) {
			$dTable = new SolutionDependency();
			foreach ($_POST['problem_id'] as $problemId) {
				$row = $dTable->createRow(array(
					'SolutionId' => $solutionId,	
					'ProblemId' => $problemId
				));
				$row->save();
			}
			$this->_redirect('/solution/view/id/'.$solutionId);
		}
		
		$this->view->solution = $solution;
		if (isset($_POST['searchText'])) {
			$pTable = new Problem();

			// exclude actuall dependency  and parent problem
			$this->view->problems = $pTable->fetchAll($pTable->select()
				->setIntegrityCheck(false)
				->from(array('p'=>'problems'), 'p.*')
				->joinLeft(array('d'=>'solution_dependency'), 'd.ProblemId = p.Id && d.SolutionId='.(int)$solutionId, 'd.SolutionId')
				->where('d.SolutionId IS NULL')
				->where('p.Id != ?', $solution->ProblemId)
				->where('p.State != ?', Problem::STATE_DELETED)
				->where('p.State != ?', Problem::STATE_NEW)
				->where('p.State != ?', Problem::STATE_CLOSED)
				->where("p.Name LIKE '%".addslashes($_POST['searchText'])."%'")
			);
		}
	}
	
	public function removedependencyAction()
	{
		$this->checkAllowed('SOLUTION', 'MANAGE_DEPENDENCY');
		
		$solutionId = $this->getRequest()->getParam('solutionId');
		$problemId 	= $this->getRequest()->getParam('problemId');
		
		// remove
		$dTable = new SolutionDependency();
		if ($row = $dTable->fetchRow($dTable->select()
			->where('ProblemId = ?', $problemId)
			->where('SolutionId = ?', $solutionId)
		)) {
			$row->delete();
		}
		$this->_redirect('/solution/view/id/'.$solutionId);
	}
}
?>