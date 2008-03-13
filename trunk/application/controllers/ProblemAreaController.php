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
    	$problemArea = new Colla_Db_Table_ProblemArea();
    	$this->view->areas = $problemArea->fetchAll()->toArray();
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
    }
}
?>