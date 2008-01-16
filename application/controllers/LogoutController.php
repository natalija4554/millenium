<?php

class LogoutController extends Colla_Controller_Action
{
	/**
	 * Odhlasi pouzivatela zo systemu
	 */
    public function indexAction()
    {
        Zend_Auth::getInstance()->clearIdentity();
        $this->_redirect('/');
    }
}
?>