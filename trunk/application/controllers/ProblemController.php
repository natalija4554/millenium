<?php
define('PROBLEM_LISTING', 10);

/**
 * Controller pre problemy
 * 
 * 
 */
class ProblemController extends Colla_Controller_Action
{
	/**
	 * Default view of a problem
	 *
	 */
	public function viewAction()
	{
		// param ID
		if (!($Id = $this->getRequest()->getParam('Id', null))) {
			$this->_helper->FlashMessenger->addMessage('Please Select a Problem');
			$this->_redirect('/problem/list');
			return;
		}
		
		// get the problem 
		$problemTable = new Problem();
		$problem = $problemTable->findProblem($Id);
		$this->view->problem = $problem;
		$this->view->comments = $problem->findDependentRowset('Comment', null, $problemTable->select()->where('SolutionID IS NULL'));
		$this->view->solutions = $problem->findSolution();
		$this->view->acceptVote = $problem->getAcceptVote($this->view->user->Id);
	}
	
	public function changedefinitionAction()
	{
		// param ID
		if (!($Id = $this->getRequest()->getParam('Id', null))) {
			$this->_helper->FlashMessenger->addMessage('Please Select a Problem');
			$this->_redirect('/problemarea/problems');
			return;
		}
		// problem in state new 
		$form = new Form_ProblemChange($Id);
		$problemTable = new Problem();
		$problem = $problemTable->findProblem($Id);
		
		// only new problem can be changed
		if ($problem->State != Problem::STATE_NEW) {
			throw new Exception('Only new problem can be changed');
		}
		if ($problem->CreatedBy != null && $problem->CreatedBy != $this->view->user->Id) {
			throw new Exception('Only original craetor can change this problem');
		}
		
		if ($this->getRequest()->isPost()) {
			if ($form->isValid($_POST)) {
				
				$adapter = $problemTable->getAdapter();
				$adapter->beginTransaction();			
				
				// save problem
				$problem->Name = $form->getValue('Name');
				$problem->Definition = $form->getValue('Definition');
				$problem->save();
				
				// history
				$pHistory = new ProblemHistory();
				$history = $pHistory->createRow(array(
					'ProblemId' => $problem->Id,
					'Event'		=> Problem::EVENT_CHANGE,
					'Note'		=> $_POST['Note'],
					'Created'	=> new Zend_Db_Expr('NOW()'),
					'CreatedBy'	=> Zend_Registry::get('User')->Id
				));
				$history->save();
				
				// clear voting
				$paTable = new ProblemAccept();
				$paTable->delete(array(
					'ProblemId' => $problem->Id
				));				
				
				$adapter->commit();
				$this->_helper->FlashMessenger->addMessage('Definícia problému bola zmenená boli odstránené všetky hlasovania za akceptáciu problému.');
				$this->_redirect('/problem/view/Id/'.$problem->Id);
				return;	
			}			
		} else {
			$form->getElement('Name')->setValue($problem->Name);
			$form->getElement('Definition')->setValue($problem->Definition);
		}
		$this->view->form = $form;	
	}
	
	public function removeAction()
	{
		
	}
	
	public function mergeAction()
	{
		
	}
	
	/**
	 * Add comment to a problem
	 * 
	 */
	public function addCommentAction()
	{
		// param ID
		if (!($problemId = $this->getRequest()->getParam('Id', null))) {
			$this->_helper->FlashMessenger->addMessage('Please Select a Problem');
			$this->_redirect('/problemarea/problems');
			return;
		}
		
		$form = new Form_ProblemComment($problemId);
		if ($this->getRequest()->isPost()) {
			if ($form->isValid($_POST)) {
				// posli komentar
				$commentTable = new ProblemComment();
				$row = $commentTable->createRow();
				$row->ProblemId	= $problemId;
				$row->Title 	= $form->getValue('Title');
				$row->Body 		= $form->getValue('Body');
				$row->UserId	= $this->view->user->Id;
				$row->Created	= new Zend_Db_Expr('NOW()');
				$row->save();
				$this->_helper->FlashMessenger->addMessage('New comment posted');
				$this->_redirect('/problem/view/Id/'.$problemId);
			}
		}		
		$this->view->form = $form;
	}
	
