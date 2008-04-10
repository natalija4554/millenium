<?php
class Colla_Db_Table_Abstract extends Zend_Db_Table_Abstract 
{
	/**
	 * Return a array which contains only values which keys are required
	 * 
	 * @throw exception if key wasn't found
	 * @return string[]
	 */
	protected function _filterArray($values, $required_fields)
	{
		$data = array();
		foreach ($required_fields as $key) {
			if (!array_key_exists($key, $values)) {
				throw new Exception('Value `'.$key.'` not found!');
			}
			$data[$key] = $values[$key];
		}
		return $data;
	}
	
	/**
	 * Get the ID of user who is looged 
	 *
	 */
	protected function _getLogedUserId()
	{
		// check if user is authenticated
		if (!Zend_Registry::get('Authenticated')) {
			throw new Exception('User not authenticated!');
		}
		$user =  Zend_Registry::get('User');
		return $user->Id;
	}
}
?>