<?php
class SolutionAccept extends Zend_Db_Table_Abstract
{
	const VOTE_YES 		= 'Yes';
	const VOTE_NO 		= 'No';
	const VOTE_IGNORE	= 'Ignore';
	
    protected $_name = 'solution_accept';
    protected $_rowClass = 'Row_SolutionAccept';

}