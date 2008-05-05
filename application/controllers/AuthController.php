<?php

class AuthController extends Colla_Controller_Action
{
    /**
     * Authentitace user action
     */
    public function loginAction()
    {
    	$form = new Form_Login();
    	$redirect = $this->getRequest()->getParam('redirect');
    	if (!$redirect) {
    		$redirect = '/auth/login';
    	}
    	$form->setRedirectParam($redirect);
    	
    	// action save !
    	if ($this->getRequest()->isPost()) {
    		
    		// authenticate
    		if ($form->isValid($_POST)) {
    			
    			// create adapter
    			$authAdapter = new Colla_Auth_Adapter(
    				$form->getValue('username'),
    				$form->getValue('password')
    			);
    						
    			// authenticate
    			$result = Zend_Auth::getInstance()->authenticate($authAdapter);
    			switch ($result->getCode()) {
    				// success
    				case Zend_Auth_Result::SUCCESS:
    					$this->_helper->FlashMessenger->addMessage('Boli ste úspešne prihlásený.');
    					$this->_redirect($form->getValue('redirect'));
    					break;
    				
    				case Colla_Auth_Result::FAILURE_NOTACTIVE:
    					$this->view->loginError = __('Prihlasovací účet nie je aktívny.');
    					break;
    				
    				case Colla_Auth_Result::FAILURE_NOTVERIFIED:
    					$this->view->loginError = __('Váš účet ešte nie je aktívny, nakoľko nebolo overená správnosť Vašej e-mailovej adresy.');
    					break;
    					
    				// failure
    				case Zend_Auth_Result::FAILURE:
    				case Zend_Auth_Result::FAILURE_IDENTITY_NOT_FOUND:
    				case Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID:
    					$this->view->loginError = __('Nesprávne prihlasovacie meno alebo heslo.');
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
    	$this->_redirect('/');
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
