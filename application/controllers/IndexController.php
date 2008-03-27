<?php

class IndexController extends Colla_Controller_Action 
{
 	public function indexAction()
    {
    	$this->_forward('view', 'problemarea');
    }

    public function deniedAction()
    {
        $this->render();
    }
}
?>