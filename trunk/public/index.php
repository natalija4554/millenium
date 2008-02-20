<?php
/**
 * Colla Application
 * 
 * @category Colla
 * @package Colla_Application
 * @version $Id$
 */
// require Zend Loader & Application
require_once '../library/Zend/Loader.php';
require_once '../library/Colla/App.php';

// run application
Colla_App::getInstance()->run();
?>