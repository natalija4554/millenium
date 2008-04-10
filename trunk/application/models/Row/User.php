<?php

class Row_User extends Zend_Db_Table_Row_Abstract 
{
	/**
	 * Get full name of a user
	 *
	 * @return string
	 */
	public function getFullname()
	{
		return $this->FullName;		
	}
}

?>