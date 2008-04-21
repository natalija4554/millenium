<?php
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
			$this->_redirect('/problemarea/problems');
			return;
		}
		
		// get the problem 
		$problemTable = new Problem();
		$problem = $problemTable->findProblem($Id);
		$this->view->problem = $problem;
		$this->view->comments = $problem->findComment();
		$this->view->acceptVote = $problem->getAcceptVote($this->view->user->Id);
		$this->view->voteInfo 	= new VoteInfo($problem->Id);
	}
	
	public function changecategoryAction()
	{
		
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
				$problem->Name = $form->getValue('Name');
				$problem->Definition = $form->getValue('Definition');
				$problem->save();
				$this->_helper->FlashMessenger->addMessage('Definícia bola zmenená');
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
	public function approveAction()
	{
		// read problem ID
		$problemId = $this->getRequest()->getParam('ProblemId');
		if (!$problemId) {
			$this->_helper->FlashMessenger->addMessage('Please select a problem!');
			$this->_redirect('/problemarea/problems');
			return;
		}
		
		// read problem
		$problemTable = new Problem();
		$problem = $problemTable->find($problemId)->current();
		if (!$problem) {
			$this->_helper->FlashMessenger->addMessage('No such problem!');
			$this->_redirect('/problemarea/problems');
			return;
		}
		
		// problem musi byt v stave novy
		if ($problem->State != Problem::STATE_NEW) {
			$this->_helper->FlashMessenger->addMessage('Je mozne akceptovat len novy problem!');
			$this->_redirect('/problem/view/Id/'.$problemId);
			return;
		}
		
		// formular
		$form = new Form_Approve($problemId);		
		if ($this->getRequest()->isPost()) {
			if ($form->isValid($_POST)) {
				
				// AKCEPTUJ
				$problemTable->getAdapter()->beginTransaction();
				
				// save 
				$problem->State = Problem::STATE_APPROVED;
				$problem->Modified = new Zend_Db_Expr('NOW()');
				$problem->save();
				
				// save comment
				$problemComment = new ProblemComment();
				$comment = $problemComment->createRow();
				$comment->ProblemId		= $problemId;
				$comment->UserId		= $this->view->user->Id;
				$comment->Title			= $form->getValue('Title');
				$comment->Body			= $form->getValue('Body');
				$comment->Created		= new Zend_Db_Expr('NOW()');
				$comment->System		= 1;
				$comment->save();
				
				// commit & redirect
				$problemTable->getAdapter()->commit();
				$this->_helper->FlashMessenger->addMessage('Problem bol akceptovany.');
				$this->_redirect('/problem/view/Id/'.$problemId);
			}
		} else {
			$form->getElement('Title')->setValue('Problem has been accepted!');
			$form->getElement('Body')->setValue('Moderator accepted this problem as approved.');
		}
		$this->view->form = $form;
	}
	
	/**
	 * Zavrhne riesenie
	 *
	 */
	public function declineAction()
	{
		// read problem ID
		$problemId = $this->getRequest()->getParam('ProblemId');
		if (!$problemId) {
			$this->_helper->FlashMessenger->addMessage('Please select a problem!');
			$this->_redirect('/problemarea/problems');
			return;
		}
		
		// read problem
		$problemTable = new Problem();
		$problem = $problemTable->find($problemId)->current();
		if (!$problem) {
			$this->_helper->FlashMessenger->addMessage('No such problem!');
			$this->_redirect('/problemarea/problems');
			return;
		}
		
		// problem musi byt v stave novy
		if ($problem->State != Problem::STATE_NEW) {
			$this->_helper->FlashMessenger->addMessage('Je mozne zavrhnut len novy problem!');
			$this->_redirect('/problem/view/Id/'.$problemId);
			return;
		}
		
		// formular
		$form = new Form_Decline($problemId);		
		if ($this->getRequest()->isPost()) {
			if ($form->isValid($_POST)) {
				
				// AKCEPTUJ
				$problemTable->getAdapter()->beginTransaction();
				
				// save 
				$problem->State = Problem::STATE_DELETED;
				$problem->Modified = new Zend_Db_Expr('NOW()');
				$problem->save();
				
				// save comment
				$problemComment = new ProblemComment();
				$comment = $problemComment->createRow();
				$comment->ProblemId		= $problemId;
				$comment->UserId		= $this->view->user->Id;
				$comment->Title			= $form->getValue('Title');
				$comment->Body			= $form->getValue('Body');
				$comment->Created		= new Zend_Db_Expr('NOW()');
				$comment->System		= 1;
				$comment->save();
				
				// commit & redirect
				$problemTable->getAdapter()->commit();
				$this->_helper->FlashMessenger->addMessage('Problem bol zavrhnuty.');
				$this->_redirect('/problem/view/Id/'.$problemId);
			}
		} else {
			$form->getElement('Title')->setValue('Problem has been rejected!');
			$form->getElement('Body')->setValue('Moderator rejected this problem.');
		}
		$this->view->form = $form;
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
	
	public function ajaxVoteInfoAction()
	{
		$problemId = $this->getRequest()->getParam('problemId');
		$voteInfo = new VoteInfo($problemId);
		$this->view->data = $voteInfo->toArray();
	}
}
?>