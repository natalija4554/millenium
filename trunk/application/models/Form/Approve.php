<?php
class Form_Approve extends Zend_Form
{
	/**
	 * Construck ProblemArea Form class
	 *
	 * @param array $options
	 */
	public function __construct($problemId)
	{
		// parent construct
		parent::__construct($problemId);
		
		// form
		$this->setAction('/problem/approve/ProblemId/'.$problemId);
		$this->setMethod('post');
		
		
		// Nazov
		$elm = new Zend_Form_Element_Text('Title');
		$elm->setRequired(true)
			->setAttrib('size', 60)
			->addValidator(new Zend_Validate_StringLength(1, 255))
			->setLabel('Titulok komentaru');
		$this->addElement($elm);
		
		// Definition
		$elm = new Zend_Form_Element_Textarea('Body');
		$elm->setAttrib('rows', '10');
		$elm->setRequired(true);
		$elm->setLabel('Obsah komentáru');
		$this->addElement($elm);
		
		// submit button
		$elm = new Zend_Form_Element_Submit('createCategory');
		$elm->setLabel('Akceptovať problém');
		$this->addElement($elm);
	}
	
	
}
?>
