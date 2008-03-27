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
		$auth = Zend_Auth::getInstance();
		$view->authenticated = $auth->hasIdentity();
		if ($view->authenticated) {
			$view->user = Colla_Db_Table_User::getByUsername($auth->getIdentity());
		}	
	}
	
	public function postDispatch()
	{
		$view = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer')->view;	
		$view->messages 		= $this->_helper->FlashMessenger->getMessages();
		$view->baseUrl 			= Zend_Controller_Front::getInstance()->getBaseUrl();
		$view->actionName 		= $this->getRequest()->getActionName();
		$view->controllerName 	= $this->getRequest()->getControllerName();
		$view->hasProblemArea	= Colla_App::getInstance()->hasProblemArea();
		
		if ($view->hasProblemArea) {
			
			// fetch pa 
			$pa = new Colla_Db_Table_ProblemArea();
			$problemAra = $pa->find(Colla_App::getInstance()->getProblemArea());
			$view->problemAreaName = $problemAra->current()->Name;
		}
	}

	public function translate($word)
	{
		return Zend_Registry::get('Zend_Translate')->translate($word);
	}
}