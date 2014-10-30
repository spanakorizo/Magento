<?php
/**
 * @package: Compandsave_Sales
 * @Model: Compandsave_Sales_Model_Quote
 * @Developer: Mohammad Zahed Hossain
 * @Email: zahedh@compandave.com
 * @Date: 9/21/14
 * @Time: 7:35 PM
 * @known bugs: Do not add auto discount in the middle of transaction
 * @method: Mage_Sales_Model_Quote collectTotals(string $value)
 * @method: Mage_Sales_Model_Quote _validateCouponCode()
 */
class Compandsave_MultipleCoupon_Model_Quote extends Mage_Sales_Model_Quote
{

    /**
     * Overwrite Mage_Sales_Model_Quote for custom coupon codes
     * @param string $couponcode
     * @param array $item_array for not applicable item array
     * @param string $newRuleId
     * @param int $discountableitem for applicable item quantity
     * @return Mage_Sales_Model_Quote
     */
    protected $_TotalAddressDiscount;
    protected $_BaseTotalAddressDiscount;

    public function collectTotals($couponcode = null,$item_array = '')
    {
        /*
         * If total collected flag true no need to update return Mage_Sales_Model_Quote
         */
        if ($this->getTotalsCollectedFlag()) {
            return $this;
        }
        $this->_TotalAddressDiscount = 0.0000;
        $this->_BaseTotalAddressDiscount = 0.0000;


        Mage::dispatchEvent($this->_eventPrefix . '_collect_totals_before', array($this->_eventObject => $this));
        /*
         * all applied coupon code and rule Id
         */

        $newRuleId = null;
        $newitemRuleId = null;

        $oldRuleId = $this->getAppliedRuleIds();
        $oldCouponCode = $this->getCouponCode();

        if(!empty($couponcode)){
            $oCoupon = Mage::getModel('salesrule/coupon')->load($couponcode, 'code');

            if(!empty($oCoupon)){
                $oRule = Mage::getModel('salesrule/rule')->load($oCoupon->getRuleId());
                $ruleData = $oRule->getData();
                $newRuleId = $ruleData['rule_id'];
                //$newDescription = $ruleData['description'];
                $newitemRuleId = $ruleData['rule_id'];

            }
        }

        /*if($discountableitem == 9999)
            $discountableitem = min($discountableitem, $this->getItemsQty());*/


        //Mage::log($discountableitem);

        if(strlen($oldRuleId) and FALSE === strpos($oldRuleId,$newRuleId) )
            $savedRuleId = $oldRuleId.','.$newRuleId;
        else
            $savedRuleId = $newRuleId;

        /*if(strlen($oldCouponCode))
            $allcouponcode = $oldCouponCode.','.$couponcode;
        else
            $allcouponcode = $couponcode;


        $this->setCouponCode(rtrim($allcouponcode,','));*/

        $this->setAppliedRuleIds(strrev(rtrim($savedRuleId,',')));

        $canAddItems = $this->isVirtual()? ('billing') : ('shipping');

        $this->getSubtotal(0);
        $this->getBaseSubtotal(0);
        $this->setBaseGiftCardsAmountUsed(0);
        $this->setGiftCardsAmountUsed(0);

        $this->setItemsCount(0);
        $this->setItemsQty(0);
        $this->setVirtualItemsQty(0);

        foreach ($this->getAllVisibleItems() as $item) {
            if ($item->getParentItem()) {
                continue;
            }
            $children = $item->getChildren();
            if ($children && $item->isShipSeparately()) {
                foreach ($children as $child) {
                    if ($child->getProduct()->getIsVirtual()) {
                        $this->setVirtualItemsQty($this->getVirtualItemsQty() + $child->getQty()*$item->getQty());
                    }
                }
            }

            if ($item->getProduct()->getIsVirtual()) {
                $this->setVirtualItemsQty($this->getVirtualItemsQty() + $item->getQty());
            }
            $this->setItemsCount($this->getItemsCount()+1);
            $this->setItemsQty((float) $this->getItemsQty()+$item->getQty());
        }

        $discountableitem = $this->getItemsQty();

        foreach ($this->getAllVisibleItems() as $item) {
            if ($item->getParentItem()) {
                continue;
            }
            /*
             * If in array of un applicable rule then we do not count discount for it
             */

            $discountItem = 0.0000;
            $basediscountItem = 0.0000;
            /*
             * Determine which rule ids are applicable for newly added item
             */
            $olditemRuleId = $item->getAppliedRuleIds();

            if(!empty($olditemRuleId)){

                $saveditemRuleId = $olditemRuleId.','.$newitemRuleId;
            }
            elseif(!empty($oldRuleId) and empty($olditemRuleId)){
                /*
                 * Ensure Newly added item have all applicable discount
                 */
                $saveditemRuleId = $oldRuleId.','.$newitemRuleId;
            }
            else{
                $saveditemRuleId = $newitemRuleId;
            }

            /*
             * Put rule id based on valid on invalid
             */
            if(in_array($item->getId(),$item_array)){

                $item->setAppliedRuleIds(strrev(rtrim($olditemRuleId,',')));

            }
            elseif((empty($couponcode) or empty($newitemRuleId)) and !empty($olditemRuleId)){
                /*
                 * Ensure Newly added item have all applicable discount by check !empty($olditemRuleId
                 */
                $item->setAppliedRuleIds(strrev(rtrim($olditemRuleId,',')));
            }
            else{

                $item->setAppliedRuleIds(strrev(rtrim($saveditemRuleId,',')));

            }

            /*
             * Get Applied rule Id for item
             */
            $appliedruleid = explode(',',$item->getAppliedRuleIds());
            $appliedruleid = array_unique($appliedruleid);//prevent duplicate rule id
            /*
             * Set Initital discount amount is zero for rule based calculation
             */

            $qty = (float) $item->getQty();

            $ruleitemid = null;

            foreach($appliedruleid as $ruleId){

                if(!empty($ruleId)){
                    $oRule = Mage::getModel('salesrule/rule')->load($ruleId);
                    $ruleData = $oRule->getData();

                    $getitemdiscountAmount = (float) $ruleData['discount_amount'];


                    /*
                     * Double check if the rule id valid if invalid do not calculate discount
                     */
                    if (!$oRule->getActions()->validate($item)){
                        continue;
                    }

                    $actiontype = $ruleData['simple_action'];
                    //$discountStep = $ruleData['discount_step'];///////////new to add code


                    if($actiontype === 'by_percent'){
                        /*
                         * add code here for step
                         */
                        $step = $oRule->getDiscountStep();
                        if ($step) {
                            $qty = floor($qty/$step)*$step;
                        }
                        $discountItem += (float) ($qty * ($item->getPrice() * $getitemdiscountAmount / 100));
                        $basediscountItem += (float) ($qty * ($item->getBasePrice() * $getitemdiscountAmount / 100));
                        //need to handle if the discount is applicable by filter


                    }
                    elseif($actiontype === 'cart_fixed'){ //total deduct from cart
                        if($discountableitem == 0){
                            $discountItem += 0;
                            $basediscountItem += 0;
                        }else{
                            $discountItem += (float) ( $getitemdiscountAmount / $discountableitem ) * $qty;
                            $basediscountItem += (float) ( $getitemdiscountAmount / $discountableitem ) * $qty;
                        }

                    }
                    elseif($actiontype === 'by_fixed'){ //fixed amount for qty

                        $discountItem += (float) ($qty * $getitemdiscountAmount);
                        $basediscountItem += (float) ($qty * $getitemdiscountAmount);
                        //need to handle if the discount is applicable by filter

                    }
                    //pending action type buy_x_get_y if buy X qty then get Y amount discount

                    /*
                     * Double triple check rule ids
                     */
                    $ruleitemid = $ruleId.','.$ruleitemid;

                    $checkRuleItem[] = $ruleId;

                    unset($oRule);
                }

            }
            /*
             * Set applied rule id for each item after collect address total
             */
            $itemruleIds[$item->getId()] = strrev(rtrim($ruleitemid,','));
            $item->setAppliedRuleIds(strrev(rtrim($ruleitemid,',')));
            /*
             * Set Item Total and Base discount for all applicable rule ID
             */
            $discount[$item->getId()] = (float) $discountItem;
            $basediscount[$item->getId()] = (float) $basediscountItem;

            /*
             * For accurate tax calculation need to set discount here
             */
            $item->setDiscountAmount((float) $discountItem);
            $item->setBaseDiscountAmount((float) $basediscountItem);

            $this->_TotalAddressDiscount = (float) $this->_TotalAddressDiscount + (float) $discountItem;
            $this->_BaseTotalAddressDiscount = (float) $this->_BaseTotalAddressDiscount + (float) $basediscountItem;


        }

        foreach ($this->getAllAddresses() as $address) {

            $address->setSubtotal(0);
            $address->setBaseSubtotal(0);

            $address->setGrandTotal(0);
            $address->setBaseGrandTotal(0);


            if($canAddItems == $address->getAddressType()){


                $address->collectTotals();

                $this->setSubtotal((float) $address->getSubtotal());
                $this->setBaseSubtotal((float) $address->getBaseSubtotal());



                $checkRuleItem = array_unique($checkRuleItem);
                $savedRuleId = strrev(rtrim($savedRuleId,','));
                $savedRuleIds = explode(',',$savedRuleId);
                $diff_rule_ids = array_diff($savedRuleIds, $checkRuleItem);
                $savedRuleIds = array_diff($savedRuleIds, $diff_rule_ids);
                $this->setAppliedRuleIds(implode(",", $savedRuleIds));
                $oCoupon = null;
                $oDescripttion = null;

                foreach($savedRuleIds as $saveRuleId){
                    $oRule = Mage::getModel('salesrule/rule')->load($saveRuleId);
                    $oCoupon = $oRule->getCouponCode().','.$oCoupon;
                    $oDescripttion = $oRule->getDescription().','.$oDescripttion;
                    unset($oRule);
                }
                $this->setCouponCode(rtrim($oCoupon,','));
                $address->setDiscountDescription(rtrim($oDescripttion,','));
                /*
                 * In grand total we have all but as we implement multiple coupon we should use own discount Amount and
                 * For Gift card as it goes negative if multiple discount applied and gift card has more than subtotal balance
                 * so that we add add here cause grandtotal already calculate gift card amount
                 */
                $discountflag = 0;


                $allcouponcode = $this->getCouponCode();

                if(strpos($this->getCouponCode(),',') === FALSE and $this->getCouponCode()!= null){

                    $grandtotalwithgiftcards = $address->getGrandTotal() + $address->getGiftCardsAmount();

                    if($address->getGiftCardsAmount() >= $grandtotalwithgiftcards ){
                        $grandTotal = (float) $address->getGrandTotal() + $address->getGiftCardsAmount();
                        $basegrandTotal = (float)  $address->getBaseGrandTotal() + $address->getBaseGiftCardsAmount();
                        $subtotalwithdiscount = $address->getSubtotal();
                        $basesubtotalwithdiscount = $address->getBaseSubtotal();
                    }
                    elseif($address->getGiftCardsAmount() < $grandtotalwithgiftcards ){
                        $grandTotal = (float) $address->getGrandTotal() ;
                        $basegrandTotal = (float)  $address->getBaseGrandTotal();
                        $subtotalwithdiscount = $address->getSubtotal();
                        $basesubtotalwithdiscount = $address->getBaseSubtotal();
                    }
                }
                elseif(!strpos($this->getCouponCode(),',') === FALSE and $this->getCouponCode()!= null){

                    $grandtotalwithoutdiscount = $address->getGrandTotal() - $this->_TotalAddressDiscount + $address->getGiftCardsAmount();

                    if($address->getGiftCardsAmount() >=   $grandtotalwithoutdiscount){
                        $grandTotal = (float) $address->getGrandTotal() - $this->_TotalAddressDiscount + $address->getGiftCardsAmount();
                        $basegrandTotal = (float)  $address->getBaseGrandTotal() - $this->_BaseTotalAddressDiscount + $address->getGiftCardsAmount();
                        $subtotalwithdiscount = $address->getSubtotal() - $this->_TotalAddressDiscount;
                        $basesubtotalwithdiscount = $address->getBaseSubtotal() - $this->_BaseTotalAddressDiscount;
                        $address->setDiscountAmount(-(float) ($this->_TotalAddressDiscount));
                        $address->setBaseDiscountAmount(- (float) ($this->_BaseTotalAddressDiscount));
                        $discountflag = 1;
                    }
                    elseif($address->getGiftCardsAmount() <   $grandtotalwithoutdiscount){
                        $grandTotal = (float) $address->getGrandTotal() - $this->_TotalAddressDiscount;
                        $basegrandTotal = (float)  $address->getBaseGrandTotal() - $this->_BaseTotalAddressDiscount;
                        $subtotalwithdiscount = $address->getSubtotal() - $this->_TotalAddressDiscount;
                        $basesubtotalwithdiscount = $address->getBaseSubtotal() - $this->_BaseTotalAddressDiscount;
                        $address->setDiscountAmount(-(float) ($this->_TotalAddressDiscount));
                        $address->setBaseDiscountAmount(- (float) ($this->_BaseTotalAddressDiscount));
                        $discountflag = 1;
                    }
                }
                elseif(empty($allcouponcode)){

                    $grandtotalwithgiftcards = $address->getGrandTotal() + $address->getGiftCardsAmount();

                    if($address->getGiftCardsAmount() >= $grandtotalwithgiftcards ){

                        $grandTotal = (float) $address->getGrandTotal() + $address->getGiftCardsAmount();
                        $basegrandTotal = (float)  $address->getBaseGrandTotal() + $address->getBaseGiftCardsAmount();
                        $subtotalwithdiscount = $address->getSubtotal();
                        $basesubtotalwithdiscount = $address->getBaseSubtotal();
                        $discountflag = 2;
                    }else{
                        $grandTotal = (float) $address->getGrandTotal() ;
                        $basegrandTotal = (float)  $address->getBaseGrandTotal();
                        $subtotalwithdiscount = $address->getSubtotal();
                        $basesubtotalwithdiscount = $address->getBaseSubtotal();
                    }

                }
                else{
                    $grandTotal = (float) $address->getGrandTotal() ;
                    $basegrandTotal = (float)  $address->getBaseGrandTotal();
                    $subtotalwithdiscount = $address->getSubtotal();
                    $basesubtotalwithdiscount = $address->getBaseSubtotal();
                }

                /*
                 * handle gift card calculation and protect grand total to zero if gift card applied amount
                 * is greater than grand total
                 */

                if((ceil($grandtotalwithgiftcards - $address->getGiftCardsAmount()) <= 0 and $discountflag == 0 ) || (($grandtotalwithoutdiscount - $address->getGiftCardsAmount()) <= 0 and $discountflag == 1) || ( $discountflag == 2 and ceil($grandtotalwithgiftcards - $address->getGiftCardsAmount()) <= 0)){

                    /*
                     * unserialized giftcard data from Mage_Sales_Quote_Address resource model
                     */
                    $array = unserialize($address->getGiftCards());

                    $cards[] = array(
                        // id
                        'i' => $array[0][i],
                        // code
                        'c' => $array[0][c],
                        // amount
                        'a' => (float) $grandTotal,
                        // base amount
                        'ba' => (float) $grandTotal,
                    );

                    $address->setGiftCardsAmount((float) $grandTotal);
                    $address->setGiftCards(serialize($cards));
                    $address->setUsedGiftCards(serialize($cards));
                    $this->setGiftCardsAmountUsed((float) $grandTotal);
                    $address->setBaseGiftCardsAmount((float) $basegrandTotal);
                    $this->setBaseGiftCardsAmountUsed((float) $basegrandTotal);
                    $grandTotal = $grandTotal - $address->getBaseGiftCardsAmount();
                    $basegrandTotal = $basegrandTotal - $address->getBaseGiftCardsAmount();
                }


                //=========set Quote Total ==================//
                //Grand total
                $this->setGrandTotal((float) $grandTotal);
                $this->setBaseGrandTotal((float) $basegrandTotal);

                //set Sub total with discount
                $this->setSubtotalWithDiscount((float) $subtotalwithdiscount);
                $this->setBaseSubtotalWithDiscount((float) $basesubtotalwithdiscount);



                //============set quote shipping address resource model value ==========//
                $address->setGrandTotal((float) $grandTotal);
                $address->setBaseGrandTotal((float) $basegrandTotal);

            }

        }

        $this->setItemsCount(0);
        $this->setItemsQty(0);
        $this->setVirtualItemsQty(0);

        foreach ($this->getAllVisibleItems() as $item) {
            if ($item->getParentItem()) {
                continue;
            }
            /*
             * If in array of un applicable rule then we do not count discount for it
             */

            $children = $item->getChildren();
            if ($children && $item->isShipSeparately()) {
                foreach ($children as $child) {
                    if ($child->getProduct()->getIsVirtual()) {
                        $this->setVirtualItemsQty($this->getVirtualItemsQty() + $child->getQty()*$item->getQty());
                    }
                }
            }

            if ($item->getProduct()->getIsVirtual()) {
                $this->setVirtualItemsQty($this->getVirtualItemsQty() + $item->getQty());
            }
            $this->setItemsCount($this->getItemsCount()+1);
            $this->setItemsQty((float) $this->getItemsQty()+$item->getQty());

            $item->setAppliedRuleIds($itemruleIds[$item->getId()]);

            $item->setDiscountAmount((float) $discount[$item->getId()]);
            $item->setBaseDiscountAmount((float) $basediscount[$item->getId()]);

        }

        Mage::helper('sales')->checkQuoteAmount($this, $this->getGrandTotal());
        Mage::helper('sales')->checkQuoteAmount($this, $this->getBaseGrandTotal());

        $this->setData('trigger_recollect', 0);//it was 0

        $this->_validateCouponCode();

        Mage::dispatchEvent($this->_eventPrefix . '_collect_totals_after', array($this->_eventObject => $this));

        $this->setTotalsCollectedFlag(true);
        $this->setGiftCardsTotalCollected(true);
        echo memory_get_usage();
        return $this;
    }

