<?php
/**
 * Magento Enterprise Edition
 *
 */


class Compandsave_Coupon_Block_Cart_Shipping extends Mage_Checkout_Block_Cart_Shipping
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