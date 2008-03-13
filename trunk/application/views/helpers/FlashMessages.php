<?php
/**
 * BaseUrl Helper
 *
 * @package Colla 
 */
class Zend_View_Helper_FlashMessages
{
	/**
	 * @var Zend_View_Interface
	 */
	public $view;
	
	/**
	 * Pouzivam objekt view
	 */
	public function setView(Zend_View_Interface $view) 
	{
		$this->view = $view;
	}
	
	/**
	 * render Flash Messages
	 * 
	 * - requires Flash messages to be stores in 'messages' variable in view
	 *  
	 * @return string
	 */
	public function FlashMessages()
	{
		// check if set
		if (!isset($this->view->messages) or !is_array($this->view->messages)) {
			return '';
		}
		
		// render
		$out = '<ul class="flashMessages">';
		foreach ($this->view->messages as $message) {
			$out .= '<li>'.$message.'</li>'; 
		}
		$out .= '</ul>';
		return $out;
	}
}
?>