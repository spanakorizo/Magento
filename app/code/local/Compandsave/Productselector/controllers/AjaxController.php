<?php
class Compandsave_Productselector_AjaxController extends Mage_Core_Controller_Front_Action{
	public function indexAction(){
        $this->loadLayout();
        $this->renderLayout();
	}
    public function livechatAction(){
        $this->loadLayout();
        $this->getLayout()->getBlock('head')->setTitle($this->__('TomatoInk Chat Page'));
        $this->renderLayout();
    }
}