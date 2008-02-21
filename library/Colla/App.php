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
    	$front->throwExceptions(false);
    	$front->setBaseUrl($this->config->baseurl);
    	$front->setControllerDirectory($this->dirApplication . DIRECTORY_SEPARATOR . 'controllers');
    	$front->registerPlugin(new Colla_Controller_Plugin_Dispatch_Check());
    	$front->registerPlugin(new Colla_Controller_Plugin_Auth());
    	$front->registerPlugin(new Colla_Controller_Plugin_View_Layout());
    	$front->returnResponse(true);    	
    	
    	// acl
    	$acl = $this->getAcl();
    	$acl->add(new Zend_Acl_Resource('index'))
    		->add(new Zend_Acl_Resource('error'))
            ->add(new Zend_Acl_Resource('login'))
            ->add(new Zend_Acl_Resource('problemarea'))
            ->add(new Zend_Acl_Resource('logout'))
            ->add(new Zend_Acl_Resource('profile'))
            ->addRole(new Zend_Acl_Role('anonymous'))
            ->addRole(new Zend_Acl_Role('member'), 'anonymous')
            ->addRole(new Zend_Acl_Role('admin'), 'member')
            ->allow()
            ->deny(null, 'problemarea')
            ->allow('admin', 'problemarea');    	
    	
       	// go !
    	//try {
    		$response = $front->dispatch();
    		$response->sendResponse();
    	//} catch (Exception $e) {
    		// echo 'Nastala chyba: ';
    		// echo $e->getMessage();
    		//print_r($e);
 	 	//}
    }
    
	/**
     * Initialize Application
     * 
     */
	private function _initialize()
    {
    	// error reporting
        error_reporting(E_ALL | E_STRICT);
        
		// check magic quotes
		if (get_magic_quotes_gpc()) {
			if (!empty($_GET)) $_GET = stripMagicQuotes($_GET);
			if (!empty($_POST)) $_POST = stripMagicQuotes($_POST);
			if (!empty($_REQUEST)) $_REQUEST = stripMagicQuotes($_REQUEST);
			if (!empty($_COOKIE)) $_COOKIE = stripMagicQuotes($_COOKIE);
		}
                
        // nadstav cesty
        $this->dirLibrary 		= dirname(dirname(__FILE__));
        $this->dirApplication 	= dirname($this->dirLibrary) . DIRECTORY_SEPARATOR . 'application';
        
        // inclde path
        set_include_path($this->dirLibrary . PATH_SEPARATOR . $this->dirApplication . DIRECTORY_SEPARATOR . 'models' . PATH_SEPARATOR . get_include_path());
		Zend_Loader::registerAutoload();
 
        // read config file
        $this->config = new Zend_Config_Xml($this->dirApplication . DIRECTORY_SEPARATOR . 'config.xml');
        
        // set default DB adapter
        Zend_Db_Table_Abstract::setDefaultAdapter($this->getDb());
    }
    
    /**
     * Vrati objekt ACL
     * 
     * @return Zend_Acl
     */
	public function getAcl()
    {
    	// inicializuj ak nie je vytvoreny
        if (null === $this->_services['acl']) {
            $this->_services['acl'] = new Zend_Acl();
        }
        return $this->_services['acl'];
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