<?php
/**
 * From zend classes in proposals
 *
 */
abstract class Colla_Paginate_Abstract implements Iterator, Countable 
{ 
    /**
     * Total number of pages
     *
     * @var int
     */ 
    protected $_pageCount = 0; 
 
    /**
     * Total number of items
     *
     * @var int
     */ 
    protected $_rowCount = 0; 
 
    /**
     * Amount of items per page
     *
     * @var int
     */ 
    protected $_rowLimit = 10; 
 
    /**
     * The current page nr
     *
     * @var int
     */ 
    protected $_currentPage = 1; 
 
    /**
     * Page counter for the iterator
     *
     * @var int
     */ 
    protected $_iteratorPage = 1; 
 
    /**
     * Get the current page nr.
     *
     * @return int
     */ 
    public function current() 
    { 
    } 
 
    /**
     * Get the iterator key
     *
     * @return int
     */ 
    public function key() 
    { 
    } 
 
    /**
     * Get the next page nr
     */ 
    public function next() 
    { 
    } 
 
    /**
     * Rewind to the first page
     */ 
    public function rewind() 
    { 
    } 
 
    /**
     * Check if there's a next page
     *
     * @return boolean
     */ 
    public function valid() 
    { 
    } 
 
    /**
     * Get the number of pages
     *
     * @return int
     */ 
    public function count() 
    { 
    } 
 
    /**
     * Check if the page number exists
     *
     * @param int $page
     * @return boolean
     */ 
    public function hasPageNumber($page) 
    { 
    } 
 
    /**
     * Check if there are pages available
     *
     * @return boolean
     */ 
    public function hasPages() 
    { 
    } 
 
    /**
     * Check if there is a next page
     *
     * @return boolean
     */ 
    public function hasNext() 
    { 
    } 
 
    /**
     * Check if there is a previous page
     *
     * @return boolean
     */ 
    public function hasPrevious() 
    { 
    } 
 
    /**
     * Get item nr
     *
     * @param int $itemNumber
     * @param int $pageNumber
     * @return int
     */ 
    public function getItemNumber($item, $page = null) 
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
 
    /**
     * Get the amount of rows
     *
     * @return int
     */ 
    public function getRowCount() 
    { 
    } 
 
    /**
     * Get amount of items per page
     *
     * @return int
     */ 
    public function getRowLimit() 
    { 
    } 
 
    /**
     * Set the amount of items per page
     *
     * @param int $limit
     * @return Zend_Paginate_Abstract
     */ 
    public function setRowLimit($limit) 
    { 
    } 
 
    /**
     * Set current page nr
     *
     * @param int $page
     * @return Zend_Paginate_Abstract
     */ 
    public function setCurrentPageNumber($page) 
    { 
    } 
 
    /**
     * Get the current page nr
     *
     * @return int
     */ 
    public function getCurrentPageNumber() 
    { 
    } 
 
    /**
     * Check if the given number is the current page nr
     *
     * @param int $number
     * @return boolean
     */ 
    public function isCurrentPageNumber($number) 
    { 
    } 
 
    /**
     * Get the current page
     *
     * @return Zend_Db_Table_Rowset_Abstract|array
     */ 
    public function getCurrentPage() 
    { 
    } 
 
    /**
     * Get the next page
     *
     * @throws Zend_Paginate_Exception_NoNextPage
     * @return Zend_Db_Table_Rowset_Abstract|array
     */ 
    public function getNextPage() 
    { 
    } 
 
    /**
     * Get the next page number
     *
     * @return int
     */ 
    public function getNextPageNumber() 
    { 
    } 
 
    /**
     * Get the previous page
     *
     * @throws Zend_Paginate_Exception_NoPreviousPage
     * @return Zend_Db_Table_Rowset_Abstract|array
     */ 
    public function getPreviousPage() 
    { 
    } 
 
    /**
     * Get previous page nr
     *
     * @return int
     */ 
    public function getPreviousPageNumber() 
    { 
    } 
 
    /**
     * Get a page
     *
     * @var int $page
     */ 
    abstract public function getPage($page); 
} 