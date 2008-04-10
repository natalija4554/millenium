<?php
class ProblemComment extends Zend_Db_Table_Abstract
{
    protected $_name = 'problemcomments';

    protected $_referenceMap = array(
    	'Problem' => array(
    		'columns'		=> array('ProblemId'),
    		'refTableClass' => 'Problem'
    	)
    );
    
}