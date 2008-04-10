<?php

/**
 * Formular na pridanie novej problemovej oblasti
 * 
 */
class Form_ProblemArea extends Zend_Form
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
		$this->setAction('/problemarea/add');
		$this->setMethod('post');
		
		// name
		$elm = new Zend_Form_Element_Text('Name');
		$elm->setRequired(true);
		$elm->setAttrib('size', 60);
		$elm->addValidator(new Zend_Validate_StringLength(4, 128));
		$elm->setLabel('Problem area name');
		$this->addElement($elm);
		
		// description
		$elm = new Zend_Form_Element_Textarea('Definition');
		$elm->setAttrib('rows', '5');
		$elm->setRequired(true);
		$elm->setLabel('Problem area description');
		$this->addElement($elm);
		
		// submit button
		$elm = new Zend_Form_Element_Submit('submit');
		$elm->setLabel('Add new problem area');
		$this->addElement($elm);
	}
}
?>