<?php

class Row_AclResource extends Zend_Db_Table_Row_Abstract 
{
	public function isAllowedBy($role)
	{
		$access = new AclAccess();
		$select = $access->select()
			->where('role_id = ?', $role)
			->where('resource_id = ?', $this->id)
			->where('privilege = ?', $this->privilege)
			->where('allow = 1');
		$rows = $access->fetchAll($select);
		return (count($rows) > 0) ? true : false;	
	}
}

?>