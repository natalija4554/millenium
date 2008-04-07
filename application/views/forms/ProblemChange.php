<?php
class Colla_Form_ProblemChange extends Zend_Form
{
	/**
	 * @param array $options
	 */
	public function __construct($problemId)
	{
		// parent construct
		parent::__construct();
		
		// form
		$this->setAction('/problem/changedefinition/Id/'.$problemId);
		$this->setMethod('post');
		
		// Name
		$elm = new Zend_Form_Element_Text('Name');
		$elm->setRequired(true);
		$elm->setAttrib('size', 40);
		$elm->addValidator(new Zend_Validate_StringLength(4, 128));
		$elm->setLabel('Názov problému');
		$this->addElement($elm);
		
		// Definition
		$elm = new Zend_Form_Element_Textarea('Definition');
		$elm->setAttrib('rows', '10');
		$elm->setRequired(true);
		$elm->setLabel('Definícia problému');
		$this->addElement($elm);
		
		// Poznamka k zmene
		$elm = new Zend_Form_Element_Textarea('Note');
		$elm->setAttrib('rows', '1');
		$elm->setRequired(false);
		$elm->setLabel('Poznámka k zmene');
		$this->addElement($elm);
		
		// ProblemId
		$elm = new Zend_Form_Element_Hidden('ProblemId');
		$elm->setRequired(true)
			->setValue($problemId);		
		$this->addElement($elm);
		
		
		// submit button
		$elm = new Zend_Form_Element_Submit('submit');
		$elm->setLabel('Pozmeniť definíciu');
		$this->addElement($elm);
	}
}
?>