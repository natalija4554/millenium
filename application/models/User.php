<?php
class User extends Zend_Db_Table_Abstract
{
    protected $_name = 'users';
    protected $_rowClass = 'Row_User';
    
    
    /**
     * Get information about user
     *
     * @param string $username
     * @return Zend_Db_Table_Row
     */
    static function getByUsername($username)
    {
    	$user = new User();
    	$row = $user->fetchRow($user->select()->where('Username = ?', $username));
     	if ($row == null) {
    		throw new Exception('Username not found!');
    	}
    	return $row;
    }
}
