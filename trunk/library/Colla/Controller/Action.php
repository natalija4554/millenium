<?php
/**
 * Zakladny controller aplikacie
 *
 */
class Colla_Controller_Action extends Zend_Controller_Action
{
    public function preDispatch()
    {
    	// ziska objekt view
        $view = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer')->view;

        // nastavi premenne
        // - authenticated
        // - user
        $auth = Zend_Auth::getInstance();
        if ($view->authenticated = $auth->hasIdentity()) {
            $view->user = new Colla_Model_User($auth->getIdentity());
        } else {
            $view->user = new Colla_Model_User();
        }
   	}

    public function __call($name, $args)
    {
        throw new Exception('Sorry, the requested action does not exist');
    }
}