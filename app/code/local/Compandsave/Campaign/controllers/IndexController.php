<?php
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
?>
<?php

class Compandsave_Campaign_IndexController extends Mage_Core_Controller_Front_Action{

    public function indexAction()
	{
		//Get current layout state
		header('Location: http://www.buycheapink.org');

	}

    public function expiredAction()
	{
		//Get current layout state
		
		$this->loadLayout();
		$block = $this->getLayout()->createBlock(
		'Mage_Core_Block_Template',
		'general-expired-campaign',
		array('template' => 'campaigns/2015/01-gen.phtml')
		);
		$this->getLayout()->getBlock('content')->append($block);
		
		$this->renderLayout();
	}

}
?>