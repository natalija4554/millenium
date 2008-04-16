<?php 
class Colla_Form extends Zend_Form
{
	public function __construct($options = null)
	{
		parent::__construct($options);
		$this->addAttribs(array('class'=>'std'));
	}
}
?>