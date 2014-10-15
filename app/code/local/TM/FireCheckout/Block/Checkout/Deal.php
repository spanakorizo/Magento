<?php

class TM_FireCheckout_Block_Checkout_Deal extends Mage_Checkout_Block_Cart_Shipping
{	
	protected $_discountAmount;
	protected $_checkout = null;
	protected $_quote = null;
	protected $_CouponCode = null;
	Protected $_coupondetails = array();
    Protected $_giftcardsdetails = array();
	
	public function getCheckout()
    {
        if (null === $this->_checkout) {
            $this->_checkout = Mage::getSingleton('checkout/session');
        }
        return $this->_checkout;
    }

    /**
     * Get active quote
     *
     * @return Mage_Sales_Model_Quote
     */
    public function getQuote()
    {
        if (null === $this->_quote) {
            $this->_quote = $this->getCheckout()->getQuote();
        }
        return $this->_quote;
    }
	
	/*
	* @return total discount amount (float)
	*/
	public function getTotalDiscount(){
		$quote  = $this->getQuote();
		if(!empty($quote)){
			if(!$quote->isVirtual()){
				$address = $quote->getShippingAddress();
				$this->_discountAmount = abs($address->getDiscountAmount()) + $address->getGiftCardsAmount() +abs($address->getShippingDiscountAmount());
			}
			
		}

		return (float) $this->_discountAmount;
		
	}
	
	public function getCouponCode(){
		$quote  = $this->getQuote();
		if(!empty($quote)){
			$this->_CouponCode = $quote->getCouponCode();
		}
		
		return (string) $this->_CouponCode;
	}
	
	public function getCouponDescription($couponcode){
		$oCoupon = Mage::getModel('salesrule/coupon')->load($couponcode, 'code');
		
		$oRule = Mage::getModel('salesrule/rule')->load($oCoupon->getRuleId());
		$ruleData = $oRule->getData();
        $quote = $this->getQuote();
        $address = $quote->getShippingAddress();
        $getdiscountAmount = $ruleData['discount_amount'];
		$this->_coupondetails['description'] = $ruleData['description'];
		$actiontype = $ruleData['simple_action'];
        $totaldiscount = 0;

        foreach($quote->getAllVisibleItems() as $item){

            if ($item->getParentItem()) {
                continue;
            }
            if (!$oRule->getActions()->validate($item)){
                continue;
            }

            if($actiontype === 'by_percent'){
                //add code here for step
                $totaldiscount += (float) ($item->getQty() * (($item->getPrice() * $getdiscountAmount) / 100));

            }
            elseif($actiontype === 'cart_fixed'){ //total deduct from cart
                $totaldiscount +=   ($getdiscountAmount / $quote->getItemsQty()) * $item->getQty(); //here total / qty

            }
            elseif($actiontype === 'by_fixed'){ //fixed amount for qty
                $totaldiscount += $item->getQty() * $getdiscountAmount;

            }
        }


        $this->_coupondetails['discount_amount'] = $totaldiscount;

		return  $this->_coupondetails;
	}

    public function getGiftCardInfo(){

        $address = $this->getQuote()->getShippingAddress();

        $this->_giftcardsdetails = unserialize($address->getGiftCards());


        return $this->_giftcardsdetails;

    }
}