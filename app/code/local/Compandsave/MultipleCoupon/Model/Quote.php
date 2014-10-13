<?php
/**
 * @package: Compandsave_Sales
 * @Model: Compandsave_Sales_Model_Quote
 * @Developer: Mohammad Zahed Hossain
 * @Email: zahedh@compandave.com
 * @Date: 9/21/14
 * @Time: 7:35 PM
 * @method: Mage_Sales_Model_Quote collectTotals(string $value)
 * @method: Mage_Sales_Model_Quote _validateCouponCode()
 */
class Compandsave_MultipleCoupon_Model_Quote extends Mage_Sales_Model_Quote
{

    /**
     * Overwrite Mage_Sales_Model_Quote for custom coupon codes
     * @param string $couponcode
     * @param array $item_array for not applicable item array
     * @return Mage_Sales_Model_Quote
     */
    protected $_TotalAddressDiscount;
    protected $_BaseTotalAddressDiscount;

    /**
     * Base rounding deltas
     *
     * @var array
     */
    protected $_roundingDeltas = array();
    protected $_baseRoundingDeltas = array();

    /**
     * Quote address
     *
     * @var null|Mage_Sales_Model_Quote_Address
     */
    protected $_address = null;


    /**
     * Information about item totals for rules.
     * @var array
     */
    protected $_rulesItemTotals = array();

    /**
     * Store information about addresses which cart fixed rule applied for
     *
     * @var array
     */
    protected $_cartFixedRuleUsedForAddress = array();

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
        //$oldCouponCode = $this->getCouponCode();

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



        if(strlen($oldRuleId))
            $savedRuleId = $oldRuleId.','.$newRuleId;
        else
            $savedRuleId = $newRuleId;

        mage::log($savedRuleId);
        $this->setAppliedRuleIds(rtrim($savedRuleId,','));


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

        $appliedruleids = explode(',',$this->getAppliedRuleIds());

        foreach ($appliedruleids as $ruleid) {
            $rule = Mage::getModel('salesrule/rule')->load($ruleid);
            if (Mage_SalesRule_Model_Rule::CART_FIXED_ACTION == $rule->getSimpleAction()) {


                $validItemsCount = 0;

                foreach ($this->getAllVisibleItems() as $item) {
                    //Skipping child items to avoid double calculations
                    if ($item->getParentItemId()) {
                        continue;
                    }
                    if (!$rule->getActions()->validate($item)) {
                        continue;
                    }

                    $validItemsCount++;
                }

                $this->_rulesItemTotals[$rule->getId()] = array(
                    'items_count' => $validItemsCount,
                );
            }

        }
        mage::log($this->_rulesItemTotals);