    protected function _validateCouponCode()
    {

        $code = Mage::getSingleton('checkout/session')->getData('coupon_code');
        if (strlen($code)) {
            $addressHasCoupon = false;
            $addresses = $quote->getAllAddresses();
            if (count($addresses)>0) {
                foreach ($addresses as $address) {
                    if ($address->hasCouponCode()) {
                        $addressHasCoupon = true;
                    }
                }
                if (!$addressHasCoupon) {
                    $this->setCouponCode('');
                }
            }
        }
        return $this;
    }

    public function _canProcessRule($rule, $address)
    {
        if ($rule->hasIsValidForAddress($address) && !$address->isObjectNew()) {
            return $rule->getIsValidForAddress($address);
        }

        /**
         * check per coupon usage limit
         */
        if ($rule->getCouponType() != Mage_SalesRule_Model_Rule::COUPON_TYPE_NO_COUPON) {
            $couponCode = $address->getQuote()->getCouponCode();
            if (strlen($couponCode)) {
                $coupon = Mage::getModel('salesrule/coupon');
                $coupon->load($couponCode, 'code');
                if ($coupon->getId()) {
                    // check entire usage limit
                    if ($coupon->getUsageLimit() && $coupon->getTimesUsed() >= $coupon->getUsageLimit()) {
                        $rule->setIsValidForAddress($address, false);
                        return false;
                    }
                    // check per customer usage limit
                    $customerId = $address->getQuote()->getCustomerId();
                    if ($customerId && $coupon->getUsagePerCustomer()) {
                        $couponUsage = new Varien_Object();
                        Mage::getResourceModel('salesrule/coupon_usage')->loadByCustomerCoupon(
                            $couponUsage, $customerId, $coupon->getId());
                        if ($couponUsage->getCouponId() &&
                            $couponUsage->getTimesUsed() >= $coupon->getUsagePerCustomer()
                        ) {
                            $rule->setIsValidForAddress($address, false);
                            return false;
                        }
                    }
                }
            }
        }

        /**
         * check per rule usage limit
         */
        $ruleId = $rule->getId();
        if ($ruleId && $rule->getUsesPerCustomer()) {
            $customerId     = $address->getQuote()->getCustomerId();
            $ruleCustomer   = Mage::getModel('salesrule/rule_customer');
            $ruleCustomer->loadByCustomerRule($customerId, $ruleId);
            if ($ruleCustomer->getId()) {
                if ($ruleCustomer->getTimesUsed() >= $rule->getUsesPerCustomer()) {
                    $rule->setIsValidForAddress($address, false);
                    return false;
                }
            }
        }
        $rule->afterLoad();
        /**
         * quote does not meet rule's conditions
         */
        if (!$rule->validate($address)) {
            $rule->setIsValidForAddress($address, false);
            return false;
        }
        /**
         * passed all validations, remember to be valid
         */
        $rule->setIsValidForAddress($address, true);
        return true;
    }
}