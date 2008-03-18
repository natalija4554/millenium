<?php
/**
 * Zakladny controller aplikacie
 *
 */
class Colla_Controller_Action extends Zend_Controller_Action
{
	public function preDispatch()
	{
		$view = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer')->view;
		
		// auth 
		$auth = Zend_Auth::getInstance();
		
		// set predefined values
		$view->authenticated = $auth->hasIdentity();
		// $view->user = Colla_Db_Table_User::getUserByName($auth->getIdentity());
		$view->messages = $this->_helper->FlashMessenger->getMessages();
		$view->baseUrl = Zend_Controller_Front::getInstance()->getBaseUrl();
	}

	public function translate($word)
	{
		return Zend_Registry::get('Zend_Translate')->translate($word);
	}
}