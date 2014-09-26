<?php

class TM_KnowledgeBase_Block_Adminhtml_Category_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{

    /**
     * Init form
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('knowledgebase_category_form');
        $this->setTitle(Mage::helper('knowledgebase')->__('Category Information'));
    }

    protected function _prepareForm()
    {
        $id = $this->getRequest()->getParam('id');
        $form = new Varien_Data_Form(array(
                'id' => 'edit_form',
                'action' => $this->getUrl('*/*/save', array('id' => $id)),
                'method' => 'post',
            )
        );

        if (Mage::registry('knowledgebase_category_data') ) {
            $data = Mage::registry('knowledgebase_category_data')->getData();
        }

        $fieldsetGeneral = $form->addFieldset(
            'category_general_form',
            array('legend' => Mage::helper('knowledgebase')->__('General'))
        );
        $fieldsetGeneral->addField('id', 'hidden', array(
        //  'class'     => 'required-entry',
            'name'      => 'id'
        ));
        $fieldsetGeneral->addField('active', 'select', array(
            'label'     => Mage::helper('knowledgebase')->__('Active'),
            'name'      => 'active',
            'required'  => true,
            'values'    => array(

                array(
                  'value'     => 0,
                  'label'     => Mage::helper('knowledgebase')->__('No'),
                ),
                array(
                  'value'     => 1,
                  'label'     => Mage::helper('knowledgebase')->__('Yes'),
                )
            ),
        ));

        $fieldsetGeneral->addField('name', 'text', array(
            'label'     => Mage::helper('knowledgebase')->__('Name'),
            'class'     => 'required-entry',
            'required'  => true,
            'name'      => 'name',
        ));

        $fieldsetGeneral->addField('identifier', 'text', array(
            'label'     => Mage::helper('knowledgebase')->__('Url'),
            'class'     => 'required-entry',
//            'required'  => true,
            'name'      => 'identifier',
            'class'     => 'validate-identifier',
        ));

        if (!Mage::app()->isSingleStoreMode()) {
        	$fieldsetGeneral->addField('store_id', 'select', array(
                'name'      => 'store_id',
                'label'     => Mage::helper('knowledgebase')->__('Store View'),
                'title'     => Mage::helper('knowledgebase')->__('Store View'),
                'required'  => true,
                'values'    => Mage::getSingleton('adminhtml/system_store')
                    ->getStoreValuesForForm(false, true),
            ));
        }
//        else {
//            $fieldset->addField('store_id', 'hidden', array(
//                'name'      => 'store_id',
////                'value'     => Mage::app()->getStore(true)->getId()
//            ));
//        }

        $fieldsetGeneral->addField('created_at', 'date', array(
            'label'     => Mage::helper('knowledgebase')->__('Create date'),
//            'required'  => true,
            'image'     => $this->getSkinUrl('images/grid-cal.gif'),
            'format'    => Varien_Date::DATETIME_INTERNAL_FORMAT,
            //Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT)
            'name'      => 'created_at',
            'disabled'  => true
        ));
        
        $fieldsetGeneral->addField('sort_order', 'text', array(
            'label'     => Mage::helper('knowledgebase')->__('Sort order'),
            'name'      => 'sort_order',
        ));
        

        $form->setValues($data);

        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }
}