<?php
class  Compandsave_Checkout_Model_Cart extends Mage_Checkout_Model_Cart
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
        $this->getQuote()->getShippingAddress()->setCollectShippingRates(true);
		/*$check = Mage::getSingleton('checkout/cart')->getQuote()->hasItems();
		
        if(!strlen($this->getQuote()->getCouponCode()) || !$check){

            $this->getQuote()->collectTotals();
			
		}
        else{

            Mage::getModel('compandsave_productselector/quotes')->collectTotal();
		}*/
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