<?php
/**
 * Collaboration Information System
 * 
 * Slovak university of technology Bratislava
 * FACULTY OF INFORMATICS AND INFORMATION TECHNOLOGIES
 * Degree Course: INFORMATION SYSTEMS
 * 
 * Author: Bc. František Durajka
 * Thesis: COLLABORATION INFORMATION SYSTEM
 * Supervisor: prof. Ing. Vladimír Vojtek PhD.
 *
 * @category Colla
 * @package Colla_App
 * @version $Id$
 */

/**
 * Colla_App
 *
 */
final class Colla_App
{
	/**
	 * Umiestnenie aplikacie
	 * @var string
	 */
	public $dirApplication;
	
	/**
	 * Umiestnenie kniznice
	 * @var string
	 */
    public $dirLibrary;
    
    /**
     * Umiestnenie jazykovych suborov
     * @var string
     */
    public $dirLanguages;
	
	/**
	 * Instancia triedy
	 * 
	 * @var Colla_App
	 */
	protected static $_instance = null;
	
	/**
	 * Konfiguracia
	 * @var Zend_Config_Xml
	 */
	public $config;
	
	
	/**
	 * Database services
	 * 
	 * @var array
	 */
	protected $_services = array(
        'acl' => null,
        'db'  => null
        );
	
	/**
	 * SingleTron pattern !
	 * 
	 * @return Colla_App
	 */
	public static function getInstance()
    {
        if (self::$_instance === null) {
            self::$_instance = new self();
            self::$_instance->_initialize();
        }
        return self::$_instance;
    }
    
	/**
     * Spusti aplikaciu
     * 
     */
    public function run()
    {
    	// set options
    	$front = Zend_Controller_Front::getInstance();
    	$front->throwExceptions(true);
    	$front->setBaseUrl('/');
    	$front->setControllerDirectory($this->dirApplication . DIRECTORY_SEPARATOR . 'controllers');
    	$front->returnResponse(true);    	
    	
    	$response = $front->dispatch();
    	$response->sendResponse();
    }
    
	/**
     * Initialize Application
     * 
     * BOOTSTRAP
     */
	private function _initialize()
    {
    	// error reporting
        error_reporting(E_ALL | E_STRICT);
        
		// check magic quotes
		if (get_magic_quotes_gpc()) {
			die('Magic quotes gpc have to be turned OFF !');
		}
                
        // nadstav cesty
        $this->dirLibrary 		= dirname(dirname(__FILE__));
        $this->dirApplication 	= dirname($this->dirLibrary) . DIRECTORY_SEPARATOR . 'application';
        $this->dirLanguages 	= dirname($this->dirLibrary) . DIRECTORY_SEPARATOR . 'languages';
        
        // inclde path
        set_include_path($this->dirLibrary . PATH_SEPARATOR . $this->dirApplication . DIRECTORY_SEPARATOR . 'models' . PATH_SEPARATOR . get_include_path());
		Zend_Loader::registerAutoload();
		
		// start session
		Zend_Session::start();
 
        // read config file
        $this->config = new Zend_Config_Xml($this->dirApplication . DIRECTORY_SEPARATOR . 'config.xml');
        
        // set default DB adapter
        Zend_Db_Table_Abstract::setDefaultAdapter($this->getDb());
        
        // start layout
        Zend_Layout::startMvc();
        
        // translate, add here more translations
        // @todo dorobit automaticke nacitavanie jazykov
        $adapter = new Zend_Translate(Zend_Translate::AN_GETTEXT, $this->dirLanguages.DIRECTORY_SEPARATOR.'sk'.DIRECTORY_SEPARATOR.'lang.mo', 'sk');
        $adapter->addTranslation($this->dirLanguages.DIRECTORY_SEPARATOR.'en'.DIRECTORY_SEPARATOR.'lang.mo', 'en');
        $adapter->setLocale('sk'); // zatial len SK
        Zend_Form::setDefaultTranslator($adapter); 
        Zend_Registry::set('Zend_Translate', $adapter);
    }
    
    /**
     * Vrati objekt databzy
     * 
     * @return Zend_Db_Adapter_Abstract
     */
    public function getDb()
    {
    	// inicializuj ak nie je vytvoreny
    	if (null === $this->_services['db']) {
            $this->_services['db'] = Zend_Db::factory($this->config->database);
        }
        return $this->_services['db'];
    }
}
?>