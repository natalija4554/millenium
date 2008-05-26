<?php
class Zend_Paginate_DbTable extends Zend_Paginate_Abstract 
{ 
    /**
     * The name for the rowcount column
     * 
     */ 
    const ROW_COUNT_COLUMN = 'colla_paginate_row_count'; 
 
    /**
     * @var Zend_Db_Table_Abstract
     */ 
    protected $_table = null; 
 
    /**
     * @var Zend_Db_Table_Select
     */ 
    protected $_select = null; 
 
    /**
     * Constructor
     *
     * @param Zend_Db_Table_Abstract $table
     * @param Zend_Db_Table_Select $select
     */ 
    public function __construct(Zend_Db_Table_Abstract $table, Zend_Db_Table_Select $select) 
    { 
    } 
 
    /**
     * Get a page
     *
     * @var int $page
     * @return Zend_Db_Table_Rowset_Abstract
     */ 
    public function getPage($page) 
    { 
    } 
 
    /**
     * Get the total amount of pages
     *
     * @return int
     */ 
    public function getPageCount() 
    { 
    } 
} 