<?php
/**
 * Zakladny controller aplikacie
 *
 */
class Colla_Controller_Action extends Zend_Controller_Action
{
	public function preDispatch()
	{
		// retrive view 
		$view = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer')->view;
		
		// set predefined values
		
		
		// print_r($this->_helper->FlashMessenger->getMessages());
		$view->assign('messages', $this->_helper->FlashMessenger->getMessages());
	}

	public function translate($word)
	{
		return Zend_Registry::get('Zend_Translate')->translate($word);
	}
}