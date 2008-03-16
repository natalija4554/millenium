<?php
/**
 * Controller pre problemove oblasti
 * 
 * 
 */
class ProblemAreaController extends Colla_Controller_Action
{
	/**
	 * Odhlasi pouzivatela zo systemu
	 */
    public function indexAction()
    {
    	$ProblemArea = new Colla_Model_ProblemArea();
    	$this->view->areas = $ProblemArea->getProblemAreas();
    	$this->render();
    }
    
    /**
     * Prida novu problemovu oblast
     */
    public function addAction()
    {
    	$form = new Colla_Form_ProblemArea();

    	// action save !
    	if ($this->getRequest()->isPost()) {
    		if ($form->isValid($_POST)) {
    			$ProblemArea = new Colla_Model_ProblemArea();
    			$ProblemArea->saveNew($form->getValues());
    			$this->_helper->FlashMessenger->addMessage($this->translate('Problem area has been created.'));
    			$this->_redirect('/problemarea/index');
       		}
    	}
    	$this->view->form = $form;
    	$this->view->messages = $this->_helper->FlashMessenger->getMessages();
    	$this->view->sidebar = 'text';
    }
    
    /**
     * Zobrazi podrobnosti o problemovej oblasti, zoznam problemov
     *
     */
    public function viewAction()
    {
    	// param ID
    	if (!$this->_hasParam('id')) {
    		$this->_helper->FlashMessenger->addMessage($this->translate('Please select problem area.'));
    		$this->_redirect('/problemarea/index');
    	}
    	
    	$PA = new Colla_Model_ProblemArea();
    	if (!$PA->hasProblemArea($this->_getParam('id'))) {
    		$this->_helper->FlashMessenger->addMessage($this->translate('No such problem area!'));
    		$this->_redirect('/problemarea/index');	
    	}
    	
    	$this->view->pa = $PA->getProblemArea($this->_getParam('id'));
    	$this->view->problems = $PA->getProblems($this->_getParam('id'));
    }
    
    public function addproblemAction()
    {
    	
    }
}
?>