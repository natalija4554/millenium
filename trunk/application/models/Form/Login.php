<?php

/**
 * Formular na pridanie novej problemovej oblasti
 * 
 */
class Form_Login extends Zend_Form
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
		$this->setAction('/auth/login');
		$this->setMethod('post');
		
		// name
		$elm = new Zend_Form_Element_Text('username');
		$elm->setRequired(true)
			->addFilter(new Zend_Filter_StringTrim())
			->addFilter(new Zend_Filter_StringToLower())
			->addValidator(new Zend_Validate_StringLength(3, 32))
			->setLabel('Username');
		$this->addElement($elm);
		
		// description
		$elm = new Zend_Form_Element_Password('password');
		$elm->setRequired(true)
			->setLabel('Password');
		$this->addElement($elm);
		
		// submit button
		$elm = new Zend_Form_Element_Submit('submit');
		$elm->setLabel('Log in');
		$this->addElement($elm);
	}
}
?>
