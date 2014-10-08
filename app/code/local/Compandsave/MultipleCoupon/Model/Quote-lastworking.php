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
    protected $_TotalAddressDiscount = 0;
    protected $_BaseTotalAddressDiscount = 0;

    public function collectTotals($couponcode = null, $item_array = null, $discountableitem = 0)
    {
        /*
         * If total collected flag true no need to update return Mage_Sales_Model_Quote
         */
        if ($this->getTotalsCollectedFlag()) {
            return $this;
        }
        if($discountableitem == 0)
            $discountableitem = max($discountableitem, $this->getItemsQty());
        Mage::dispatchEvent($this->_eventPrefix . '_collect_totals_before', array($this->_eventObject => $this));

        /*
         * all applied coupon code and rule Id
         */
        $oldRuleId = $this->getAppliedRuleIds();
        $oldCouponCode = $this->getCouponCode();
        if(!empty($couponcode)){
            $oCoupon = Mage::getModel('salesrule/coupon')->load($couponcode, 'code');
            if(!empty($oCoupon)){
                $oRule = Mage::getModel('salesrule/rule')->load($oCoupon->getRuleId());
                $ruleData = $oRule->getData();
                $newRuleId = $ruleData['rule_id'];
                $newDescription = $ruleData['description'];
            }
        }


        if(strlen($oldRuleId))
            $savedRuleId = $oldRuleId.','.$newRuleId;
        else
            $savedRuleId = $newRuleId;

        if(strlen($oldCouponCode))
            $allcouponcode = $oldCouponCode.','.$couponcode;
        else
            $allcouponcode = $couponcode;


        $this->setCouponCode(rtrim($allcouponcode,','));

        $this->setAppliedRuleIds(strrev(rtrim($savedRuleId,',')));

        $canAddItems = $this->isVirtual()? ('billing') : ('shipping');

        $this->getSubtotal(0);
        $this->getBaseSubtotal(0);
        $this->setBaseGiftCardsAmountUsed(0);
        $this->setGiftCardsAmountUsed(0);


        mage::log($item_array);
        foreach ($this->getAllVisibleItems() as $item) {
            if ($item->getParentItem()) {
                continue;
            }
            /*
             * If in array of un applicable rule then we do not count discount for it
             */

            $discountItem = 0;
            $basediscountItem = 0;

            if(in_array($item->getId(),$item_array)){

                $item->setAppliedRuleIds(strrev(rtrim($oldRuleId,',')));


            }elseif(empty($couponcode) or empty($newRuleId)){
                $item->setAppliedRuleIds(strrev(rtrim($oldRuleId,',')));
            }
            else{

                $item->setAppliedRuleIds(strrev(rtrim($savedRuleId,',')));

            }

            /*
             * Get Applied rule Id for item
             */
            $appliedruleid = explode(',',$item->getAppliedRuleIds());
            /*
             * Set Initital discount amount is zero for rule based calculation
             */

            $qty = $item->getQty();


            foreach($appliedruleid as $ruleId){

                if(!empty($ruleId)){
                    $oRule = Mage::getModel('salesrule/rule')->load($ruleId);
                    $ruleData = $oRule->getData();

                    $getitemdiscountAmount = $ruleData['discount_amount'];


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
                        $discountItem += (float) ( $getitemdiscountAmount / $discountableitem ) * $qty;
                        $basediscountItem += (float) ( $getitemdiscountAmount / $discountableitem ) * $qty;
                    }
                    elseif($actiontype === 'by_fixed'){ //fixed amount for qty

                        $discountItem += (float) ($qty * $getitemdiscountAmount);
                        $basediscountItem += (float) ($qty * $getitemdiscountAmount);
                        //need to handle if the discount is applicable by filter

                    }
                    //pending action type buy_x_get_y if buy X qty then get Y amount discount
                }

            }
            /*
             * Set Item Total and Base discount for all applicable rule ID
             */

            $discount[$item->getId()] = $discountItem;
            $basediscount[$item->getId()] = $basediscountItem;

            $this->_TotalAddressDiscount = $this->_TotalAddressDiscount + $discountItem;
            $this->_BaseTotalAddressDiscount = $this->_BaseTotalAddressDiscount + $basediscountItem;

        }

        foreach ($this->getAllAddresses() as $address) {

            $address->setSubtotal(0);
            $address->setBaseSubtotal(0);

            $address->setGrandTotal(0);
            $address->setBaseGrandTotal(0);


            if($canAddItems == $address->getAddressType()){

                $fullDescripttion = $address->getDiscountDescription();

                $address->collectTotals();


                $this->setSubtotal((float) $address->getSubtotal());
                $this->setBaseSubtotal((float) $address->getBaseSubtotal());

                $this->setCouponCode(rtrim($allcouponcode,','));

                $this->setAppliedRuleIds(strrev(rtrim($savedRuleId,',')));

                if(strlen($address->getDiscountDescription())){
                    if(!strlen($newDescription)){
                        //if no description found
                        $fullDescripttion = $couponcode.','.$fullDescripttion;
                    }
                    else
                    {
                        $fullDescripttion = $newDescription.','.$fullDescripttion;

                    }
                }
                if(strlen($fullDescripttion))
                    $address->setDiscountDescription(rtrim($fullDescripttion,','));


                $grandTotal = $address->getSubtotal() + $address->getTaxAmount() + $address->getShippingAmount() + $address->getShippingTaxAmount() - $address->getShippingDiscountAmount() - $this->_TotalAddressDiscount ;
                $basegrandTotal = $address->getBaseSubtotal() + $address->getBaseTaxAmount() + $address->getBaseShippingAmount() + $address->getBaseShippingTaxAmount() - $address->getBaseShippingDiscountAmount() - $this->_BaseTotalAddressDiscount ;

                /*
                 * handle gift card calculation and protect grand total to zero if gift card applied amount
                 * is greater than grand total
                 */

                if(($grandTotal - $address->getGiftCardsAmount()) < 0 ){

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
                else{
                    $grandTotal = $grandTotal - $address->getGiftCardsAmount();
                    $basegrandTotal = $basegrandTotal - $address->getGiftCardsAmount();
                }

                $subtotalwithdiscount = $address->getSubtotal() - $this->_TotalAddressDiscount;
                $basesubtotalwithdiscount = $address->getBaseSubtotal() - $this->_BaseTotalAddressDiscount;

                //$subtotalwithTax = $address->getSubtotal() + $address->getTaxAmount() + $address->getShippingTaxAmount();
                //$BasesubtotalwithTax = $address->getBaseSubtotal() + $address->getBaseTaxAmount() + $address->getBaseShippingTaxAmount();

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

                //$address->setSubtotalInclTax($subtotalwithTax);
                //$address->setSubtotalInclTax($BasesubtotalwithTax);


                $address->setDiscountAmount(-($this->_TotalAddressDiscount));
                $address->setBaseDiscountAmount(- ($this->_BaseTotalAddressDiscount));

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

            if(in_array($item->getId(),$item_array)){

                $item->setAppliedRuleIds(strrev(rtrim($oldRuleId,',')));

            }elseif(empty($couponcode) or empty($newRuleId)){
                $item->setAppliedRuleIds(strrev(rtrim($oldRuleId,',')));
            }
            else{

                $item->setAppliedRuleIds(strrev(rtrim($savedRuleId,',')));

            }
            /*
             * Item for discount
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

            $item->setDiscountAmount($discount[$item->getId()]);
            $item->setBaseDiscountAmount($basediscount[$item->getId()]);


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
}