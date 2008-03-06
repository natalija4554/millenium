<?php
/**
 * BaseUrl Helper
 *
 * @package Colla 
 */
class Zend_View_Helper_BaseUrl
{
	/**
	 * Get baseURL appended with filename if set 
	 *
	 * @param string $filename
	 * @return string
	 */
	public function baseUrl($filename = null)
	{
		$baseUrl = Zend_Controller_Front::getInstance()->getBaseUrl();
		if ($filename !== null) {
			$baseUrl .= '/' . trim($filename, '/\\');
		}
		return $baseUrl;
	}
}
?>