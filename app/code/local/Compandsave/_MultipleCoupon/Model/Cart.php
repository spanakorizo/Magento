<?php
class  Compandsave_MultipleCoupon_Model_Cart extends Mage_Checkout_Model_Cart
{
/**
* Save cart
*
* @return Mage_Checkout_Model_Cart
*/
    public function save()
    {

        Mage::dispatchEvent('checkout_cart_save_before', array('cart'=>$this));
        $this->getQuote()->getBillingAddress();
        $address = $this->getQuote()->getShippingAddress();

        $freeshippingflag = Mage::getStoreConfig('carriers/freeshipping/active');
        $freeshippingamount = Mage::getStoreConfig('carriers/freeshipping/free_shipping_subtotal');
        $freesubtotalamount = $this->getQuote()->getBaseSubtotal();
        $methods = Mage::getSingleton('shipping/config')->getActiveCarriers();

        if($freeshippingflag == 1 and $freesubtotalamount >= $freeshippingamount ){

            foreach($methods as $_ccode => $_carrier) { //get all carrier model
                if($_ccode ==='freeshipping'){ //carrier code
                    if($_methods = $_carrier->getAllowedMethods())  {
                        if(!$_title = Mage::getStoreConfig("carriers/$_ccode/title"))
                            $_title = $_ccode;

                        foreach($_methods as $_mcode => $_method){//carrier method code
                            if($_mcode ==='freeshipping'){
                                $address->setFreeShipping(1);
                                $address->setShippingDescription(trim($_title).' - '.trim($_method));
                                $address->setShippingMethod(trim($_ccode) . '_' . trim($_mcode))->setCollectShippingRates(true)->save();
                            }

                        }
                    }
                }

            }

        }/*elseif($freeshippingflag == 1 and $freesubtotalamount < $freeshippingamount){

            //if($address->getShippingMethod() === 'freeshipping_freeshipping'){
                $array = $address->setCountryId($address->getCountryId())
                    ->setCollectShippingRates(true)->getGroupedAllShippingRates();
                foreach ($array as $carrier) {
                    foreach ($carrier as $rate) {

                        $rate_array[] = $rate->getPrice();

                    }

                    $m_rate = min($rate_array);

                    foreach ($carrier as $rate) {

                        if($rate->getPrice() == $m_rate){

                            $shipping_code = $rate->getCode();



                        }

                    }

                }

            $address->setShippingMethod($shipping_code);

        }*/else{
            $address->setCollectShippingRates(true)->collectShippingRates();
        }

        $this->getQuote()->collectTotals();
        $this->getQuote()->save();

        $this->getCheckoutSession()->setQuoteId($this->getQuote()->getId());
        /**
        * Cart save usually called after changes with cart items.
        */
        Mage::dispatchEvent('checkout_cart_save_after', array('cart'=>$this));
        return $this;
    }

}