<?php
/**
 * Controller pre problemove oblasti
 * 
 * 
 */
class ProblemareaController extends Colla_Controller_Action
{
/**
     * View problem Area details
     */
    public function viewAction()
    {
    	// param ID
    	$ProblemAreaId = Colla_App::getInstance()->getProblemArea();
    	$PA = new ProblemArea();
    	$problemArea = $PA->getProblemArea($ProblemAreaId);
    	
    	$this->view->pa = $problemArea;
		$this->view->changes = $problemArea->getChanges();
    }
    
    public function problemsAction()
    {
    	$this->view->CategoryId = $this->getRequest()->getParam('CategoryId');
    	
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
    	$form = new Form_ProblemArea();
    	if ($this->getRequest()->isPost()) {
    		if ($form->isValid($_POST)) {
    			$ProblemArea = new ProblemArea();
    			$data = $form->getValues();
    			$data['CreatedBy'] = $this->view->user->Id;
    			$ProblemArea->saveNew($data);
    			$this->_helper->FlashMessenger->addMessage($this->translate('Problem area has been created.'));
    			$this->_redirect('/problemarea/view');
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
    	$form = new Form_Problem();
    	if ($this->getRequest()->isPost()) {
    		if ($form->isValid($_POST)) {
    			$data = array();
    			$data = $form->getValues();
    			$data['CreatedBy'] = $this->view->user->Id;
    			$data['ProblemAreaId'] = $ProblemAreaId;
    			$problem = new Problem();
				$problem->createNew($data);
				$this->_helper->FlashMessenger->addMessage('Problém bol vytvorený');
				$this->_redirect('/problemarea/problems');
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
    	$ProblemArea = new ProblemArea();
    	$this->view->areas = $ProblemArea->getProblemAreas();
    }
    
    /**
     * Change the definition of the problem Area
     * 
     * @todo logovanie ?
     */
    public function changeAction()
    {
	    // param Id is required
		$problemAreaId = Colla_App::getInstance()->getProblemArea();	
		$form = new Form_ProblemAreaChange($problemAreaId);
		$problemAreaTable = new ProblemArea();
		if ($this->getRequest()->isPost()) {
			if ($form->isValid($_POST)) {
				$problemAreaTable->changeDefinition($problemAreaId, $form->getValues());
				$this->_helper->FlashMessenger->addMessage('Problem Area has been changed.');
    			$this->_redirect('/problemarea/view');
			}			
		} else {
			// fill values
			$problemArea = $problemAreaTable->getProblemArea($problemAreaId);
			$form->getElement('Name')->setValue($problemArea->Name);
			$form->getElement('Definition')->setValue($problemArea->Definition);
		}
		
		$this->view->form = $form;
    }
    
    /**
     * Vrati zoznam problemov
     * 
     * @todo refactor !
     */
    public function ajaxProblemsAction()
    {
    	// limiting
    	$start = $this->getRequest()->getParam('start');
    	$limit = $this->getRequest()->getParam('limit');
    	$status = $this->getRequest()->getParam('status');
    	$text = $this->getRequest()->getParam('text');
    	$sort = $this->getRequest()->getParam('sort');
    	$dir = $this->getRequest()->getParam('dir');
    	$category = $this->getRequest()->getParam('category');
    	
    	
    	// default problem area
    	$ProblemAreaId = Colla_App::getInstance()->getProblemArea();
    	$problemTable = new Problem();
    	$select = $problemTable->select()
    		->setIntegrityCheck(false)
    		->from('problems')
    		->where('problems.State != ?', 'DELETED')
    		->where('problems.ProblemAreaId = ?', $ProblemAreaId)
    		->join('users', 'users.Id = problems.CreatedBy', array('Username', 'FullName'));
    		
    	$select->limit($limit, $start);
    		
    	// status
    	if ($status != '') {
    		$select->where('problems.State = ?', $status);
    	}
    	if ($text != '') {
    		$select->where("problems.Name LIKE '%".addslashes($text)."%'");
    	}	
    	if ($category != '') {
    		$select->where("problems.CategoryId = ?", $category);
    	}
    	$problems = $problemTable->fetchAll($select);
    	$problemsArray = $problems->toArray();
		foreach ($problemsArray as $key => $problem) {
			
			// get the row and find out category
			$row = $problems->getRow($key);
			$problemsArray[$key]['FullCategoryName'] = $row->getFullCategoryName();
    	}
    	
    	
    	// get total count
    	$select = $problemTable->select();
    	$select->from('problems', array('Id'));
    	$select->where('ProblemAreaId = ?', $ProblemAreaId);
    	$select->where('problems.State != ?', 'DELETED');
    	if ($status != '') {
    		$select->where('problems.State = ?', $status);
    	}
    	if ($text != '') {
    		$select->where("problems.Name LIKE '%".addslashes($text)."%'");
    	}
    	if ($category != '') {
    		$select->where("problems.CategoryId = ?", $category);
    	}
    	$all = $problemTable->fetchAll($select);
    	
    	// data
    	$this->view->data = array(
    		'total' => count($all), 
    		'rows' => $problemsArray,
    		'info' => $_POST
    	);
    	
    	// preparse data object
    	foreach ($this->view->data['rows'] as &$row) {
    		$date = new Zend_Date($row['Created']);
    		$row['Created'] = $date->toString();
    	}
    }
    
 	
}
?>