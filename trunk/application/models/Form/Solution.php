<?php
class Form_Solution extends Colla_Form
{
	/**
	 * @param array $options
	 * 
	 * param options['problemId']
	 */
	public function __construct($options = null)
	{
		// parent construct
		parent::__construct($options);
		
		if (!isset($options['problemId'])) {
			throw new Exception('missing problem Id in form');
		}
		
		// Name
		$elm = new Zend_Form_Element_Text('Name');
		$elm->setRequired(true);
		$elm->setAttrib('size', '60');
		$elm->addValidator(new Zend_Validate_StringLength(4, 128));
		$elm->setLabel('Názov riešenia:');
		$this->addElement($elm);
		
			
		// Definition
		$elm = new Zend_Form_Element_Textarea('Definition');
		$elm->setAttrib('rows', '10');
		$elm->setAttrib('cols', '10');
		$elm->setRequired(true);
		$elm->setAttrib('class', 'mceEditor');
		$elm->setLabel('Definícia riešenia:');
		$this->addElement($elm);
		
		// submit button
		$elm = new Zend_Form_Element_Submit('submit');
		$elm->setLabel('» Pridať riešenie');
		$this->addElement($elm);
		
		
		// form
		$this->setAction('/solution/create/id/'.$options['problemId']);
		$this->setMethod('post');
		
	}
}
?>