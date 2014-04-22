<?php
class Compandsave_Variable_Block_Adminhtml_Coupon_Edit_Form
    extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        // Instantiate a new form to display coupon edit
        $form = new Varien_Data_Form(array(
            'id' => 'edit_form',
            'action' => $this->getUrl(
                'compandsave_variable_admin/coupon/edit',
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
                'legend' => $this->__('Coupon Details')
            )
        );

        $CouponSingleton = Mage::getSingleton(
            'compandsave_variable/coupon'
        );

        // Add the fields that we want to be editable.
        $this->_addFieldsToFieldset($fieldset, array(
            'value' => array(
                'label' => $this->__('Coupon Code'),
                'input' => 'text',
                'required' => true,
            ),
			/* 'entity_type_id' => array(
                'label' => $this->__('Type'),
                'input' => 'select',
                'required' => true,
				'options' => $CouponSingleton->getType(),
            ), */
			'description' => array(
                'label' => $this->__('Description'),
                'input' => 'textarea',
                'required' => false,
            ),
            'visibility' => array(
                'label' => $this->__('Visibility'),
                'input' => 'select',
                'required' => true,
                'options' => $CouponSingleton->getAvailableVisibilies(),
            ),
			'status' => array(
                'label' => $this->__('Status'),
                'input' => 'select',
                'required' => true,
                'options' => $CouponSingleton->getStatus(),
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
            ->getPost('CouponData'));

        foreach ($fields as $name => $_data) {
            if ($requestValue = $requestData->getData($name)) {
                $_data['value'] = $requestValue;
            }

            // Wrap all fields with CouponData group.
            $_data['name'] = "CouponData[$name]";

            // Generally, label and title are always the same.
            $_data['title'] = $_data['label'];

            // If no new value exists, use the existing coupon data.
            if (!array_key_exists('value', $_data)) {
                $_data['value'] = $this->_getCoupon()->getData($name);
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
    protected function _getCoupon()
    {
        if (!$this->hasData('coupon')) {
            // This will have been set in the controller.
            $Coupon = Mage::registry('current_coupon');

            // Just in case the controller does not register the Coupon.
            if (!$Coupon instanceof
                    Compandsave_Variable_Model_Coupon) {
                $Coupon = Mage::getModel(
                    'compandsave_variable/coupon'
                );
            }

            $this->setData('coupon', $Coupon);
        }

        return $this->getData('coupon');
    }
}