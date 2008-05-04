<?php
class Validate_UserExists extends Zend_Validate_Abstract 
{
	const USER_EXITS = 'userAllreadyExists';
	protected $_messageTemplates = array(
		self::USER_EXITS => 'Zadaný používateľ už existuje!'
	);
	
	public function isValid($value, $context = null) {
		
		$value = (string) $value;
		$this->_setValue($value);
		$username = $value;
		
		$userTable = new User();
		$select = $userTable->select()->where(
			'Username = ?', $username
		);
		$rows = $userTable->fetchAll($select);
		if (count($rows) > 0) {
			$this->_error(self::USER_EXITS);
			return false;
		}
		return true;
	}
}
?>