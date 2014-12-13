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
		$head = $this->getLayout()->getBlock('head');
		$head->setTitle("Your Title");
		$head->setKeywords("your, keywords, anything");
		$head->setDescription("Your Description");

		$this->renderLayout();
	}

	public function surveyrespondAction()
	{
		//Get current layout state
		
		$this->loadLayout();
		$block = $this->getLayout()->createBlock(
		'Mage_Core_Block_Template',
		'survey-respond-campaign',
		array('template' => 'campaigns/2014/12-sur-re.phtml')
		);
		$this->getLayout()->getBlock('content')->append($block);
		
		$this->renderLayout();
	}

	public function surveynorespondAction()
	{
		//Get current layout state
		
		$this->loadLayout();
		$block = $this->getLayout()->createBlock(
		'Mage_Core_Block_Template',
		'survey-norespond-campaign',
		array('template' => 'campaigns/2014/12-sur-non.phtml')
		);
		$this->getLayout()->getBlock('content')->append($block);
		
		$this->renderLayout();
	}


}
?>