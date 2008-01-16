<?php

class Colla_Controller_Plugin_Dispatch_Check extends Zend_Controller_Plugin_Abstract
{
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        if (!Zend_Controller_Front::getInstance()->getDispatcher()->isDispatchable($this->getRequest())) {
            throw new Zend_Controller_Dispatcher_Exception();
        }
    }
}
