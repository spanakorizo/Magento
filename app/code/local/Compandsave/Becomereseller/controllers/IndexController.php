<?php
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
?>
<?php

class Compandsave_Becomereseller_IndexController extends Mage_Core_Controller_Front_Action{
   /* public function indexAction(){
        echo "Tell me why it can't work?";
        //$this->loadLayout();
        //$this->renderLayout();
    } */
    public function indexAction()
	{
		//Get current layout state
		
		$this->loadLayout();
		$block = $this->getLayout()->createBlock(
		'Mage_Core_Block_Template',
		'becomeseller',
		array('template' => 'becomeresellerpage/view.phtml')
		);
		$this->getLayout()->getBlock('content')->append($block);
		
		$this->renderLayout();
	}
}
?>