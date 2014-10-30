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
     * @return Mage_Sales_Model_Quote
     */

    public function collectTotals($couponcode = null)
    {
        /*
         * If total collected flag true no need to update return Mage_Sales_Model_Quote
         */
        if ($this->getTotalsCollectedFlag()) {
            return $this;
        }

        Mage::dispatchEvent($this->_eventPrefix . '_collect_totals_before', array($this->_eventObject => $this));



        if(strlen($couponcode)){
            $oCoupon = Mage::getModel('salesrule/coupon')->load($couponcode, 'code');
            if(!empty($oCoupon)){
                $oRule = Mage::getModel('salesrule/rule')->load($oCoupon->getRuleId());
                $ruleData = $oRule->getData();
                //must need to retrive onl and new rul from system apply before calculation of discount
                $newRuleId = $ruleData['rule_id'];

            }

        }
        else{
            $newRuleId = null;
        }

        $oldRuleId = $this->getAppliedRuleIds();
        $oldCouponCode = $this->getCouponCode();

        $canAddItems = $this->isVirtual()? ('billing') : ('shipping');

        $this->getSubtotal(0);
        $this->getBaseSubtotal(0);
        $this->setBaseGiftCardsAmountUsed(0);
        $this->setGiftCardsAmountUsed(0);

        foreach ($this->getAllAddresses() as $address) {

            $address->setSubtotal(0);
            $address->setBaseSubtotal(0);

            $address->setGrandTotal(0);
            $address->setBaseGrandTotal(0);


            if($canAddItems == $address->getAddressType()){

                $address->collectTotals();


                $this->setSubtotal((float) $address->getSubtotal());
                $this->setBaseSubtotal((float) $address->getBaseSubtotal());

                if(strlen($oldRuleId))
                    $this->setAppliedRuleIds($oldRuleId.','.$newRuleId);
                else
                    $this->setAppliedRuleIds($newRuleId);

                if(strlen($oldCouponCode))
                    $allcouponcode = $oldCouponCode.','.$couponcode;
                else
                    $allcouponcode = $couponcode;

                $appliedruleid = explode(',',$this->getAppliedRuleIds());
                $totaldiscount = 0;
                $basetotaldiscount = 0;
                $fullDescripttion = null;
                $savedRuleId = null;
                foreach($appliedruleid as $ruleId){

                    if(!empty($ruleId)){
                        $oCoupon = Mage::getModel('salesrule/coupon')->load($ruleId, 'rule_id');
                        $ruleCouponCode = $oCoupon->getCode();
                        $oRule = Mage::getModel('salesrule/rule')->load($ruleId);
                        $ruleData = $oRule->getData();

                        $savedRuleId = $ruleId.','.$savedRuleId;

                        $getdiscountAmount = $ruleData['discount_amount'];
                        $newDescription = $ruleData['description'];

                        if(!strlen($newDescription)){
                            //if no description found
                            $fullDescripttion = $ruleCouponCode.','.$fullDescripttion;
                        }
                        else
                        {
                            $fullDescripttion = $newDescription.','.$fullDescripttion;

                        }
                        //if Ican not solve the coupon code empty issue then I have to code it here based on rule id
                        //$newRuleId = $ruleData['rule_id'];
                        $actiontype = $ruleData['simple_action'];
                        //$discountStep = $ruleData['discount_step'];///////////new to add code

                        if($actiontype === 'by_percent'){
                            //add code here for step
                            $discountAmount = (float) $address->getSubtotal() * $getdiscountAmount / 100;
                            $basediscountAmount = (float) $address->getBaseSubtotal() * $getdiscountAmount / 100;
                            $totaldiscount = $totaldiscount + $discountAmount;//20
                            $basetotaldiscount = $basetotaldiscount + $basediscountAmount;//20
                        }
                        elseif($actiontype === 'cart_fixed'){ //total deduct from cart
                            $totaldiscount = $totaldiscount + $getdiscountAmount;
                            $basetotaldiscount = $basetotaldiscount + $getdiscountAmount;
                        }
                        elseif($actiontype === 'by_fixed'){ //fixed amount for qty

                            $totaldiscount = $totaldiscount + $this->getItemsQty() * $getdiscountAmount;
                            $basetotaldiscount = $basetotaldiscount + $this->getItemsQty() * $getdiscountAmount;
                        }
                        //pending action type buy_x_get_y if buy X qty then get Y amount discount
                    }

                }
                $totalquoteitems = $this->getItemsQty();
                $this->setCouponCode(rtrim($allcouponcode,','));

                $this->setAppliedRuleIds(strrev(rtrim($savedRuleId,','))); //use rtrim to remove , from end of string also can substr with 0 -1

                //total discount amount = $totaldiscount
                //calculate grand total

                $grandTotal = $address->getSubtotal() + $address->getTaxAmount() + $address->getShippingAmount() + $address->getShippingTaxAmount() - $address->getShippingDiscountAmount() - $totaldiscount ;
                $basegrandTotal = $address->getBaseSubtotal() + $address->getBaseTaxAmount() + $address->getBaseShippingAmount() + $address->getBaseShippingTaxAmount() - $address->getBaseShippingDiscountAmount() - $basetotaldiscount ;

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

                $subtotalwithdiscount = $address->getSubtotal() - $totaldiscount;
                $basesubtotalwithdiscount = $address->getBaseSubtotal() - $basetotaldiscount;

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

                if(strlen($fullDescripttion))
                    $address->setDiscountDescription(rtrim($fullDescripttion,','));

                $address->setDiscountAmount(-($totaldiscount));
                $address->setBaseDiscountAmount(-$basetotaldiscount);
            }

        }

        Mage::helper('sales')->checkQuoteAmount($this, $this->getGrandTotal());
        Mage::helper('sales')->checkQuoteAmount($this, $this->getBaseGrandTotal());

        $this->setItemsCount(0);
        $this->setItemsQty(0);
        $this->setVirtualItemsQty(0);

        foreach ($this->getAllVisibleItems() as $item) {
            if ($item->getParentItem()) {
                continue;
            }

            $item->setAppliedRuleIds(strrev(rtrim($savedRuleId,',')));

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

            $appliedruleid = explode(',',$item->getAppliedRuleIds());

            $discountItem = 0;
            $basediscountItem = 0;
            foreach($appliedruleid as $ruleId){

                if(!empty($ruleId)){
                    $oRule = Mage::getModel('salesrule/rule')->load($ruleId);
                    $ruleData = $oRule->getData();

                    $getitemdiscountAmount = $ruleData['discount_amount'];


                    $actiontype = $ruleData['simple_action'];
                    //$discountStep = $ruleData['discount_step'];///////////new to add code

                    if($actiontype === 'by_percent'){
                        //add code here for step

                        $discountItem += (float) ($item->getQty() * ($item->getPrice() * $getitemdiscountAmount / 100));
                        $basediscountItem += (float) ($item->getQty() * ($item->getBasePrice() * $getitemdiscountAmount / 100));
                        //need to handle if the discount is applicable by filter


                    }
                    elseif($actiontype === 'cart_fixed'){ //total deduct from cart
                        $discountItem += (float) ($getitemdiscountAmount / $totalquoteitems) * $item->getQty();
                        $basediscountItem += (float) ($getitemdiscountAmount / $totalquoteitems) * $item->getQty();
                    }
                    elseif($actiontype === 'by_fixed'){ //fixed amount for qty

                        $discountItem += (float) ($item->getQty() * $getitemdiscountAmount);
                        $basediscountItem += (float) ($item->getQty() * $getitemdiscountAmount);
                            //need to handle if the discount is applicable by filter

                    }
                    //pending action type buy_x_get_y if buy X qty then get Y amount discount
                }

            }
            $item->setDiscountAmount($discountItem);
            $item->setBaseDiscountAmount($basediscountItem);
        }

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