<?php

/**
 * Edit profile
 * 
 */
class Colla_Form_Profile extends Zend_Form
{
	/**
	 * @param array $options
	 */
	public function __construct($options = null)
	{
		// parent construct
		parent::__construct($options);
		
		// form
		$this->setAction('/auth/profile');
		$this->setMethod('post');
		
		// name
		$elm = new Zend_Form_Element_Text('FullName');
		$elm->setRequired(true)
			->setAttrib('size', 60)
			->addValidator(new Zend_Validate_StringLength(6, 255))
			->setLabel('Full Name');
		$this->addElement($elm);
		
		// submit button
		$elm = new Zend_Form_Element_Submit('changeProfile');
		$elm->setLabel('Change profile');
		$this->addElement($elm);
	}
}
?>