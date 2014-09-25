<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Checkout
 * @copyright   Copyright (c) 2013 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */


class Compandsave_Checkout_Block_Cart_Shipping extends Mage_Checkout_Block_Cart_Shipping
{


    /**
     * Get Lowest Shipping Method Code
     *
     * @return string
     */
    protected $_shipping_code;

    public function getLowestShippingMethodCode( $code = null )
    {
        if (!empty($code)) {

            $address = Mage::getSingleton('checkout/cart')->getQuote()->getShippingAddress();
            $address->setCountryId($code)
                ->setCollectShippingRates(true);
            unset($rate_array);
            // Find if our shipping has been included.
            $rates = $address->collectShippingRates()->getGroupedAllShippingRates();
            foreach ($rates as $carrier) {
                foreach ($carrier as $rate) {

                    $rate_array[] = $rate->getPrice();

                }
                $m_rate = min($rate_array);
                foreach ($carrier as $rate) {

                    if($rate->getPrice() == $m_rate){

                        $this->_shipping_code = $rate->getCode();



                    }

                }
            }
        }
        else{
            $this->_shipping_code = '';
        }


        return $this->_shipping_code;

    }


}
