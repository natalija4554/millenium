<?php

/**
 * Front controller plugin
 *
 */
class Colla_Controller_Plugin_Auth extends Zend_Controller_Plugin_Abstract 
{
	public function preDispatch (Zend_Controller_Request_Abstract $request)
	{
		// auth objekt
		$auth = Zend_Auth::getInstance();

		// zisti rolu identity 
        if ($auth->hasIdentity()) {
            switch ($auth->getIdentity()->username) {
                case 'admin':
                    $role = 'admin';
                    break;
                default:
                    $role = 'member';
                    break;
            }
        } else {
            $role = 'anonymous';
        }

        // ziska request
        $request = $this->getRequest();

        // ziska nazov aktualneho controlleru
        $controllerName = $request->getControllerName();

        // pristup k ACL listu
        $acl = Colla_App::getInstance()->getAcl();

        // ak neexistuje takyto objekt (resource) v acl
        if (!$acl->has($controllerName)) {
            throw new Exception('Sorry, the requested controller '.$controllerName.' does not exist as an ACL resource');
        }

        // zisti ci je povolene, ak nie vyrenderuj denied akciu.
        if (!$acl->isAllowed($role, $controllerName, $request->getActionName())) {
            $request->setControllerName('index')
                    ->setActionName('denied')
                    ->setDispatched(false);
        }
		 
	}
}

?>