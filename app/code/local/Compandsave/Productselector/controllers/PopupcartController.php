<?php
class Compandsave_Productselector_PopupcartController extends Mage_Core_Controller_Front_Action{
	public function indexAction(){
        $this->loadLayout();
       $this->renderLayout();
		//echo "testindex";
	}
    public function headerAction(){
        $this->loadLayout();
        $this->renderLayout();
        //echo "testheader";
    }
	
 }