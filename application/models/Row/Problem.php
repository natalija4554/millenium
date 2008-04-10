<?php

class Row_Problem extends Zend_Db_Table_Row_Abstract 
{
	/**
	 * Vrati vsetky komentare k problemu
	 *
	 * @todo Zend_Db_Table_Rowset
	 */
	public function getComments()
	{
		// check if comment is loaded
		if (!$this->isConnected()) {
			throw new Exception('Not connected !');
		}
		
		// 
	}
}

?>