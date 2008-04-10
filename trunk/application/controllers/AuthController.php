<?php

class AuthController extends Colla_Controller_Action
{
    /**
     * Authentitace user action
     */
    public function loginAction()
    {
    	$form = new Form_Login();
    	
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
    					$this->_redirect('/');
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
     * Logout user, clear identity
     * 
     */
    public function logoutAction()
    {
    	$auth = Zend_Auth::getInstance();
    	
    	if ($auth->hasIdentity()) {
    		$auth->clearIdentity();
    		$this->_helper->FlashMessenger->addMessage('You have been logged out.');
    	} else {
    		$this->_helper->FlashMessenger->addMessage('You are allready logged out.');
    	}
    	$this->_redirect('/auth/login');
    }
    
    /**
     * Change profile of logged user
     *
     */
    public function profileAction()
    {
    	// check if he is logged in
    	if (!Zend_Auth::getInstance()->hasIdentity()) {
    		$this->_helper->FlashMessenger->addMessage('Please sign in first!');
    		$this->_redirect('/auth/login');
    	}
    	
    	// get form
    	// get informations 
    	$user = User::getByUsername(Zend_Auth::getInstance()->getIdentity());
    	$form = new Form_Profile();
    	
    	// action save !
    	if ($this->getRequest()->isPost()) {
    		if ($form->isValid($_POST)) {
    			$user->FullName = $form->getValue('FullName');
    			$user->save();
    			$this->_helper->FlashMessenger->addMessage($this->translate('Your profile has been changed.'));
    			$this->_redirect('/auth/profile');
       		}
    	} else {
			$form->getElement('FullName')->setValue($user->FullName);
		}
		
		// render
		$this->view->formProfile = $form;
    }
    
    /**
     * Change user password
     */
    public function passwordAction()
    {
    	// check if he is logged in
    	if (!Zend_Auth::getInstance()->hasIdentity()) {
    		$this->_helper->FlashMessenger->addMessage('Please sign in first!');
    		$this->_redirect('/auth/login');
    	}
    	
    	// get form
    	// get informations 
    	$user = User::getByUsername(Zend_Auth::getInstance()->getIdentity());
    	$form = new Form_Password();
    	
    	// action save !
    	if ($this->getRequest()->isPost()) {
    		if ($form->isValid($_POST)) {
    			if ($form->getValue('password1') == $form->getValue('password2')) {
    				$user->Password = md5($form->getValue('password1'));
	    			$user->save();
	    			$this->_helper->FlashMessenger->addMessage($this->translate('Your password has been changed.'));
	    			$this->_redirect('/auth/password');
    			} else {
    				$this->_helper->FlashMessenger->addMessage($this->translate('Both passwords need to be the same!'));
    				$this->_redirect('/auth/password');
    			}
       		}
    	}
    	
    	// render
		$this->view->form = $form;
    }
}