	/**
	 * Akceptuje nove riesenie
	 *
	 */
	public function acceptAction()
	{
		$problemId = $this->getRequest()->getParam('problemId');
		$problemTable = new Problem();
		$problem = $problemTable->find($problemId)->current();
		if (!$problem) {
			$this->_helper->FlashMessenger->addMessage('No such problem!');
			$this->_redirect('/problemarea/problems');
		}
		
		// problem state need to be NEW
		if ($problem->State != Problem::STATE_NEW) {
			$this->_helper->FlashMessenger->addMessage('Je mozne akceptovat len novy problem!');
			$this->_redirect('/problem/view/Id/'.$problemId);
		}
		
		// Assert permission
		$this->checkAllowed('PROBLEM', 'ACCEPT');
		
		// parameters
		$acceptcommenttitle = $this->getRequest()->getParam('accept-comment-title');
		$acceptcommentbody	= $this->getRequest()->getParam('accept-comment-body');
		$acceptnote 		= $this->getRequest()->getParam('accept-note');

		// begin transaction
		$adapter = $problemTable->getAdapter();
		$adapter->beginTransaction();
		
		// save problem
		$problem->State = Problem::STATE_APPROVED;
		$problem->save();
			
		// save comment
		if ($this->getRequest()->getParam('accept-comment', null)) {
			$cTable = new Comment();
			$comment = $cTable->createRow(array(
				'ProblemId' 	=> $problem->Id,
				'UserId'		=> Zend_Registry::get('User')->Id,
				'Title'			=> $acceptcommenttitle,
				'System'		=> 1,
				'Event'			=> Problem::EVENT_APPROVE,	
				'Body'			=> $acceptcommentbody,
				'Created'		=> new Zend_Db_Expr('NOW()')
			));
			$comment->save();
		}
		
		// save history
		$hTable = new ProblemHistory();
		$history = $hTable->createRow(array(
			'ProblemId'			=> $problem->Id,
			'Event'				=> Problem::EVENT_APPROVE, 
			'Note'				=> $acceptnote,
			'Created'			=> new Zend_Db_Expr('NOW()'),
			'CreatedBy'			=> Zend_Registry::get('User')->Id
		));
		$history->save();
		
		// commit transaction
		$adapter->commit();
		$this->_helper->FlashMessenger->addMessage('Problem bol akceptovany.');
		$this->_redirect('/problem/view/Id/'.$problemId);
	}
	
	/**
	 * Zavrhne riesenie
	 *
	 */
	public function declineAction()
	{
		$problemId = $this->getRequest()->getParam('problemId');
		
		// read problem
		$problemTable = new Problem();
		$problem = $problemTable->find($problemId)->current();
		if (!$problem) {
			$this->_helper->FlashMessenger->addMessage('No such problem!');
			$this->_redirect('/problemarea/problems');
		}
		
		// problem state have to be NEW
		if ($problem->State != Problem::STATE_NEW) {
			$this->_helper->FlashMessenger->addMessage('Je mozne akceptovat len novy problem!');
			$this->_redirect('/problem/view/Id/'.$problemId);
		}
		
		// Assert permission
		$this->checkAllowed('PROBLEM', 'ACCEPT');
		
		// parameters
		$acceptcommenttitle = $this->getRequest()->getParam('decline-comment-title');
		$acceptcommentbody	= $this->getRequest()->getParam('decline-comment-body');
		$acceptnote 		= $this->getRequest()->getParam('decline-note'); 

		// save problem
		$problemTable->getAdapter()->beginTransaction();
		$problem->State = Problem::STATE_DELETED;
		$problem->save();
			
		// save comment
		if ($this->getRequest()->getParam('decline-comment', null)) {
			$cTable = new Comment();
			$comment = $cTable->createRow(array(
				'ProblemId' 	=> $problem->Id,
				'UserId'		=> Zend_Registry::get('User')->Id,
				'Title'			=> $acceptcommenttitle,
				'System'		=> 1,
				'Event'			=> Problem::EVENT_DECLINE,	
				'Body'			=> $acceptcommentbody,
				'Created'		=> new Zend_Db_Expr('NOW()')
			));
			$comment->save();
		}
		
		// save history
		$hTable = new ProblemHistory();
		$history = $hTable->createRow(array(
			'ProblemId'			=> $problem->Id,
			'Event'				=> Problem::EVENT_DECLINE, 
			'Note'				=> $acceptnote,
			'Created'			=> new Zend_Db_Expr('NOW()'),
			'CreatedBy'			=> Zend_Registry::get('User')->Id
		));
		$history->save();
		
		// commit
		$problemTable->getAdapter()->commit(); 
		$this->_helper->FlashMessenger->addMessage('Problem bol zavrhnutý.');
		$this->_redirect('/problem/view/Id/'.$problemId);
	}
	
