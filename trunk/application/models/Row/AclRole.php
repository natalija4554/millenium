<?php

class Row_AclRole extends Zend_Db_Table_Row_Abstract 
{
	
	/**
	 * Grant permission to new resource
	 *
	 * @param Row_AclResource $resource
	 */
	public function addResource(Row_AclResource $resource)
	{
		$row = $this->_getAclRow($resource);
		$row->allow = 1;
		$row->save();
	}
	
	/**
	 * Remove permission to resource
	 *
	 * @param Row_AclResource $resource
	 */
	public function removeResource(Row_AclResource $resource)
	{
		$row = $this->_getAclRow($resource);
		$row->allow = 0;
		$row->save();
	}
	
	/**
	 * Return coresponding row if exists, or new one if doesn't
	 *
	 * @param Row_AclResource $resource
	 * @return Zend_Db_Table_Row_Abstract
	 */
	protected function _getAclRow($resource)
	{
		$access = new AclAccess();
		$rows = $access->fetchAll($access->select()
			->where('role_id = ?', $this->id)
			->where('resource_id = ?', $resource->id)
			->where('privilege = ?', $resource->privilege
		)); 
		$row = count($rows) ? 
			$rows->current() : 
			$access->createRow(array(
				'role_id' => $this->id,
				'resource_id' => $resource->id,
				'privilege' => $resource->privilege	
			));
		return $row;
	}
}

?>