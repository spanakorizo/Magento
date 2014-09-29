<?php
class TM_KnowledgeBase_Model_Product_Attribute_Faq_Source extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
  {
      public function getAllOptions()
      {
          if (!$this->_options) {
              $this->_options = array(
                  array(
                      'value' => '',
                      'label' => '',
                  )
              );
          }
          $collection = Mage::getModel('knowledgebase/faq')->getCollection();
          foreach ($collection as $_faq) {
              $this->_options[] = array(
                  'value' => $_faq->getId(),
                  'label' => $_faq->getTitle()
              );
          }
//          Zend_Debug::dump($this->_options);
//          die;
          return $this->_options;
      }
  }