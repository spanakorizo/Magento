<?php

class Compandsave_Coupon_Block_Deal extends Mage_Checkout_Block_Cart_Shipping
{	
	protected $_discountAmount;
	protected $_checkout = null;
	protected $_quote = null;
	protected $_CouponCode = null;
	Protected $_coupondetails = array();
    Protected $_giftcardsdetails = array();
	protected $_CouponRule = null;
    protected $_ruledetails = array();

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
	
	public function getRuleId(){
		$quote  = $this->getQuote();
		if(!empty($quote)){

			$this->_CouponRule = $quote->getAppliedRuleIds();
		}

		return (string) $this->_CouponRule;
	}

    public function getCouponCode(){
        $quote  = $this->getQuote();
        if(!empty($quote)){
            $this->_CouponCode = $quote->getCouponCode();
        }

        return (string) $this->_CouponCode;
    }

    public function getRuleDescription($rule){

        $oRule = Mage::getModel('salesrule/rule')->load($rule);

        $this->_ruledetails['description'] = $oRule->getDescription();
        $this->_ruledetails['coupon_code'] = $oRule->getCouponCode();
        $this->_ruledetails['rule_id'] = $rule;


        return  $this->_ruledetails;
    }
	
	public function getCouponDescription($couponcode){
		$oCoupon = Mage::getModel('salesrule/coupon')->load($couponcode, 'code');
		
		$oRule = Mage::getModel('salesrule/rule')->load($oCoupon->getRuleId());

        $this->_coupondetails['description'] = $oRule->getDescription();
        $this->_coupondetails['rule_id'] = $oCoupon->getRuleId();


		return  $this->_coupondetails;
	}

    public function getGiftCardInfo(){

        $address = $this->getQuote()->getShippingAddress();

        $this->_giftcardsdetails = unserialize($address->getGiftCards());


        return $this->_giftcardsdetails;

    }
}