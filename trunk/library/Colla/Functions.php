<?php
/**
 * Translation ;-)
 *
 * @param unknown_type $string
 * @return unknown
 */
function __($string) {
	return Zend_Registry::get('Zend_Translate')->translate($string);
}

?>