<?php
class Colla_Auth_Result extends Zend_Auth_Result 
{
	/**
     * User e-mail address is not verified
     */
    const FAILURE_NOTVERIFIED = -5;
    
    
    /**
     * User Account is not active.
     */
    const FAILURE_NOTACTIVE = -6;
    
    
	/**
     * Sets the result code, identity, and failure messages
     *
     * @param  int     $code
     * @param  mixed   $identity
     * @param  array   $messages
     * @return void
     */
    public function __construct($code, $identity, array $messages = array())
    {
        $code = (int) $code;

        if ($code < self::FAILURE_NOTACTIVE) {
            $code = self::FAILURE;
        } elseif ($code > self::SUCCESS ) {
            $code = 1;
        }
        
        $this->_code     = $code;
        $this->_identity = $identity;
        $this->_messages = $messages;
    }
    
}
?>