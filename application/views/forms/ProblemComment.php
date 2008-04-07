<?php
class Colla_Form_ProblemComment extends Zend_Form
{
	/**
	 * @param array $options
	 */
	public function __construct($problemId)
	{
		// parent construct
		parent::__construct();
		
		// form
		$this->setAction('/problem/add-comment/Id/'.$problemId);
		$this->setMethod('post');
		
		// Name
		$elm = new Zend_Form_Element_Text('Title');
		$elm->setRequired(true);
		$elm->setAttrib('size', 70);
		$elm->addValidator(new Zend_Validate_StringLength(4, 255));
		$elm->setLabel('Názov komentáru');
		$this->addElement($elm);
		
		// Definition
		$elm = new Zend_Form_Element_Textarea('Body');
		$elm->setAttrib('rows', '10');
		$elm->setRequired(true);
		$elm->setLabel('Komentár');
		$this->addElement($elm);
		
		// ProblemId
		$elm = new Zend_Form_Element_Hidden('ProblemId');
		$elm->setRequired(true)
			->setValue($problemId);		
		$this->addElement($elm);
				
		// submit button
		$elm = new Zend_Form_Element_Submit('submit');
		$elm->setLabel('Poslať');
		$this->addElement($elm);
	}
}
?>