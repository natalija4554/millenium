<?php

class Validate_RegisterConfirm extends Zend_Validate_NotEmpty 
{
	const IS_EMPTY = 'isRequired';
	
	protected $_messageTemplates = array(
        self::IS_EMPTY => "Musíte súhlasiť s registračnými podmienkami"
    );
}

?>