	public function voteAcceptYesAction()
	{
		$this->_vote(ProblemAccept::VOTE_YES);
	}
	
	public function voteAcceptNoAction()
	{
		$this->_vote(ProblemAccept::VOTE_NO);
	}
	
	public function voteAcceptIgnoreAction()
	{
		$this->_vote(ProblemAccept::VOTE_IGNORE);
	}
	
	private function _vote($vote)
	{
		$problemId = $this->getRequest()->getParam('problemId');
		$user = Zend_Registry::get('User');
		$pa = new ProblemAccept();
		$row = $user->getAcceptVote($problemId);
		$row->Vote = $vote;
		$row->VoteTime = new Zend_Db_Expr('NOW()');
		$row->save();
			
	
		// get it again and display in json
		$this->view->acceptVote = $user->getAcceptVote($problemId);
		$this->render('ajax-vote-accept');
	}
	
	public function voteImportanceAction()
	{
		$problemId = $this->getRequest()->getParam('problemId');
		$piTable = new ProblemImportance();
		$pImportance = $piTable->fetchRow($piTable->select()
			->where('UserId = ?', Zend_Registry::get('User')->Id)
			->where('ProblemId = ?', $problemId)
		);
		if (!$pImportance) {
			$pImportance = $piTable->createRow(array(
				'UserId' 	=> Zend_Registry::get('User')->Id,
				'ProblemId'	=> $problemId 
			));
		}		
		$pImportance->Importance = $this->getRequest()->getParam('vote');
		$pImportance->VoteTime = new Zend_Db_Expr('NOW()');
		$pImportance->save();
		$this->view->data = array($pImportance->Importance);
	}
	
	public function ajaxVoteInfoAction()
	{
		$problemId = $this->getRequest()->getParam('problemId');
		$voteInfo = new VoteInfo($problemId);
		$this->view->data = $voteInfo->toArray();
	}
	
	public function partialSimilarListAction()
	{
		// this wies is not using layouts
		$this->_helper->layout()->disableLayout();
		
		// requested keywords
		$keywords = $this->getRequest()->getParam('keywords');
		$keywords = array_unique(explode(' ', trim($keywords)));
		
		// no keywords given
		if (count($keywords) == 0) {
			$this->render('partial-similar-list-empty');
			return;
		}
		$pkTable = new ProblemKeyword();
		$select = $pkTable->getAdapter()->select()
			->from(array('pk' => 'problem_keywords'), array('p.*', 'pk.*', 'tcount' => new Zend_Db_Expr('COUNT(*)')))
			->where('pk.Keyword = ?', array_shift($keywords))
			->joinLeft(array('p' => 'problems'), 'p.Id = pk.ProblemId')
			->group('ProblemId')
			->order('tcount DESC');
		foreach ($keywords as $k) {
			$select->orWhere('pk.Keyword = ?', $k);
		}
		$rows = $pkTable->getAdapter()->fetchAll($select);
		
		// no problems found
		if (count($rows) == 0) {
			$this->render('partial-similar-list-none');
			return;
		}
		$this->view->problems = $rows;
	}
	
