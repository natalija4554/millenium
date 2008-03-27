<?php
class Colla_Db_Table_User extends Zend_Db_Table_Abstract
{
    protected $_name = 'users';
    protected $_rowClass = 'Colla_Db_Table_Row_User';
    
    
    /**
     * Get information about user
     *
     * @param string $username
     * @return Zend_Db_Table_Row
     */
    static function getByUsername($username)
    {
    	$user = new Colla_Db_Table_User();
    	$row = $user->fetchRow($user->select()->where('Username = ?', $username));
     	if ($row == null) {
    		throw new Exception('Username not found!');
    	}
    	return $row;
    }
}
