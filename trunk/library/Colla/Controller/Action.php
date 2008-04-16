<?php
/**
 * Zakladny controller aplikacie
 *
 */
class Colla_Controller_Action extends Zend_Controller_Action
{
	public function preDispatch()
	{
		// view object
		$view = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer')->view;
		
		// check login informations
		$auth = Zend_Auth::getInstance();
		$view->authenticated = $auth->hasIdentity();
		$view->user = $view->authenticated ? User::getByUsername($auth->getIdentity()) : null;
		
		// save informations into registry to gain access to them by Db_Table_Abstract
		Zend_Registry::set('Authenticated', $view->authenticated);
		Zend_Registry::set('User', $view->user);
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
			$pa = new ProblemArea();
			$problemAra = $pa->find(Colla_App::getInstance()->getProblemArea());
			$view->problemAreaName = $problemAra->current()->Name;
		}
	}

	public function translate($word)
	{
		return Zend_Registry::get('Zend_Translate')->translate($word);
	}
	
	/**
	 * Check if user is authenticated
	 *
	 */
	public function hasIdentity()
	{
		return Zend_Auth::getInstance()->hasIdentity();
	}
	protected function _getRole()
	{
		return $this->hasIdentity() ? Zend_Registry::get('User')->RoleId : 'guest';
	}
	
	/**
	 * Check if user has an granted permission
	 *
	 * @param unknown_type $resource
	 * @param unknown_type $privilege
	 */
	public function isAllowed($resource, $privilege)
	{
		return Zend_Registry::get('Colla_Acl')->isAllowed($this->_getRole(), $resource, $privilege);
	}
	
	public function checkAllowed($resource, $privilege) 
	{
		if (!$this->isAllowed($resource, $privilege)) {
			throw new Exception('You have no privilege('.$privilege.') to access this resource ('.$resource.')');
		}
	}
}