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
		$problemTable = new Colla_Db_Table_Problem();
		$problem = $problemTable->findProblem($Id);
		$this->view->problem = $problem;
		
		// render depending on problem state
		switch ($this->view->problem->State) {
			case 'NEW':
				$this->render('viewNew');
				break;
			case 'APPROVED':
				$this->render('viewApproved');
				break;
			case 'SOLVED':
				$this->render('viewSolved');
				break;
			case 'CLOSED':
				$this->render('viewClosed');
				break;
			case 'DELETED':
			default: 
				$this->render('viewDeleted');
				break;
		}
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
		$form = new Colla_Form_ProblemChange($Id);
		$problemTable = new Colla_Db_Table_Problem();
		if ($this->getRequest()->isPost()) {
			if ($form->isValid($_POST)) {
				$problemTable->changeDefinition($Id, $form->getValues());	
			}			
		} else {
			$problem = $problemTable->findProblem($Id);
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
		
		$form = new Colla_Form_ProblemComment($problemId);
		if ($this->getRequest()->isPost()) {
			if ($form->isValid($_POST)) {
				// posli komentar
				$commentTable = new Colla_Db_Table_ProblemComment();
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
}
?>