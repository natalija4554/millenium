<?php

class IndexController extends Colla_Controller_Action 
{
 	public function indexAction()
    {
    	$this->getRequest()->setParam('filter', 'none');
    	$this->_forward('list', 'problem');
    }

    public function deniedAction()
    {
        $this->render();
    }
}
?>