        foreach ($this->getAllVisibleItems() as $item) {
            if ($item->getParentItem()) {
                continue;
            }
            /*
             * If in array of un applicable rule then we do not count discount for it
             */
            $item->setDiscountAmount(0);
            $item->setBaseDiscountAmount(0);
            $item->setDiscountPercent(0);

            $itemPrice              = $this->_getItemPrice($item);
            $baseItemPrice          = $this->_getItemBasePrice($item);
            $itemOriginalPrice      = $this->_getItemOriginalPrice($item);
            $baseItemOriginalPrice  = $this->_getItemBaseOriginalPrice($item);

            $discountAmount = 0;
            $baseDiscountAmount = 0;
            //discount for original price
            $originalDiscountAmount = 0;
            $baseOriginalDiscountAmount = 0;
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
            $address    = $this->_getAddress($item);

            $ruleitemid = null;
            mage::log($appliedruleid);
            foreach($appliedruleid as $ruleId){

                if(!empty($ruleId)){
                    $oRule = Mage::getModel('salesrule/rule')->load($ruleId);
                    //$ruleData = $oRule->getData();

                    //$getitemdiscountAmount = (float) $ruleData['discount_amount'];


                    /*
                     * Double check if the rule id valid if invalid do not calculate discount
                     */
                    if (!$oRule->getActions()->validate($item)){
                        continue;
                    }

                    $qty = $this->_getItemQty($item, $oRule);

                    $rulePercent = min(100, $oRule->getDiscountAmount());

                    switch ($oRule->getSimpleAction()) {
                        case Mage_SalesRule_Model_Rule::TO_PERCENT_ACTION:
                            $rulePercent = max(0, 100-$oRule->getDiscountAmount());
                        //no break;
                        case Mage_SalesRule_Model_Rule::BY_PERCENT_ACTION:
                            $step = $oRule->getDiscountStep();
                            if ($step) {
                                $qty = floor($qty/$step)*$step;
                            }
                            $_rulePct = $rulePercent/100;
                            $discountAmount    += ($qty * $itemPrice - $item->getDiscountAmount()) * $_rulePct;
                            $baseDiscountAmount += ($qty * $baseItemPrice - $item->getBaseDiscountAmount()) * $_rulePct;
                            //get discount for original price
                            $originalDiscountAmount    += ($qty * $itemOriginalPrice - $item->getDiscountAmount()) * $_rulePct;
                            $baseOriginalDiscountAmount +=
                                ($qty * $baseItemOriginalPrice - $item->getDiscountAmount()) * $_rulePct;

                            if (!$oRule->getDiscountQty() || $oRule->getDiscountQty()>$qty) {
                                $discountPercent = min(100, $item->getDiscountPercent()+$rulePercent);
                                $item->setDiscountPercent($discountPercent);
                            }
                            break;
                        case Mage_SalesRule_Model_Rule::TO_FIXED_ACTION:
                            $thisAmount = $this->getStore()->convertPrice($oRule->getDiscountAmount());
                            $discountAmount    += $qty * ($itemPrice-$thisAmount);
                            $baseDiscountAmount += $qty * ($baseItemPrice-$oRule->getDiscountAmount());
                            //get discount for original price
                            $originalDiscountAmount    += $qty * ($itemOriginalPrice-$thisAmount);
                            $baseOriginalDiscountAmount += $qty * ($baseItemOriginalPrice-$oRule->getDiscountAmount());
                            break;

                        case Mage_SalesRule_Model_Rule::BY_FIXED_ACTION:
                            $step = $oRule->getDiscountStep();
                            if ($step) {
                                $qty = floor($qty/$step)*$step;
                            }
                            $thisAmount        = $this->getStore()->convertPrice($oRule->getDiscountAmount());
                            $discountAmount     += $qty * $thisAmount;
                            $baseDiscountAmount += $qty * $oRule->getDiscountAmount();
                            break;

                        case Mage_SalesRule_Model_Rule::CART_FIXED_ACTION:

                            if (empty($this->_rulesItemTotals[$oRule->getId()])) {
                                Mage::throwException(Mage::helper('salesrule')->__('Item totals are not set for rule.'));
                            }

                             /*
                             * prevent applying whole cart discount for every shipping order, but only for first order
                             */

                            if ($this->getIsMultiShipping()) {
                                $usedForAddressId = $this->getCartFixedRuleUsedForAddress($oRule->getId());
                                if ($usedForAddressId && $usedForAddressId != $address->getId()) {
                                    break;
                                } else {
                                    $this->setCartFixedRuleUsedForAddress($oRule->getId(), $address->getId());
                                }
                            }

                            $maximumItemDiscount = (float) ( $oRule->getDiscountAmount() / $this->_rulesItemTotals[$oRule->getId()]['items_count'] );
                            $thisAmount = $this->getStore()->convertPrice($maximumItemDiscount);

                            $currentbaseDiscountAmount = min($baseItemPrice * $qty, $maximumItemDiscount);

                            $currentdiscountAmount = min($itemPrice * $qty, $thisAmount);

                            $discountAmount += $this->getStore()->convertPrice($currentdiscountAmount);

                            $baseDiscountAmount += $this->getStore()->convertPrice($currentbaseDiscountAmount);

                            //get discount for original price
                            $originalDiscountAmount += min($itemOriginalPrice * $qty, $thisAmount);
                            $baseOriginalDiscountAmount += $this->getStore()->convertPrice($baseItemOriginalPrice);



                            break;


                        case Mage_SalesRule_Model_Rule::BUY_X_GET_Y_ACTION:
                            $x = $oRule->getDiscountStep();
                            $y = $oRule->getDiscountAmount();
                            if (!$x || $y > $x) {
                                break;
                            }
                            $buyAndDiscountQty = $x + $y;

                            $fullRuleQtyPeriod = floor($qty / $buyAndDiscountQty);
                            $freeQty  = $qty - $fullRuleQtyPeriod * $buyAndDiscountQty;

                            $discountQty = $fullRuleQtyPeriod * $y;
                            if ($freeQty > $x) {
                                $discountQty += $freeQty - $x;
                            }

                            $discountAmount    += $discountQty * $itemPrice;
                            $baseDiscountAmount += $discountQty * $baseItemPrice;
                            //get discount for original price
                            $originalDiscountAmount    += $discountQty * $itemOriginalPrice;
                            $baseOriginalDiscountAmount += $discountQty * $baseItemOriginalPrice;
                            break;
                    }

                    $result = new Varien_Object(array(
                        'discount_amount'      => $discountAmount,
                        'base_discount_amount' => $baseDiscountAmount,
                    ));
                    Mage::dispatchEvent('salesrule_validator_process', array(
                        'rule'    => $oRule,
                        'item'    => $item,
                        'address' => $address,
                        'quote'   => $this,
                        'qty'     => $qty,
                        'result'  => $result,
                    ));

                    $discountAmount = $result->getDiscountAmount();
                    $baseDiscountAmount = $result->getBaseDiscountAmount();

                    $percentKey = $item->getDiscountPercent();
                    /**
                     * Process "delta" rounding
                     */
                    if ($percentKey) {
                        $delta      = isset($this->_roundingDeltas[$percentKey]) ? $this->_roundingDeltas[$percentKey] : 0;
                        $baseDelta  = isset($this->_baseRoundingDeltas[$percentKey])
                            ? $this->_baseRoundingDeltas[$percentKey]
                            : 0;
                        $discountAmount += $delta;
                        $baseDiscountAmount += $baseDelta;

                        $this->_roundingDeltas[$percentKey]     = $discountAmount -
                            $this->getStore()->convertPrice($discountAmount);
                        $this->_baseRoundingDeltas[$percentKey] = $baseDiscountAmount -
                            $this->getStore()->convertPrice($baseDiscountAmount);
                        $discountAmount = $this->getStore()->convertPrice($discountAmount);
                        $baseDiscountAmount = $this->getStore()->convertPrice($baseDiscountAmount);
                    } else {
                        $discountAmount     = $this->getStore()->convertPrice($discountAmount);
                        $baseDiscountAmount = $this->getStore()->convertPrice($baseDiscountAmount);
                    }

                    $ruleitemid = $ruleId.','.$ruleitemid;

                    $checkRuleItem[] = $ruleId;


                }

            }
            /*
             * Set applied rule id for each item after collect address total
             */
            $itemruleIds[$item->getId()] = rtrim($ruleitemid,',');
            $item->setAppliedRuleIds(rtrim($ruleitemid,','));
            /*
             * Set Item Total and Base discount for all applicable rule ID
             */
            $itemDiscountAmount = $item->getDiscountAmount();
            $itemBaseDiscountAmount = $item->getBaseDiscountAmount();

            $discountAmount     = min($itemDiscountAmount + $discountAmount, $itemPrice * $qty);
            $baseDiscountAmount = min($itemBaseDiscountAmount + $baseDiscountAmount, $baseItemPrice * $qty);

            $discount[$item->getId()] = (float) $discountAmount;
            $basediscount[$item->getId()] = (float) $baseDiscountAmount;
            mage::log($item->getId());
            mage::log($discountAmount);
            /*
             * For accurate tax calculation need to set discount here
             */
            $item->setDiscountAmount((float) $discountAmount);
            $item->setBaseDiscountAmount((float) $baseDiscountAmount);

            $this->_TotalAddressDiscount = (float) $this->_TotalAddressDiscount + (float) $discountAmount;
            $this->_BaseTotalAddressDiscount = (float) $this->_BaseTotalAddressDiscount + (float) $baseDiscountAmount;


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
                $savedRuleId = rtrim($savedRuleId,',');
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

        return $this;

    }

