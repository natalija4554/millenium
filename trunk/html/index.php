<?php
/**
 * Millenium project
 * 
 * @author Frantisek Durajka
 */
set_include_path('.' . PATH_SEPARATOR . '../../library');
require_once 'Zend/Controller/Front.php';

/**
 * Run the controller
 */
Zend_Controller_Front::run('../application/controllers');