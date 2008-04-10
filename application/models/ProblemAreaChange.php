<?php
class ProblemAreaChange extends Zend_Db_Table_Abstract
{
    protected $_name = 'problemareachanges';   
    protected $_dependentTables = array('ProblemArea');
    protected $_referenceMap    = array(
        'ProblemArea' => array(
            'columns'           => 'ProblemAreaId',
            'refTableClass'     => 'ProblemArea',
            'refColumns'        => 'Id'
        ),
    );
    
}
