<?php
class Validate_PasswordConfirmation extends Zend_Validate_Abstract 
{
	const NOT_MATCH = 'passwordsDoNotMatch';
	protected $_messageTemplates = array(
		self::NOT_MATCH => 'Zadané heslá sa nezhodujú!'
	);
	protected $_fieldtomatch;
	public function __construct($fieldtomatch) {
		$this->_fieldtomatch = (string)$fieldtomatch;
	}
	public function isValid($value, $context = null) {
		$value = (string) $value;
		$this->_setValue($value);
		
		if (!isset($context[$this->_fieldtomatch]) || $value != $context[$this->_fieldtomatch]) {
			$this->_error(self::NOT_MATCH);
			return false;
		}
		return true;
	}
}
?>