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
    	$this->view->problems = $PA->getProblems($ProblemAreaId);
    }
    
	/**
     * Add new problem area
     */
    public function addAction()
    {
    	$form = new Colla_Form_ProblemArea();

    	// action save !
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
    	Colla_App::getInstance()->setProblemArea($this->_getParam('id'));
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