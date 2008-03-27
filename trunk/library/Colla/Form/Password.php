<?php

/**
 * Formular na pridanie novej problemovej oblasti
 * 
 */
class Colla_Form_Password extends Zend_Form
{
	/**
	 * Construck ProblemArea Form class
	 *
	 * @param array $options
	 */
	public function __construct($options = null)
	{
		// parent construct
		parent::__construct($options);
		
		// form
		$this->setAction('/auth/password');
		$this->setMethod('post');
		
		// password1
		$elm = new Zend_Form_Element_Password('password1');
		$elm->setRequired(true)
			->setLabel('Password');
		$this->addElement($elm);
		
		// password1
		$elm = new Zend_Form_Element_Password('password2');
		$elm->setRequired(true)
			->setLabel('Password (retype)');
		$this->addElement($elm);
		
		// submit button
		$elm = new Zend_Form_Element_Submit('changePassword');
		$elm->setLabel('Change Password');
		$this->addElement($elm);
	}
}
?>
