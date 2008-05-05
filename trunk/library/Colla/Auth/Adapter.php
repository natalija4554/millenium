<?php

class Colla_Auth_Adapter  implements Zend_Auth_Adapter_Interface
{
	/**
     * $_identity - Identity value
     *
     * @var string
     */
    protected $_username = null;

    /**
     * $_credential - Credential values
     *
     * @var string
     */
    protected $_password = null;
	
	
	/**
     * Sets username and password for authentication
     *
     * @return void
     */
    public function __construct($username, $password)
    {
    	$this->_username 	= $username;
    	$this->_password 	= $password;	
    }

	/**
     * Performs an authentication attempt
     *
     * @throws Zend_Auth_Adapter_Exception If authentication cannot be performed
     * @return Zend_Auth_Result
     */
    public function authenticate()
    {
    	// select
    	$userTable = new User();
    	$rows = $userTable->fetchAll($userTable->select()->where(
    		'Username = ?', $this->_username
    	));
    	
    	// identity not found
    	if (count($rows) == 0) {
    		return new Colla_Auth_Result(
    			Colla_Auth_Result::FAILURE_IDENTITY_NOT_FOUND, 
    			$this->_username, 
    			array()
    		);
    	}
    	
    	// identity is ambiguous
    	if (count($rows) > 1) {
    		return new Colla_Auth_Result(
    			Colla_Auth_Result::FAILURE_IDENTITY_AMBIGUOUS, 
    			$this->_username, 
    			array()
    		);
    	}
    	
    	// check username
    	$user = $rows->current();
    	if ($user->Password != md5($this->_password)) {
    		return new Colla_Auth_Result(
    			Colla_Auth_Result::FAILURE, 
    			$this->_username, 
    			array()
    		);
    	}
    	
    	// user is not disabled
    	if (!$user->Active) {
    		return new Colla_Auth_Result(
    			Colla_Auth_Result::FAILURE_NOTACTIVE, 
    			$this->_username, 
    			array()
    		);
    	}
    	
    	// e-mail not verified
    	if (!$user->Verified) {
    		return new Colla_Auth_Result(
    			Colla_Auth_Result::FAILURE_NOTVERIFIED, 
    			$this->_username, 
    			array()
    		);
    	}
    	
    	// success
    	return new Colla_Auth_Result(
    		Colla_Auth_Result::SUCCESS, 
    		$this->_username, 
    		array()
    	);
    }
}
?>