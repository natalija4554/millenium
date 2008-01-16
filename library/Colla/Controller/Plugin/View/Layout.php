<?php

/**
 * Zobrazi layout
 * 
 */
class Colla_Controller_Plugin_View_Layout extends Zend_Controller_Plugin_Abstract
{
    public function dispatchLoopShutdown()
    {
        $view = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer')->view;
        $response = $this->getResponse();
        $view->content = $response->getBody();
        $response->setBody($view->render('layout.phtml'));
    }
}