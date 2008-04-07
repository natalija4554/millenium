<?php

/**
 * Formular na pridanie novej problemovej oblasti
 * 
 */
class Colla_Form_SubCategory extends Zend_Form
{
	/**
	 * Construck ProblemArea Form class
	 *
	 * @param array $options
	 */
	public function __construct($parentId)
	{
		// parent construct
		parent::__construct();
		
		// form
		$this->setAction('/category/newsubcategory/Id/'.$parentId);
		$this->setMethod('post');		
		
		// Nazov
		$elm = new Zend_Form_Element_Text('Name');
		$elm->setRequired(true)
			->setAttrib('size', 60)
			->addValidator(new Zend_Validate_StringLength(1, 255))
			->setLabel('Názov kategórie');
		$this->addElement($elm);
		
		// ParentId
		$elm = new Zend_Form_Element_Hidden('ParentId');
		$elm->setRequired(true)
			->setValue($parentId);		
		$this->addElement($elm);
		
		// submit button
		$elm = new Zend_Form_Element_Submit('createCategory');
		$elm->setLabel('Vytvoriť');
		$this->addElement($elm);
	}
}
?>
