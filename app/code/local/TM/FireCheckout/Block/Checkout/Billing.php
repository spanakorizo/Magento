<?php

class TM_FireCheckout_Block_Checkout_Billing extends Mage_Checkout_Block_Onepage_Billing
{
    /**
     * Added to get shipping address from quote for guests too.
     *
     * Return Sales Quote Address model
     *
     * @return Mage_Sales_Model_Quote_Address
     */
    /*public function getAddress()
    {
        if (is_null($this->_address)) {
            $this->_address = $this->getQuote()->getBillingAddress();
            if ($this->isCustomerLoggedIn()) {
                if(!$this->_address->getFirstname()) {
                    $this->_address->setFirstname($this->getQuote()->getCustomer()->getFirstname());
                }
                if(!$this->_address->getLastname()) {
                    $this->_address->setLastname($this->getQuote()->getCustomer()->getLastname());
                }
            }
        }

        return $this->_address;
    }*/
    public function getAddress()
    {
        if (is_null($this->_address)) {
            $this->_address = $this->getQuote()->getBillingAddress();
        }

        return $this->_address;
    }

    public function getRegisterAccount()
    {
        return $this->getAddress()->getRegisterAccount()
            || $this->getRequest()->has('register');
    }

    public function getAddressesHtmlSelect($type)
    {
        return Mage::helper('firecheckout/address')->getAddressesHtmlSelect($type, $this);
    }

    public function hasCustomerAddressId()
    {
        return (bool)$this->getAddress()->getCustomerAddressId();
    }
    public function getCountryHtmlSelect($type)
    {
        $countryId = $this->getAddress()->getCountryId();
        if (is_null($countryId)) {
            $countryId = Mage::helper('core')->getDefaultCountry();
        }
        $select = $this->getLayout()->createBlock('core/html_select')
            ->setName($type.'[country_id]')
            ->setId($type.':country_id')
            ->setTitle(Mage::helper('checkout')->__('Country'))
            ->setClass('validate-select one_half_fire_checkout_select ti_select_font_size')
            ->setValue($countryId)
            ->setOptions($this->getCountryOptions());

        return $select->getHtml();
    }
}
