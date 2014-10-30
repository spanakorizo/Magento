<?php
class Compandsave_Coupon_CartsController extends Mage_Core_Controller_Front_Action
{

    /**
     * Action list where need check enabled cookie
     *
     * @var array
     */
    protected $_cookieCheckActions = array('add');

    /**
     * Retrieve shopping cart model object
     *
     * @return Mage_Checkout_Model_Cart
     */
    protected function _getCart()
    {
        return Mage::getSingleton('checkout/cart');
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
    /**
     * Initialize coupon
     */
    public function couponPostAction()
    {
        /**
         * No reason continue with empty shopping cart
         */
        if (!$this->_getCart()->getQuote()->getItemsCount()) {
            $this->_goBack();
            return;
        }

        $couponCode = (string) $this->getRequest()->getParam('coupon_code');

        if(strlen($couponCode)){
            $oCoupon = Mage::getModel('salesrule/coupon')->load($couponCode, 'code');
            if(!empty($oCoupon)){
                $oRule = Mage::getModel('salesrule/rule')->load($oCoupon->getRuleId());
                $ruleData = $oRule->getData();
                $isactive = $ruleData['is_active'];

                $address = $this->_getQuote()->getShippingAddress();

                if (!Mage::getModel('compandsave_coupon/validator')->_canProcessRule($oRule, $address) || !$isactive) {
                    $this->_getSession()->addError(
                        $this->__('Coupon code "%s" is not valid or active', Mage::helper('core')->escapeHtml($couponCode))
                    );
                    $this->_goBack();
                    return;
                }
                $discountableitem = 0;

                foreach ($this->_getQuote()->getAllItems() as $item) {
                    if ($item->getParentItem()) {
                        continue;
                    }

                    if (!$oRule->getActions()->validate($item)) {
                        $this->_getSession()->addError(
                            $this->__('Coupon code "%s" is not valid for '.$item->getName(), Mage::helper('core')->escapeHtml($couponCode))
                        );

                    }
                    else{

                        $discountableitem = $discountableitem + 1;
                    }

                }


            }

        }
        if($discountableitem == 0){

            $this->_getSession()->addError(
                $this->__('There are no discountable item for Coupon code "%s"', Mage::helper('core')->escapeHtml($couponCode))
            );
            $this->_goBack();
            return;

        }else{

            $oldCouponCodes = explode(',',$this->_getQuote()->getCouponCode());

            foreach($oldCouponCodes as $oldcouponcode){

                if (!strlen($couponCode) && !strlen($oldcouponcode)) {
                    $this->_goBack();
                    return;
                }

                if(!strcmp( $couponCode, $oldcouponcode )){
                    $flag = 0;
                    break;
                }
                else{
                    $flag = 1;
                }

            }
            if($flag == 1){

                if($this->_getQuote()->getCouponCode() == null)
                    $newCouponcode = $couponCode ;
                else
                    $newCouponcode = $this->_getQuote()->getCouponCode().','.$couponCode;

                try {

                    $codeLength = strlen($couponCode);
                    $isCodeLengthValid = $codeLength && $codeLength <= Mage_Checkout_Helper_Cart::COUPON_CODE_MAX_LENGTH;

                    $this->_getQuote()->getShippingAddress()->setCollectShippingRates(true);
                    $this->_getQuote()->setCouponCode($isCodeLengthValid ? $newCouponcode : '')
                        ->collectTotals()
                        ->save();

                    if ($codeLength) {

                        if ($isCodeLengthValid && $newCouponcode == $this->_getQuote()->getCouponCode()) {
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
            }else{

                $this->_getSession()->addError(
                    $this->__('Coupon code "%s" already in use.', Mage::helper('core')->escapeHtml($couponCode))
                );


            }
        }


        $this->_goBack();
    }

	public function removeCouponAction(){
		
		if (!$this->_validateFormKey()) {
            $this->_goBack();
            return;
        }
		
		/*$paramruleid = $this->getRequest()->getParam("ruleid");*/
		$allCouponCode = explode(',',$this->getRequest()->getParam("all_coupon_code"));
		$removeCouponCode = $this->getRequest()->getParam("remove_coupon_code");
        $oCoupon = Mage::getModel('salesrule/coupon')->load($removeCouponCode, 'code');
        if(empty($oCoupon)){
            $this->_getSession()->addError(
                $this->__('Coupon code "%s" Can Not Found.', Mage::helper('core')->escapeHtml($removeCouponCode))
            );
            $this->_redirect('checkout/cart');
            return;
        }else{

            $couponcodes = null;

            foreach($allCouponCode as $couponcode){
                if(!empty($couponcode)){
                    if(!strcmp($couponcode,$removeCouponCode)){
                        continue;
                    }
                    else
                        $couponcodes = $couponcode.','.$couponcodes;
                }

            }

            $couponcodes = rtrim($couponcodes,',');


            try{

                $this->_getQuote()->setCouponCode($couponcodes)->collectTotals()->save();

                $this->_getSession()->addSuccess(
                    $this->__('Coupon code "%s" was Removed Successfully.', Mage::helper('core')->escapeHtml($removeCouponCode))
                );

            }catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (Exception $e) {
                $this->_getSession()->addError($this->__('Cannot Remove Coupon "%s".', Mage::helper('core')->escapeHtml($removeCouponCode)));
                Mage::logException($e);
            }
        }


		$this->_goBack();
	
	}

}