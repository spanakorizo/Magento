<?php
class Compandsave_Productselector_CartsController extends Mage_Core_Controller_Front_Action
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
        return Mage::getSingleton('compandsave_productselector/quote');
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

    public function indexAction(){
    
        $allAvailable = true;
        $allAdded     = true;
        $flag = 0;
        $productIds = explode(',', $this->getRequest()->getParam('product'));
        //$productIds = array();
        
        $productIds[] = $this->getRequest()->getParam('products');
        
        if (!is_array($productIds)) {
            $this->__('Product IDs Not Found');
            $this->_redirect('checkout/cart');
            return;
        }

        if (!$this->_validateFormKey()) {
            $this->__('Form Key Not Match.');
            $this->_redirect('checkout/cart');
            return;
        }
        
        $cart = Mage::getModel('checkout/cart');
        
        $cart->init();
        $html_response = "";
        foreach ($productIds as $product_id) {
        
            if ($product_id == '') {
                continue;
            }
            $qty = $this->getRequest()->getParam('qty'.$product_id);

            if ($qty <= 0) continue;
            
            $product = Mage::getModel('catalog/product')->load($product_id);
                        
            if ($product->getTypeId() == Mage_Catalog_Model_Product_Type::TYPE_SIMPLE) {
                
                if ($product->getId() && $product->isVisibleInCatalog()) {
                    try {

                        
                        $cart->getQuote()->addProduct($product,$qty);
                        //return added product information
                        $html_response .= "<div class='one_fifth'><a href='" . $product->getProductUrl() . "'><img class='block_center' style='width:75px;height:75px;' src='" . $product->getImageUrl() . "'></img></a></div><div class='two_fifth'><a href='" . $product->getProductUrl() . "'><p class='ti_product_blockName'>" . $product->getName() . "</p></a></div><div class='one_fifth text_center'><p>".$qty."</p></div><div class='one_fifth'><p>$".$qty * $product->getPrice()."</p></div><div class='ti_cms_separator'></div>";


                    } catch (Exception $e){
                        $allAdded = false;
                    }
                } else {
                    $allAvailable = false;
                }
            }
            if($product->getTypeId() == Mage_Catalog_Model_Product_Type::TYPE_BUNDLE){
            
                $bundle_option = array();
                $bundle_qty = array();
                $bundle_option = $this->getRequest()->getParam('bundle_option'.$product_id);
                $bundle_qty = $this->getRequest()->getParam('bundle_option_qty'.$product_id);

                $params = array(
                    'product' => $product_id,
                    'related_product' => null,
                    'bundle_option' => $bundle_option,
                    'bundle_option_qty' => $bundle_qty,
                    'qty' => $qty,
                );

                if ($product->getId() && $product->isVisibleInCatalog()) {
                    try {
                                                
                        $cart->addProduct($product,$params);
                         //return added product information
                        $html_response .= "<div class='one_fifth'><a href='" . $product->getProductUrl() . "'><img class='block_center' style='width:75px;height:75px;' src='" . $product->getImageUrl() . "'></img></a></div><div class='two_fifth'><a href='" . $product->getProductUrl() . "'><p class='ti_product_blockName'>" . $product->getName() . "</p></a></div><div class='one_fifth text_center'><p>".$qty."</p></div><div class='one_fifth'><p>$".$qty * $product->getPrice()."</p></div><div class='ti_cms_separator'></div>";


                        
                    } catch (Exception $e){
                        $this->__('Some of the requested products are not available in the desired quantity.');
                        $flag=1;

                    }
                } else {
                    $this->__('Some of the requested products are unavailable.');
                    $flag = 1;
                }
            
            }
        }
                
        $cart->save();

        if($flag == 0){
            Mage::getSingleton('checkout/session')->setCartWasUpdated(true);
            $this->_redirect('checkout/cart');
        }

        if ($this->getRequest()->isXmlHttpRequest()) {
            //echo $html_response;
            exit($html_response);
            //exit('2');
            //echo $html_response;
        }

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

        //check if the couponcode have , cause we do not accept it

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


            $oldCouponCodes = explode(',',$oldCouponCode);
            $allOldCoupon = null;
            foreach($oldCouponCodes as $oldCouponCode){

                if (!strlen($couponCode) && !strlen($oldCouponCode)) {
                    $this->_goBack();
                    return;
                }
                elseif(!strcmp($couponCode,$oldCouponCode) ){
                    $flag = 0;
                }
                else{
                    if(strlen($oldCouponCode)){

                        $allOldCoupon = $oldCouponCode.','.$allOldCoupon;

                    }
                }
            }

            $allOldCoupon =  $allOldCoupon.''.$couponCode;//here , is not working

            $this->_getQuote()->setCouponCode($allOldCoupon);

            if($flag){
                try {
                    $totalLength = strlen($allOldCoupon);
                    $codeLength = strlen($couponCode);
                    $isCodeLengthValid = $codeLength && $codeLength <= Mage_Checkout_Helper_Cart::COUPON_CODE_MAX_LENGTH;
                    $isValidlength = $totalLength && $totalLength <= Mage_Checkout_Helper_Cart::COUPON_CODE_MAX_LENGTH;

                    $this->_getQuote()->getShippingAddress()->setCollectShippingRates(true);
                    //$this->_getQuote()->setCouponCode($isCodeLengthValid ? $couponCode : '');

                    //try with set coupon code manually then
                    //$this->_getQuote()->setCouponCode($allOldCoupon)->save();



                    if ($codeLength) {
                        if ($isCodeLengthValid && $isValidlength && $allOldCoupon == $this->_getQuote()->getCouponCode()) {
                            if (!empty($couponCode)) {

                                $this->_getQuote()->collectTotals($couponCode);
                                $this->_getQuote()->setCouponCode($allOldCoupon)->save();


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