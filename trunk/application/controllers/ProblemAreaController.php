<?php
/**
 * Controller pre problemove oblasti
 * 
 * 
 */
class ProblemAreaController extends Colla_Controller_Action
{
/**
     * View problem Area details
     */
    public function viewAction()
    {
    	// param ID
    	$ProblemAreaId = Colla_App::getInstance()->getProblemArea();
    	$PA = new Colla_Db_Table_ProblemArea();
		$this->view->pa = $PA->getProblemArea($ProblemAreaId);
    	
    }
    
    public function problemsAction()
    {
    	$ProblemAreaId = Colla_App::getInstance()->getProblemArea();
    	$PA = new Colla_Db_Table_ProblemArea();
    	$this->view->problems = $PA->getProblems($ProblemAreaId);
    }
    
	/**
     * Add new problem area
     */
    public function addAction()
    {
    	// check if user is logged in
    	if (!$this->hasIdentity()) {
    		$this->_helper->FlashMessenger->addMessage('You need to be logged in to perform actions.');
    		$this->_redirect('/auth/login');
    		return;
    	}
    	
    	// new form
    	$form = new Colla_Form_ProblemArea();
    	if ($this->getRequest()->isPost()) {
    		if ($form->isValid($_POST)) {
    			$ProblemArea = new Colla_Db_Table_ProblemArea();
    			$ProblemArea->saveNew($form->getValues());
    			$this->_helper->FlashMessenger->addMessage($this->translate('Problem area has been created.'));
    			$this->_redirect('/problemarea/index');
       		}
    	}
    	$this->view->form = $form;
    }
    
    /**
     * Add new problem to problem area
     */
    public function addproblemAction()
    {
	    // check if user is logged in
    	if (!$this->hasIdentity()) {
    		$this->_helper->FlashMessenger->addMessage('You need to be logged in to perform actions.');
    		$this->_redirect('/auth/login');
    		return;
    	}
    	
    	// ID for the new problem
    	$ProblemAreaId = Colla_App::getInstance()->getProblemArea();
    	$form = new Colla_Form_Problem();
    	if ($this->getRequest()->isPost()) {
    		if ($form->isValid($_POST)) {
    			$data = array();
    			$data = $form->getValues();
    			$data['CreatedBy'] = $this->view->user->Id;
    			$data['ProblemAreaId'] = $ProblemAreaId;
    			$problem = new Colla_Db_Table_Problem();
				$problem->createNew($data);
				$this->_helper->FlashMessenger->addMessage('Problém bol vytvorený');
				$this->_redirect('/problemarea/view');
				return;
    		}
    	}
    	$this->view->form = $form;
    }
    
    /**
     * Let you to choose the default problem Area
     * 
     * @todo select screen when there is more or none problem areas
     */
    public function selectAction()
    {
    	$ProblemArea = new Colla_Db_Table_ProblemArea();
    	$this->view->areas = $ProblemArea->getProblemAreas();
    }
}
?>