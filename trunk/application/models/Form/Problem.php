<?php
class Form_Problem extends Colla_Form
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
		$elm->setAttrib('size', '60');
		$elm->addValidator(new Zend_Validate_StringLength(4, 128));
		$elm->setLabel('Názov problému:');
		$this->addElement($elm);
		
			
		// Definition
		$elm = new Zend_Form_Element_Textarea('Definition');
		$elm->setAttrib('rows', '10');
		$elm->setAttrib('cols', '10');
		$elm->setRequired(true);
		$elm->setAttrib('class', 'mceEditor');
		$elm->setLabel('Definícia problému:');
		$this->addElement($elm);
		
		// Kategoria
		/*
		$categoryTable = new Category();
		$elm = new Zend_Form_Element_Select('CategoryId');
		$rows = $categoryTable->getSelectList();
		$elm->addMultiOptions($rows)
			->setLabel('Kategória problému:');
		$this->addElement($elm);
		*/
		
		$elm = new Zend_Form_Element_Text('Keywords');
		$elm->setRequired(true);
		$elm->setAttrib('size', '60');
		$elm->setLabel('Kľúčové slová:');
		$this->addElement($elm);
		
		// Note
		$elm = new Zend_Form_Element_Textarea('Note');
		$elm->setAttrib('rows', '1');
		$elm->setAttrib('class', 'siroky');
		$elm->setRequired(false);
		$elm->setLabel('Poznámka k pridaniu problému:');
		$this->addElement($elm);
		
		// submit button
		$elm = new Zend_Form_Element_Submit('submit');
		$elm->setLabel('» Pridať nový problém');
		$this->addElement($elm);
	}
}
?>