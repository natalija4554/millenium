<?php

/**
 * Formular na pridanie novej problemovej oblasti
 * 
 */
class Form_ProblemAreaChange extends Zend_Form
{
	/**
	 * Construck ProblemArea Form class
	 *
	 * @param array $options
	 */
	public function __construct($problemAreaId)
	{
		// parent construct
		parent::__construct();
		
		// form
		$this->setAction('/problemarea/change/Id/'.$problemAreaId);
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
		$elm->setAttrib('rows', '25');
		$elm->setRequired(true);
		$elm->setLabel('Problem area description');
		$this->addElement($elm);
		
		// ProblemAreaId
		$elm = new Zend_Form_Element_Hidden('ProblemAreaId');
		$elm->setRequired(true)
			->setValue($problemAreaId);		
		$this->addElement($elm);
		
		// submit button
		$elm = new Zend_Form_Element_Submit('submit');
		$elm->setLabel('Zmeniť definíciu');
		$this->addElement($elm);
	}
}
?>