<?php

class BoxController extends Colla_Controller_Action
{
	/**
     * Show partial login box in navigation menu
     */
	public function partialLoginBoxAction()
    {
    	$this->view->redirect = $this->getRequest()->getParam('redirect');
    }
	
	/**
     * Show partial login box in navigation menu
     */
	public function partialStatusBoxAction()
    {
    	$problemTable = new Problem();
    	$select = $problemTable->select();
    	$select->from($problemTable, array('Id', 'State'));
    	
    	// Vsetkych 
    	$rows = $problemTable->fetchAll($select->where('State != ?', 'DELETED'));
    	$this->view->countAll = count($rows);
    	
    	// Novych
    	$rows = $problemTable->fetchAll($select->where('State = ?', 'NEW'));
    	$this->view->countNew = count($rows);
    	
    	// Akceptovanych
    	$rows = $problemTable->fetchAll($select->where('State = ?', 'APPROVED'));
    	$this->view->countApproved = count($rows);
    	
    	// Novych
    	$rows = $problemTable->fetchAll($select->where('State = ?', 'SOLVED'));
    	$this->view->countSolved = count($rows);
    	
    	// Novych
    	$rows = $problemTable->fetchAll($select->where('State = ?', 'CLOSED'));
    	$this->view->countClosed = count($rows);
    }
    
    public function partialCategoryBoxAction()
    {
    	
    }
}
?>