	/**
	 * List all problems, filter and search
	 */
	public function listAction()
	{
		// session data for listing
		$filter = new Zend_Session_Namespace('Problem_Filter');
		$adapter = Zend_Db_Table_Abstract::getDefaultAdapter();
		$ProblemAreaId = Colla_App::getInstance()->getProblemArea();

		// SET FILTER
		$filter->type = $this->getRequest()->getParam('filter');
		if ($filter->type) { 
			switch ($filter->type) {
				case 'acceptance':
					$filter->unsetAll();
					$filter->type = 'acceptance';
					if (!$this->hasIdentity()) {
						$this->_helper->FlashMessenger->addMessage('Na filtrovanie neakceptovaných požiadaviek musíte byť prihlásený.');
						$this->_redirect('/auth/login');
					}
					$filter->acceptance 		= true;			
					$filter->acceptanceUser 	= Zend_Registry::get('User')->Id; 
					break;

				case 'solutions':
					$filter->unsetAll();
					$filter->type = 'solutions';
					$filter->notNew = true;
					$filter->notImpVote = true;
					$filter->notImpUser = Zend_Registry::get('User')->Id;
					break;
				
				case 'form':
					$filter->unsetAll();
					$filter->type = 'form';
					$filter->state 	= $this->getRequest()->getParam('problemState');
					$filter->text 	= $this->getRequest()->getParam('text');
					break;
					
				case 'keyword':
					$filter->unsetAll();
					$filter->type = 'keyword';
					$filter->keyword = $this->getRequest()->getParam('keyword');
					break;
					
				case 'none':
					$filter->unsetAll();
					break;
			}
		}

		// BASE SELECT
		$select = $adapter->select()
			->from(array('p' => 'problems'), 'count(*)')
			->join(array('u'=>'users'), 'u.Id = p.CreatedBy', array())
			->where('p.ProblemAreaId = ?', $ProblemAreaId);

		// APPLY FILTER
		if ($filter->state) {
			$select->where('p.State = ?', $filter->state);
		} else {
			$select->where('p.State != ?', Problem::STATE_DELETED);
		}
		if ($filter->text) {
			$select->where("p.Name LIKE '%".addslashes($filter->text)."%'");
		}
		if ($filter->keyword) {
			$select->where("(SELECT count(*) FROM problem_keywords pk WHERE pk.problemId=p.Id AND pk.keyword=".$adapter->quote($filter->keyword).") > 0");
		}
		if ($filter->acceptance) {
			$select->where('p.State = ?', Problem::STATE_NEW);
			$select->where("(SELECT count(*) FROM problem_accept pa WHERE p.Id = pa.ProblemId AND pa.UserId = ?) = 0", $filter->acceptanceUser);
		}
		if ($filter->notNew) {
			$select->where('p.State != ?', Problem::STATE_NEW);
			$select->where('p.State != ?', Problem::STATE_DELETED);
		}
		if ($filter->notImpVote) {
			$select->where("(SELECT count(*) FROM problem_importance pi WHERE p.Id = pi.ProblemId AND pi.UserId = ?) = 0", $filter->notImpUser);
		}

		// DATA FROM SELECT
		$count = $adapter->fetchOne($select);
		$pageNumber = (int) $this->getRequest()->getParam('page', 0);
		$select->reset(Zend_Db_Select::COLUMNS)
			->limit(PROBLEM_LISTING,  $pageNumber * PROBLEM_LISTING)
			->order('Id')
			->columns(array('p.*', 'u.Username'))
			->columns(array('Keywords' => new Zend_Db_Expr('(SELECT GROUP_CONCAT(Keyword ORDER BY Keyword SEPARATOR \' \') FROM problem_keywords pk WHERE pk.problemId=p.Id GROUP BY problemId)')));
		$problems = $adapter->fetchAll($select);
		
		// VIEW
		$this->view->filter = $filter;
		$this->view->page		= $pageNumber;
		$this->view->listing	= PROBLEM_LISTING;
		$this->view->count		= $count;
		$this->view->problems 	= $problems;
	}
	
