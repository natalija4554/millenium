<?php

/**
 * Formular na pridanie novej problemovej oblasti
 * 
 */
class Form_Category extends Zend_Form
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
		$this->setAction('/category/new');
		$this->setMethod('post');
		
		
		// Nazov
		$elm = new Zend_Form_Element_Text('Name');
		$elm->setRequired(true)
			->setAttrib('size', 60)
			->addValidator(new Zend_Validate_StringLength(1, 255))
			->setLabel('Názov kategórie');
		$this->addElement($elm);
		
		
		
		
		// submit button
		$elm = new Zend_Form_Element_Submit('createCategory');
		$elm->setLabel('Vytvoriť');
		$this->addElement($elm);
	}
	
	
}
?>
