<?php

class ErrorController extends Zend_Controller_Action 
{
	function errorAction()
	{
		$errors = $this->_getParam('error_handler');
		
		switch ($errors->type) {
			case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
			case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
				$this->getResponse()->setRawHeader('HTTP/1.1 404 Not Found');
				$this->render('404');
				break;
			default: 
				$this->view->assign('message', $errors->exception->getMessage());
				$this->render('500');
				break;
		}
	}
}
?>