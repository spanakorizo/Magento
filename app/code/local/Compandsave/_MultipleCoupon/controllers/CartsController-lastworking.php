<?php
class Compandsave_MultipleCoupon_CartsController extends Mage_Core_Controller_Front_Action
{

    protected $_eventPrefix = 'sales_quote';
    protected $_eventObject = 'quote';
    /**
     * Retrieve shopping cart model object
     *
     * @return Mage_Checkout_Model_Cart
     */
    protected function _getCart()
    {
        return Mage::getSingleton('checkout/cart');
    }
    protected function _getOwnQuote()
    {
        return Mage::getSingleton('compandsave_multiplecoupon/quote');
    }
    /**
     * Get checkout session model instance
     *
     * @return Mage_Checkout_Model_Session
     */
    protected function _getSession()
    {
        return Mage::getSingleton('checkout/session');
    }

    protected function _getCouponStatus($couponcode){
        if(!empty($couponcode)){
            $Coupon = Mage::getModel('salesrule/coupon')->load($couponcode, 'code');
            if($Coupon->getId())
                return true;
        }
        return false;

    }
    /**
     * Get current active quote instance
     *
     * @return Mage_Sales_Model_Quote
     */
    protected function _getQuote()
    {
        return $this->_getCart()->getQuote();
    }

    /**
     * Set back redirect url to response
     *
     * @return Mage_Checkout_CartController
     * @throws Mage_Exception
     */
    protected function _goBack()
    {
        $returnUrl = $this->getRequest()->getParam('return_url');
        if ($returnUrl) {

            if (!$this->_isUrlInternal($returnUrl)) {
                throw new Mage_Exception('External urls redirect to "' . $returnUrl . '" denied!');
            }

            $this->_getSession()->getMessages(true);
            $this->getResponse()->setRedirect($returnUrl);
        } elseif (!Mage::getStoreConfig('checkout/cart/redirect_to_cart')
            && !$this->getRequest()->getParam('in_cart')
            && $backUrl = $this->_getRefererUrl()
        ) {
            $this->getResponse()->setRedirect($backUrl);
        } else {
            if (($this->getRequest()->getActionName() == 'add') && !$this->getRequest()->getParam('in_cart')) {
                $this->_getSession()->setContinueShoppingUrl($this->_getRefererUrl());
            }
            $this->_redirect('checkout/cart');
        }
        return $this;
    }


