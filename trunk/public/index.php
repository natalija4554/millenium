<?php
/**
 * Colla Application
 * 
 * @category Colla
 * @package Colla_Application
 * @version $Id$
 */ 

/**
 * @see Zend_Loader 
 */
require_once '../library/Zend/Loader.php';

/**
 * @see Colla_App
 */
require_once '../library/Colla/App.php';
require_once '../library/Colla/Functions.php';

/** Run Application */
Colla_App::getInstance()->run();
?>