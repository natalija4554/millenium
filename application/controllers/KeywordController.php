<?php
class KeywordController extends Colla_Controller_Action 
{
 	public function ajaxListAction()
 	{
 		// get the prefix
 		$prefix = $this->getRequest()->getParam('prefix');
 		
 		// save
 		$kTable = new Keyword();
 		$select = $kTable->select()
 			->where("Keyword LIKE '".addslashes($prefix)."%'");
 		$rows = $kTable->fetchAll($select);
 		$data = array();
 		foreach ($rows as $keyword) {
 			$data[] = $keyword->Keyword;
 		}
 		$this->view->data = $data;
 	}
}
?>