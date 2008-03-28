<?php
/**
 * Controller pre problemy
 * 
 * 
 */
class ProblemController extends Colla_Controller_Action
{
	public function viewAction()
	{
		// param ID
		if (!($Id = $this->getRequest()->getParam('Id', null))) {
			$this->_helper->FlashMessenger->addMessage('Please Select a Problem');
			$this->_redirect('/problemarea/problems');
			return;
		}
		
		// get the 
		$problemTable = new Colla_Db_Table_Problem();
		$problem = $problemTable->findProblem($Id);
		$this->view->problem = $problem;
	}
	public function changecategoryAction()
	{
		
	}
	
	public function changedefintionAction()
	{
		
	}
	
	public function removeAction()
	{
		
	}
	
	public function mergeAction()
	{
		
	}
}
?>