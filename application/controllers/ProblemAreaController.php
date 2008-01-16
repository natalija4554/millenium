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
    	
    }
}
?>