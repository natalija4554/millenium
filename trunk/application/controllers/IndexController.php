<?php

class IndexController extends Colla_Controller_Action 
{
 	public function indexAction()
    {
    	$this->_forward('problems', 'problemarea');
    }

    public function deniedAction()
    {
        $this->render();
    }
}
?>