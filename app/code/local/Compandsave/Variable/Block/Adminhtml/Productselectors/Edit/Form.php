<?php
class Compandsave_Variable_Block_Adminhtml_Productselectors_Edit_Form
    extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        // Instantiate a new form to display coupon edit
        $form = new Varien_Data_Form(array(
            'id' => 'edit_form',
            'action' => $this->getUrl(
                'compandsave_variable_admin/productselectors/edit',
                array(
                    '_current' => true,
                    'continue' => 0,
                )
            ),
            'method' => 'post',//post method 
        ));
        $form->setUseContainer(true);
        $this->setForm($form);

        // Define a new fieldset. We need only one for our simple entity.
        $fieldset = $form->addFieldset(
            'general',
            array(
                'legend' => $this->__('Product Selectors Details')
            )
        );

        $ProductselectorsSingleton = Mage::getSingleton(
            'compandsave_variable/productselectors'
        );

        // Add the fields that we want to be editable.
        $this->_addFieldsToFieldset($fieldset, array(
            'brand_id' => array(
                'label' => $this->__('Brand Name'),
                'input' => 'select',
                'required' => true,
                'options' => $ProductselectorsSingleton->getCategory(),
            ),
            'value' => array(
                'label' => $this->__('Brand JSON'),
                'input' => 'textarea',
                'required' => true,
            ),
			'visibility' => array(
                'label' => $this->__('Visibility'),
                'input' => 'select',
                'required' => true,
                'options' => $ProductselectorsSingleton->getAvailableVisibilies(),
            ),
			'status' => array(
                'label' => $this->__('Status'),
                'input' => 'select',
                'required' => true,
                'options' => $ProductselectorsSingleton->getStatus(),
            ),

            /**
             * Note: we have not included created_at or updated_at.
             * We will handle those fields ourself in the model
			 * before saving.
             */
        ));

        return $this;
    }

    /**
     * This method makes life a little easier for us by pre-populating
     * fields with $_POST data where applicable and wrapping our post data
     * in 'CouponData' so that we can easily separate all relevant information
     * in the controller. You could of course omit this method entirely
     * and call the $fieldset->addField() method directly.
     */
    protected function _addFieldsToFieldset(
        Varien_Data_Form_Element_Fieldset $fieldset, $fields)
    {
        $requestData = new Varien_Object($this->getRequest()
            ->getPost('ProductselectorsData'));

        foreach ($fields as $name => $_data) {
            if ($requestValue = $requestData->getData($name)) {
                $_data['value'] = $requestValue;
            }

            // Wrap all fields with CouponData group.
            $_data['name'] = "ProductselectorsData[$name]";

            // Generally, label and title are always the same.
            $_data['title'] = $_data['label'];

            // If no new value exists, use the existing coupon data.
            if (!array_key_exists('value', $_data)) {
                $_data['value'] = $this->_getProductselectors()->getData($name);
            }

            // Finally, call vanilla functionality to add field.
            $fieldset->addField($name, $_data['input'], $_data);
        }

        return $this;
    }

    /**
     * Retrieve the existing Coupon for pre-populating the form fields.
     * For a new Coupon entry, this will return an empty Coupon object.
     */
    protected function _getProductselectors()
    {
        if (!$this->hasData('productselectors')) {
            // This will have been set in the controller.
            $Productselectors = Mage::registry('current_productselectors');

            // Just in case the controller does not register the Coupon.
            if (!$Productselectors instanceof
                    Compandsave_Variable_Model_Brand) {
                $Productselectors = Mage::getModel(
                    'compandsave_variable/productselectors'
                );
            }

            $this->setData('productselectors', $Productselectors);
        }

        return $this->getData('productselectors');
    }
}