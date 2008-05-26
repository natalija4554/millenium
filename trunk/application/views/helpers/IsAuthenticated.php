<?php

/**
 * View helper, obsahuje metodu authAcces, ktora zistuje ci ma mom. prihlaseny pouzivatel 
 * prav no objekt so zadanymi parametrami. 
 *
 * @package Colla
 */
class Zend_View_Helper_IsAuthenticated 
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
	public function isAuthenticated()
	{
		return Zend_Auth::getInstance()->hasIdentity(); 
	}
}
?>