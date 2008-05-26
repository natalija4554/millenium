<?php
class Comment extends Zend_Db_Table_Abstract
{
    protected $_name = 'comments';

    protected $_referenceMap = array(
    	'Problem' => array(
    		'columns'		=> array('ProblemId'),
    		'refTableClass' => 'Problem'
    	),
    	'Solution' => array(
    		'columns'		=> array('SolutionId'),
    		'refTableClass' => 'Solution'
    	)
    );
    
}