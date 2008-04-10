<?php
class Colla_Controller_Plugin_ProblemArea extends Zend_Controller_Plugin_Abstract 
{
	public function preDispatch(Zend_Controller_Request_Abstract $request)
	{
		// if allready set
		$collaApp = Colla_App::getInstance();
		if ($collaApp->hasProblemArea()) {
			return;
		}
		
		// find info about problem areas
		$problemArea = new ProblemArea();
		if ($id = $problemArea->getDefaultProblemArea()) {
			$collaApp->setProblemArea($id);
			return;
		}
		
		// redirect to page, where user can choose default problem area
		$request->setControllerName('problemarea');
		$request->setActionName('select');
	}
}
?>