	public function displayStatusAction()
	{
		$problemId = $this->getRequest()->getParam('problemId');
		$enableLayout = $this->getRequest()->getParam('enableLayout');
		if (!$enableLayout) {
			$this->_helper->layout()->disableLayout();
		}
		
		// zisti ci hlasoval
		$paTable = new ProblemAccept();
		$rows = $paTable->fetchAll($paTable->select()
			->where('ProblemId = ?', $problemId)
			->where('UserId = ?', Zend_Registry::get('User')->Id)
		);
		
		$this->view->problemId = $problemId;
		$this->view->problemAccept = (count($rows) == 1) ?
			$rows->current() :
			$paTable->createRow(array(
				'ProblemId' => $problemId,
				'UserId' => Zend_Registry::get('User')->Id
			));
	}
	
	public function displayImportanceStatusAction()
	{
		$problemId = $this->getRequest()->getParam('problemId');
		$enableLayout = $this->getRequest()->getParam('enableLayout');
		if (!$enableLayout) {
			$this->_helper->layout()->disableLayout();
		}
		
		// all possible importances
		$iTable = new Importance();
		$importances = $iTable->fetchAll(null, array('Importance'));
		
		// current one 
		$piTable = new ProblemImportance();
		$rows = $piTable->fetchAll($piTable->select()
			->from(array('pi' => 'problem_importance'), array('pi.*'))
			->setIntegrityCheck(false)
			->join(array('i' => 'importances'), 'i.Importance = pi.Importance', 'i.Name as ImportanceName')
			->where('pi.ProblemId = ?', $problemId)
			->where('UserId = ?', Zend_Registry::get('User')->Id)
		);
		
		$this->view->problemId = $problemId;
		$this->view->importances = $importances;
		$this->view->problemImportance = (count($rows) == 1) ?
			$rows->current() :
			$piTable->createRow(array(
				'ProblemId' => $problemId,
				'UserId'	=> Zend_Registry::get('User')->Id
			));
	}
	
	public function displayAcceptStatusAction()
	{
		$problemId = $this->getRequest()->getParam('problemId');
		$enableLayout = $this->getRequest()->getParam('enableLayout');
		if (!$enableLayout) {
			$this->_helper->layout()->disableLayout();
		}
		$this->view->voteInfo 	= new VoteInfo($problemId);
	}
	
    public function historyAction()
    {
    	$problemId = $this->getRequest()->getParam('problemId');
    	$adapter = Zend_Db_Table_Abstract::getDefaultAdapter();
    	$select = $adapter->select()
    		->from(array('ph' => 'problem_history'), array('*'))
    		->join(array('u' => 'users'), 'u.Id = ph.CreatedBy', array('Username'))
    		->where('ProblemId = ?', $problemId);
    	$this->view->records = $adapter->fetchAll($select);
    	$this->view->problemId = $problemId;
    }
    
    public function solveAction()
    {
    	$this->checkAllowed('PROBLEM', 'SOLVE');
    	$problemId = $this->getRequest()->getParam('problemId');
		$problemTable = new Problem();
		$problem = $problemTable->find($problemId)->current();
		if (!$problem) {
			$this->_helper->FlashMessenger->addMessage('No such problem!');
			$this->_redirect('/problem/list');
		}
		
		// over ci ma uzatvorene riesenia
		$sTable = new Solution();
		$solutions = $sTable->fetchAll($sTable->select()
			->where('ProblemId = ?', $problemId)
		);
		foreach ($solutions as $s) {
			if ($s->State == Solution::STATE_NEW) {
				$this->_helper->FlashMessenger->addMessage('Nemožno uzavrieť riešenie problému, nakoľko nie sú uzavreté všetky riešenia!');
				$this->_redirect('/problem/view/Id/'.$problem->Id);
			}
		}
		
		$problemTable->getAdapter()->beginTransaction();
		
		$problem->State = Problem::STATE_SOLVED;
		$problem->save();
		
		$hTable = new ProblemHistory();
		$history = $hTable->createRow(array(
			'ProblemId'			=> $problem->Id,
			'Event'				=> Problem::EVENT_SOLVE, 
			'Note'				=> '',
			'Created'			=> new Zend_Db_Expr('NOW()'),
			'CreatedBy'			=> Zend_Registry::get('User')->Id
		));
		$history->save();
		
		$problemTable->getAdapter()->commit();
		
		$this->_redirect('/problem/view/Id/'.$problem->Id);
    }
}
?>