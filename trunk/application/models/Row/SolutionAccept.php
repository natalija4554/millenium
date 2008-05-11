<?php
class Row_SolutionAccept extends Zend_Db_Table_Row_Abstract 
{
	/**
	 * Return true if allready voted, false if not
	 *
	 * @return bool
	 */
	public function voted()
	{
		// ak je pripojeny a su nastavne udaje 
		if ($this->isConnected() && $this->Vote) {
			return true; 
		}
		return false;
	}
}