    public function couponPostAction()
    {

        $flag = 1;
        if (!$this->_getCart()->getQuote()->getItemsCount()) {
            $this->_goBack();
            return;
        }

        $couponCode = (string) $this->getRequest()->getParam('coupon_code');
        /* if ($this->getRequest()->getParam('remove') == 1) {
            $couponCode = '';
        } */
        $item_array = array();
        unset($item_array);
        if(strlen($couponCode)){
            $oCoupon = Mage::getModel('salesrule/coupon')->load($couponCode, 'code');
            if(!empty($oCoupon)){
                $oRule = Mage::getModel('salesrule/rule')->load($oCoupon->getRuleId());
                $ruleData = $oRule->getData();
                $isactive = $ruleData['is_active'];
                //$newDescription = $ruleData['description'];
                $address = $this->_getQuote()->getShippingAddress();

                if (!Mage::getModel('salesrule/validator')->_canProcessRule($oRule, $address) || !$isactive) {
                    $this->_getSession()->addError(
                        $this->__('Coupon code "%s" is not valid or active', Mage::helper('core')->escapeHtml($couponCode))
                    );
                    $this->_goBack();
                    return;
                }

                $discountableitem = 0;

                foreach ($this->_getQuote()->getAllVisibleItems() as $item) {
                    if ($item->getParentItem()) {
                        continue;
                    }

                    if (!$oRule->getActions()->validate($item)) {
                        $this->_getSession()->addError(
                            $this->__('Coupon code "%s" is not valid for '.$item->getName(), Mage::helper('core')->escapeHtml($couponCode))
                        );
                        /*
                         * Store In array where rule is not applicable and send it total calculation function for not calculate
                         */
                        $item_array[] = $item->getId();
                    }
                    else{

                        $discountableitem = $discountableitem + 1;
                    }

                }
            }

        }

        //check if the coupon code have , cause we do not accept it

        $pos = strpos($couponCode,',');
        if($pos === FALSE){
            $oldCouponCode = $this->_getQuote()->getCouponCode();

            if(! $this->_getCouponStatus($couponCode) ){
                $this->_getSession()->addError(
                    $this->__('Coupon code "%s" Not Found / Expired.', Mage::helper('core')->escapeHtml($couponCode))
                );
                $this->_goBack();
                return;
            }




            if($flag){
                try {
                    //$totalLength = strlen($allOldCoupon);
                    $codeLength = strlen($couponCode);
                    $isCodeLengthValid = $codeLength && $codeLength <= Mage_Checkout_Helper_Cart::COUPON_CODE_MAX_LENGTH;
                    //$isValidlength = $totalLength && $totalLength <= Mage_Checkout_Helper_Cart::COUPON_CODE_MAX_LENGTH;

                    $this->_getQuote()->getShippingAddress()->setCollectShippingRates(true);
                    //$this->_getQuote()->setCouponCode($isCodeLengthValid ? $couponCode : '');

                    //try with set coupon code manually then
                    //$this->_getQuote()->setCouponCode($allOldCoupon)->save();



                    if ($codeLength) {
                        if ($isCodeLengthValid  ) {//&& $isValidlength
                            if (!empty($couponCode)) {

                                $this->_getQuote()->collectTotals($couponCode , $item_array, $discountableitem);
                                //$this->_getQuote()->setCouponCode($allOldCoupon)->save();
                                $this->_getQuote()->save();



                            }
                            $this->_getSession()->addSuccess(
                                $this->__('Coupon code "%s" was applied.', Mage::helper('core')->escapeHtml($couponCode))
                            );

                        } else {
                            $this->_getSession()->addError(
                                $this->__('Coupon code "%s" is not valid.', Mage::helper('core')->escapeHtml($couponCode))
                            );
                        }
                    } else {
                        $this->_getSession()->addSuccess($this->__('Coupon code was canceled.'));
                    }

                } catch (Mage_Core_Exception $e) {
                    $this->_getSession()->addError($e->getMessage());
                } catch (Exception $e) {
                    $this->_getSession()->addError($this->__('Cannot apply the coupon code.'));
                    Mage::logException($e);
                }



                $this->_goBack();
            }
            else{
                $this->_getSession()->addError(
                    $this->__('Coupon code "%s" already in use.', Mage::helper('core')->escapeHtml($couponCode))
                );
                $this->_redirect('checkout/cart');
                return;
            }
        }
        else{
            $this->_getSession()->addError(
                $this->__('Coupon code "%s" Contain Invalid Character.', Mage::helper('core')->escapeHtml($couponCode))
            );
            $this->_redirect('checkout/cart');
            return;
        }


    }
	public function removeCouponAction(){
		
		if (!$this->_validateFormKey()) {
            $this->_goBack();
            return;
        }
		
		$paramruleid = $this->getRequest()->getParam("ruleid");
		$allCouponCode = explode(',',$this->getRequest()->getParam("all_coupon_code"));
		$removeCouponCode = $this->getRequest()->getParam("remove_coupon_code");
		$couponcodes = null;
		foreach($allCouponCode as $couponcode){
			if(!empty($couponcode)){
				if(!strcmp($couponcode,$removeCouponCode))
					continue;
				else
					$couponcodes = $couponcode.','.$couponcodes;	
			}
			
		}
		$couponcodes = rtrim($couponcodes,',');
		
		$appliedruleid = explode(',',$this->_getQuote()->getAppliedRuleIds());
			
		$savedRuleId = null;			
		foreach($appliedruleid as $ruleId){
			if(!empty($ruleId)){
			
				if($ruleId == $paramruleid)
					continue;
				else
					$savedRuleId = $ruleId.','.$savedRuleId;				
			
			}
		
		}
		$this->_getQuote()->setAppliedRuleIds(strrev(rtrim($savedRuleId,',')))
							->setCouponCode($couponcodes)
							->save();

        $this->_getQuote()->collectTotals()->save();
		
		$this->_goBack();
	
	}

}