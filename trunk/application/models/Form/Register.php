<?php
class Form_Register extends Colla_Form
{
	/**
	 * @param array $options
	 */
	public function __construct($options = null)
	{
		// parent construct
		parent::__construct($options);
		
		// form
		$this->setAction('/user/register');
		$this->setMethod('post');
		
		// Name
		$elm = new Zend_Form_Element_Text('Username');
		$elm->setRequired(true);
		$elm->setAttrib('size', '25');
		$elm->addValidator(new Zend_Validate_StringLength(3, 32));
		$elm->setLabel('Vaše želané prihlasovacie meno:');
		$elm->addValidator(new Validate_UserExists());
		$this->addElement($elm);
		
		$elm = new Zend_Form_Element_Text('FullName');
		$elm->setRequired(true);
		$elm->setAttrib('size', '70');
		$elm->addValidator(new Zend_Validate_StringLength(3, 255));
		$elm->setLabel('Titul, Meno a Priezvisko:');
		$this->addElement($elm);
		
		// heslo 
		$elm = new Zend_Form_Element_Password('Password');
		$elm->setRequired(true)
			->setLabel('Vaše heslo:')
			->addValidator(new Validate_PasswordConfirmation('Password2'));
		$this->addElement($elm);
		
		$elm = new Zend_Form_Element_Password('Password2');
		$elm->setRequired(true)
			->setLabel('Overenie hesla:');
		$this->addElement($elm);
		
		// email
		$elm = new Zend_Form_Element_Text('EMail');
		$elm->setRequired(true);
		$elm->setAttrib('size', '30');
		$elm->addValidator(new Zend_Validate_EmailAddress());
		$elm->setLabel('Váš E-mail:');
		$this->addElement($elm);
		
		
		$elm = new Zend_Form_Element_Textarea('Podmienky');
		$elm->setRequired(false);
		$elm->setAttrib('class', 'regPodmienky');
		$elm->setAttrib('disabled', 'disabled');
		$elm->setAttrib('style', 'background-color: white');
		$elm->setLabel('Podmienky registrácie');
		$elm->setValue('');
		$this->addElement($elm);
		
		$elm = new Zend_Form_Element_Checkbox('Suhlasim');
		$elm->setRequired(true);
		$elm->setDescription('Súhlasím s podmienkami registrácie.');
		$elm->setAutoInsertNotEmptyValidator(false);
		$elm->addValidator(new Validate_RegisterConfirm());
		$elm->clearDecorators();
		$elm->addDecorators(array(
			 array('ViewHelper'),
			 array('Description', array('tag'=>null)),
			 array('Errors'), 
			 array('HtmlTag', array('tag' => 'dd')),
			 array('Label', array('tag' => 'dt', 'reqSuffix'=>'*'))
		));
		$elm->getErrors();
		$this->addElement($elm);
		
		// submit button
		$elm = new Zend_Form_Element_Submit('submit');
		$elm->setLabel('Registrovať sa »');
		$this->addElement($elm);
	}
}
?>