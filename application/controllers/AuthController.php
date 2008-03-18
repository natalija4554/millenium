<?php

class AuthController extends Colla_Controller_Action
{
    /**
     * Authentitace user action
     */
    public function loginAction()
    {
    	$form = new Colla_Form_Login();
    	
    	// action save !
    	if ($this->getRequest()->isPost()) {
    		
    		// authenticate
    		if ($form->isValid($_POST)) {
    			
    			// create adapter
    			$authAdapter = new Zend_Auth_Adapter_DbTable(
    					Zend_Db_Table_Abstract::getDefaultAdapter(),
    					'users',
    					'username',
    					'password',
    					'MD5(?)'
    				);
    				
    			// fill adapter
    			$authAdapter->setIdentity($form->getValue('username'))
    						->setCredential($form->getValue('password'));
    						
    			// authenticate
    			$result = Zend_Auth::getInstance()->authenticate($authAdapter);
    			switch ($result->getCode()) {
    				// success
    				case Zend_Auth_Result::SUCCESS:
    					$this->_helper->FlashMessenger->addMessage('You have been successfully logged in.');
    					$this->_redirect('/auth/login');
    					break;
    					
    				// failure
    				case Zend_Auth_Result::FAILURE:
    				case Zend_Auth_Result::FAILURE_IDENTITY_NOT_FOUND:
    				case Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID:
    					$this->_helper->FlashMessenger->addMessage('Invalid username or password.');
    					$this->_redirect('/auth/login');
    					break;
    					
    				// exception
    				default:
    					throw new Exception('Authentification failure!');	
    			}
       		}
    	}
    	$this->view->form = $form;
    }
    
    /**
     * Show partial login box in navigation menu
     */
	public function partialLoginBoxAction()
    {
    	;
    }
}
