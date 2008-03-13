<?php

/**
 * Model pre colla
 *
 */
class Colla_Model_ProblemArea extends Colla_Model_Abstract
{
	/**
	 * Protected
	 *
	 * @var Colla_Db_Table_ProblemArea
	 */
	protected $_table;
	
	
	/**
	 * Initialize db connection with problem area table
	 */
	public function __construct()
	{
		$this->_table = new Colla_Db_Table_ProblemArea();
	}
	
	/**
	 * Save problematic area into database
	 * 
	 * @param array $values data to save
	 * @todo save CreatedBy
	 */
	public function saveNew($values)
	{
		// required fields
		$required_fields = array(
			'name',
			'description'
		);
		
		// filter and check input array
		$data = array();
		foreach ($required_fields as $key) {
			if (!array_key_exists($key, $values)) {
				throw new Exception('Value '.$key.' not found!');
			}
			$data[$key] = $values[$key];
		}
		
		// extra fields to save
		$data['Created'] = new Zend_Db_Expr('NOW()');
		
		// save data
		$this->_table->insert($data);
	}
}
?>