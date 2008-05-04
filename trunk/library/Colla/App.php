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
 * Few definitions to make life easier
 */
define('DS', DIRECTORY_SEPARATOR);
define('PS', PATH_SEPARATOR);


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
	private $_dirApplication;
	
	/**
	 * Umiestnenie kniznice
	 * @var string
	 */
    private $_dirLibrary;
    
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
	private $_config;
	
	/**
	 * Session data
	 *
	 * @var Zend_Session_Namespace
	 */
	private $_session;
	
	/**
	 * SingleTron pattern !
	 * 
	 * @return Colla_App
	 */
	public static function getInstance()
    {
        if (self::$_instance === null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    
	/**
     * @return Colla_App
     */
    public function run()
    {
    	$this->_setupEnviroment()
    		 ->_setupSession()
    		 ->_loadConfig()
    		 ->_setupDatabase()
    		 ->_setupEmail()
    		 ->_setupLayout()
    		 ->_setupTranslation()
    		 ->_setupAcl()
    		 ->_setupFrontController()
    		 ->_dispachFrontController();
    		 
    	return $this;
    }
    
    /**
     * Setup Enviroment
     *
     * @return Colla_App
     */
    private function _setupEnviroment()
    {
    	// error reporting
        error_reporting(E_ALL | E_STRICT);
        
		// nadstav cesty
        $this->_dirLibrary 		= dirname(dirname(__FILE__));
        $this->_dirApplication 	= dirname($this->_dirLibrary) . DS . 'application';
        
        // autoloader
        set_include_path($this->_dirLibrary . PS . $this->_dirApplication . DS . 'models' . PS . get_include_path());
		Zend_Loader::registerAutoload();

		return $this;
    }
		
    /**
     * Setup Session
     *
     * @return Colla_App
     */
    private function _setupSession()
    {
		Zend_Session::start();
		$this->_session = new Zend_Session_Namespace('Colla_App');
		return $this;
    }
    
    /**
     * Setup Configuration
     *
     * @return Colla_App
     */
 	private function _loadConfig()
 	{
        // read config file
        $this->_config = new Zend_Config_Xml($this->_dirApplication . DS . 'config.xml');
        
        // set defaul timezone
        date_default_timezone_set($this->_config->timezone);
        return $this;
 	}
 	
 	/**
 	 * Setup Database
 	 *
 	 * @return Colla_App
 	 */
 	private function _setupDatabase()
 	{
 		$adapter = Zend_Db::factory($this->_config->database);
        Zend_Db_Table_Abstract::setDefaultAdapter($adapter);
        return $this;
 	}
 	
 	private function _setupEmail()
 	{
 		$tr = new Zend_Mail_Transport_Smtp($this->_config->email->smtp->host);
		Zend_Mail::setDefaultTransport($tr);
 		return $this;
 	}
 	
 	/**
 	 * Setup Layout
 	 *
 	 * @return Colla_App
 	 */
 	private function _setupLayout()
 	{
 		// start layout
 		Zend_Layout::startMvc();
        $layout = Zend_Layout::getMvcInstance();
        $layout->setLayoutPath($this->_dirApplication . DS . 'views' . DS . 'layout' . DS);
        $layout->setLayout('simple');
        
        return $this;
 	}
 	
 	/**
 	 * Setup Translation
 	 *
 	 * @return Colla_App
 	 */
 	private function _setupTranslation()
 	{
 		// configure path
 		$dirLanguages 	= dirname($this->_dirLibrary) . DS . 'languages';
 		
 		// translate, add here more translations
        // @todo dorobit automaticke nacitavanie jazykov
        $adapter = new Zend_Translate(Zend_Translate::AN_GETTEXT, $dirLanguages.DS.'sk'.DS.'lang.mo', 'sk');
        $adapter->addTranslation($dirLanguages.DS.'en'.DS.'lang.mo', 'en');
        $adapter->setLocale('sk'); // zatial len SK
        Zend_Form::setDefaultTranslator($adapter); 
        Zend_Registry::set('Zend_Translate', $adapter);
        
        return $this;
 	}
 	
	/**
 	 * Setup Acl
 	 *
 	 * @return Colla_App
 	 */
 	private function _setupAcl()
 	{
 		Zend_Registry::set('Colla_Acl', new Colla_Acl());
 		return $this;
 	}
 	 	
 	/**
 	 * Setup Front Controller
 	 *
 	 * @return Colla_App
 	 */
 	private function _setupFrontController()
 	{
        // set options
    	$front = Zend_Controller_Front::getInstance();
    	$front->throwExceptions(true);
    	$front->setBaseUrl('/');
    	$front->setControllerDirectory($this->_dirApplication . DS . 'controllers');
    	$front->returnResponse(true);
    	
    	// register plugins
    	$front->registerPlugin(new Colla_Controller_Plugin_ProblemArea());
    	
        return $this;
    }
    
    /**
     * Dispatch Front Controller
     *
     * @return Colla_App
     */
    private function _dispachFrontController()
    {
		// dispach
		$front = Zend_Controller_Front::getInstance();
    	$response = $front->dispatch();
    	$response->sendResponse();
    	
    	return $this;
    }
    
    /**
     * Return number of problem are id, if none set, return false
     *
     * @return int | false
     */
    
    public function hasProblemArea()
    {
    	return (isset($this->_session->problemAreaId) ? true : false);
    }
    public function getProblemArea()
    {
    	if (!isset($this->_session->problemAreaId)) {
    		throw new Exception('No problem area !');
    	}
    	return $this->_session->problemAreaId;
    }
    public function setProblemArea($id)
    {
    	$this->_session->problemAreaId = $id;
    }
    
    public function getAppConfig()
    {
    	return $this->_config;
    }
    
    static function getConfig()
    {
    	return self::getInstance()->getAppConfig();	
    }
}
?>