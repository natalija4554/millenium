<?php
class Colla_Paginate_Iterator extends Zend_Paginate_Abstract 
{ 
    /**
     * The iterator
     *
     * @var Iterator
     */ 
    protected $_iterator = null; 
 
    /**
     * Constructor
     *
     * @var Iterator $iterator
     */ 
    public function __construct(Iterator $iterator) 
    { 
    } 
 
    /**
     * Get a page
     *
     * @var LimitIterator $page
     */ 
    public function getPage($page) 
    { 
    } 
} 