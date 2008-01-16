<?php

class LoginController extends Colla_Controller_Action
{
	/**
	 * Session to store login informations
	 * 
	 */
    protected $_session;

    /**
     * Inicializuje session
     */
    public function init()
    {
    	// vytvori novy namespace pre class
        $this->_session = new Zend_Session_Namespace(__CLASS__);
    }

    /**
     * Zobrazi formular pre prihlasenie
     */
    public function indexAction()
    {
        if (isset($this->_session->messages)) {
            $this->view->messages = $this->_session->messages;
        }
        if (isset($this->_session->username)) {
            $this->view->username = $this->_session->username;
        }
        $this->render();
    }

    
    /**
     * Process login -> authenticate user
     */
    public function processAction()
    {
    	// validacia 
        $options = array(
            'missingMessage' => "Field '%field%' is required"
            );
        $filters = array(
            'username' => array('StringTrim', 'StringToLower')
            );
        $validators = array(
            'username' => array(
                'presence' => 'required',
                array('StringLength', 3, 32),
                'Alpha',
                'messages' => array(
                    'Musí byť medzi %min% a %max% znakmi dlné',
                    'Musí obsahovať iba alfa znaky',
                    ),
                'breakChainOnFailure' => true
                ),
            'password' => array(
                'presence' => 'required',
                array('StringLength', 5),
                'messages' => array(
                    0 => 'Musí byť minimálne %min% znakov dlhé'
                    )
                )
            );

		// validuj vstup
        $input = new Zend_Filter_Input($filters, $validators, $_POST, $options);
        if ($input->isValid()) {
            $authAdapter = new Zend_Auth_Adapter_DbTable(
                Colla_App::getInstance()->getDb(),
                'user',
                'username',
                'password',
                'MD5(?)'
                );

            $authAdapter->setIdentity($input->username)
                        ->setCredential($input->password);

            $auth = Zend_Auth::getInstance();
            $result = $auth->authenticate($authAdapter);
            if ($result->isValid()) {
                $auth->getStorage()->write($authAdapter->getResultRowObject(null, 'password'));
                unset($this->_session->messages, $this->_session->username);
                $this->_redirect('/');
            } else {
                switch ($result->getCode()) {
                    case Zend_Auth_Result::FAILURE_IDENTITY_NOT_FOUND:
                        $messages = array('username' => array('Neexistujuci pouzivatel'));
                        break;
                    case Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID:
                        $messages = array('password' => array('Nespravne heslo !'));
                        break;
                    default:
                        throw new Exception('Unsupported authentication failure code');
                        break;
                }
                $this->_session->messages = $messages;
                $this->_session->username = $_POST['username'];
                $this->_redirect('index');
            }

        } else {
            $this->_session->messages = $input->getMessages();
            if (isset($_POST['username'])) {
                $this->_session->username = $_POST['username'];
            }
            $this->_redirect('index');
        }
    }
}
