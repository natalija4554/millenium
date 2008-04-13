<?php
class Form_Problem extends Zend_Form
{
	/**
	 * @param array $options
	 */
	public function __construct($options = null)
	{
		// parent construct
		parent::__construct($options);
		
		// form
		$this->setAction('/problemarea/addproblem');
		$this->setMethod('post');
		
		// Name
		$elm = new Zend_Form_Element_Text('Name');
		$elm->setRequired(true);
		$elm->setAttrib('size', 80);
		$elm->addValidator(new Zend_Validate_StringLength(4, 128));
		$elm->setLabel('Názov problému');
		$this->addElement($elm);
		
			
		// Definition
		$elm = new Zend_Form_Element_Textarea('Definition');
		$elm->setAttrib('rows', '10');
		$elm->setRequired(true);
		$elm->setLabel('Definícia problému');
		$this->addElement($elm);
		
		// Kategoria
		$categoryTable = new Category();
		$elm = new Zend_Form_Element_Select('CategoryId');
		$rows = $categoryTable->getSelectList();
		$elm->addMultiOptions($rows)
			->setLabel('Kategória problému');
		$this->addElement($elm);
		
		// Note
		$elm = new Zend_Form_Element_Textarea('Note');
		$elm->setAttrib('rows', '1');
		$elm->setRequired(false);
		$elm->setLabel('Poznámka k pridaniu správy');
		$this->addElement($elm);
		
		// Klucove slova
		$elm = new Zend_Form_Element_Text('Keywords');
		$elm->setRequired(false);
		$elm->setAttrib('size', 100);
		$elm->setLabel('Kľúčové slová');
		$this->addElement($elm);
		
		// submit button
		$elm = new Zend_Form_Element_Submit('submit');
		$elm->setLabel('Pridať nový problém');
		$this->addElement($elm);
	}
}
?>