    /*
     * Validate coupon is for the address and has coupon
     */
    protected function _validateCouponCode()
    {

        $code = Mage::getSingleton('checkout/session')->getData('coupon_code');
        if (strlen($code)) {
            $addressHasCoupon = false;
            $addresses = $this->getAllAddresses();
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

    /**
     * Return item price
     *
     * @param Mage_Sales_Model_Quote_Item_Abstract $item
     * @return float
     */
    protected function _getItemPrice($item)
    {
        $price = $item->getDiscountCalculationPrice();
        $calcPrice = $item->getCalculationPrice();
        return ($price !== null) ? $price : $calcPrice;
    }

    /**
     * Return item original price
     *
     * @param Mage_Sales_Model_Quote_Item_Abstract $item
     * @return float
     */
    protected function _getItemOriginalPrice($item)
    {
        return Mage::helper('tax')->getPrice($item, $item->getOriginalPrice(), true);
    }

    /**
     * Return item base price
     *
     * @param Mage_Sales_Model_Quote_Item_Abstract $item
     * @return float
     */
    protected function _getItemBasePrice($item)
    {
        $price = $item->getDiscountCalculationPrice();
        return ($price !== null) ? $item->getBaseDiscountCalculationPrice() : $item->getBaseCalculationPrice();
    }

    /**
     * Return item base original price
     *
     * @param Mage_Sales_Model_Quote_Item_Abstract $item
     * @return float
     */
    protected function _getItemBaseOriginalPrice($item)
    {
        return Mage::helper('tax')->getPrice($item, $item->getBaseOriginalPrice(), true);
    }
    /**
     * Set information about usage cart fixed rule by quote address
     *
     * @param int $ruleId
     * @param int $itemId
     * @return void
     */
    public function setCartFixedRuleUsedForAddress($ruleId, $itemId)
    {
        $this->_cartFixedRuleUsedForAddress[$ruleId] = $itemId;
    }

    /**
     * Retrieve information about usage cart fixed rule by quote address
     *
     * @param int $ruleId
     * @return int|null
     */
    public function getCartFixedRuleUsedForAddress($ruleId)
    {
        if (isset($this->_cartFixedRuleUsedForAddress[$ruleId])) {
            return $this->_cartFixedRuleUsedForAddress[$ruleId];
        }
        return null;
    }
    /*
     * Get Item Discount qty
     */
    protected function _getItemQty($item, $rule)
    {
        $qty = $item->getTotalQty();
        return $rule->getDiscountQty() ? min($qty, $rule->getDiscountQty()) : $qty;
    }
    /*
     * Get address for quote items
     */
    protected function _getAddress(Mage_Sales_Model_Quote_Item_Abstract $item)
    {
        if ($item instanceof Mage_Sales_Model_Quote_Address_Item) {
            $address = $item->getAddress();
        } elseif ($this->_address) {
            $address = $this->_address;
        } elseif ($item->getQuote()->getItemVirtualQty() > 0) {
            $address = $item->getQuote()->getBillingAddress();
        } else {
            $address = $item->getQuote()->getShippingAddress();
        }
        return $address;
    }
    /*
     * Check the rule can process by the address
     */
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