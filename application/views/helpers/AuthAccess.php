<?php

/**
 * View helper, obsahuje metodu authAcces, ktora zistuje ci ma mom. prihlaseny pouzivatel 
 * prav no objekt so zadanymi parametrami. 
 *
 * @package Colla
 */
class Zend_View_Helper_AuthAccess 
{
	/**
	 * @var Zend_View_Interface
	 */
	public $view;
	
	/**
	 * Pouzivam objekt view
	 */
	public function setView(Zend_View_Interface $view) 
	{
		$this->view = $view;
	}

	/**
	 * Zisti ci ma aktualne prihlaseny pouzivatel pravo na zadany 
	 *  - resource 	: controller
	 *  - privilege : action 
	 */
	public function authAccess($resource, $privilege = null)
	{
		// ak nie je prihlaseny
		if (!Zend_Auth::getInstance()->hasIdentity()) {
			return false;
		}
		
		// skontrolujem identitu 
		$username = Zend_Auth::getInstance()->getIdentity()->username;
	
		// pravo 
		$acl = Colla_App::getInstance()->getAcl();
		
		 // ak neexistuje takyto objekt (resource) v acl
        if (!$acl->has($resource)) {
            throw new Exception('Sorry, the requested controller '.$resource.' does not exist as an ACL resource');
        }
		
		// zisti ci ma pravo
		if ($acl->isAllowed($username, $resource, $privilege)) {
			return true;
		}
		return false;
	}
}
?>