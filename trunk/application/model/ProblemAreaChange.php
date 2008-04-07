<?php
class Colla_Db_Table_ProblemAreaChange extends Zend_Db_Table_Abstract
{
    protected $_name = 'problemareachanges';   
    protected $_dependentTables = array('Colla_Db_Table_ProblemArea');
    protected $_referenceMap    = array(
        'ProblemArea' => array(
            'columns'           => 'ProblemAreaId',
            'refTableClass'     => 'Colla_Db_Table_ProblemArea',
            'refColumns'        => 'Id'
        ),
    );
    
}
