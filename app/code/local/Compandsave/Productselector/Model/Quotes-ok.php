<?php
/**
 * Created by PhpStorm.
 * User: Zahed
 * Date: 9/21/14
 * Time: 7:35 PM
 * known bugs: 	Do not add auto discount in the middle of transaction
 *				May be shipping and Tax must apply first
 */
class Compandsave_Productselector_Model_Quotes extends Mage_Core_Model_Abstract
{
    protected $_eventPrefix = 'sales_quote';
    protected $_eventObject = 'quote';

    protected function _construct()
    {
        $this->_init('compandsave_productselector/quote');
    }

    public function collectTotal($couponcode, $updateflag = 1)
    {
        $quote = Mage::getSingleton('checkout/cart')->getQuote();

        if($updateflag==null){
            $quote->setTotalsCollectedFlag(false);
			return $quote; //prevent double update total in checkout/cart
        }
        else{
            if ($quote->getTotalsCollectedFlag()) {
                return $quote;
            }
            Mage::dispatchEvent($this->_eventPrefix . '_collect_totals_before', array($this->_eventObject => $quote));

            $oCoupon = Mage::getModel('salesrule/coupon')->load($couponcode, 'code');
            $oRule = Mage::getModel('salesrule/rule')->load($oCoupon->getRuleId());
            $ruleData = $oRule->getData();

            $discountAmount = $ruleData['discount_amount'];
            $newDescription = $ruleData['description'];
            if($newDescription == null)
                $newDescription = $couponcode;
            $newRuleId = $ruleData['rule_id'];
            $actiontype = $ruleData['simple_action'];

            if($actiontype === 'by_percent'){
                $discountAmount = (float) $quote->getSubtotal() * $discountAmount / 100;
            }
            elseif($actiontype === 'by_fixed'){
                $discountAmount = $discountAmount;
            }

            $oldRuleId = $quote->getAppliedRuleIds();
            $canAddItems = $quote->isVirtual()? ('billing') : ('shipping');

            foreach ($quote->getAllAddresses() as $address) {
				
				$address->setSubtotal(0);
				$address->setBaseSubtotal(0);

/* 				$address->setGrandTotal(0);
				$address->setBaseGrandTotal(0); */

                if($canAddItems == $address->getAddressType()){

                    $old_discount = $address->getDiscountAmount(); // -5
                    $old_description = $address->getDiscountDescription(); //DAD4
                    $old_grandTotal = $address->getGrandTotal();
                    $old_BasegrandTotal = $address->getBaseGrandTotal();  // 95

                    $address->collectTotals();
                    //applied_rule_ids
					//echo $quote->getGrandTotal().'/';
					//echo $address->getSubtotal();
					//exit();
                    $quote->setSubtotal((float) $quote->getSubtotal() + $address->getSubtotal());
					$quote->setBaseSubtotal((float) $quote->getBaseSubtotal() + $address->getBaseSubtotal());

                    $quote->setGrandTotal((float) $quote->getGrandTotal() - $discountAmount);
                    $quote->setBaseGrandTotal((float) $quote->getBaseGrandTotal() - $discountAmount);
                    $quote->setSubtotalWithDiscount((float) $quote->getSubtotalWithDiscount() - $discountAmount);
                    $quote->setBaseSubtotalWithDiscount((float) $quote->getBaseSubtotalWithDiscount() - $discountAmount);
					if(strlen($oldRuleId))
						$quote->setAppliedRuleIds($oldRuleId.','.$newRuleId);
					else
						$quote->setAppliedRuleIds($newRuleId);

                    if( $old_discount < 0 ){

                        $address->setGrandTotal((float) $old_grandTotal - $discountAmount);
                        $address->setBaseGrandTotal((float) $old_BasegrandTotal - $discountAmount);
                        $address->setDiscountAmount( -(abs($old_discount) + $discountAmount));
                        if(strlen($old_description))
                            $address->setDiscountDescription($old_description.','.$newDescription);
                        else
                            $address->setDiscountDescription($newDescription);
                        $address->setBaseDiscountAmount( -(abs($old_discount) + $discountAmount));
                    }else {
                        $address->setDiscountAmount(-($discountAmount));
                        $address->setDiscountDescription($address->getDiscountDescription());
                        $address->setBaseDiscountAmount(-($discountAmount));
						$address->setGrandTotal((float) $old_grandTotal - $discountAmount);
                        $address->setBaseGrandTotal((float) $old_BasegrandTotal - $discountAmount);
                    }
                }
                
                    
                

            }

            Mage::helper('sales')->checkQuoteAmount($quote, $quote->getGrandTotal());
            Mage::helper('sales')->checkQuoteAmount($quote, $quote->getBaseGrandTotal());

            $quote->setItemsCount(0);
            $quote->setItemsQty(0);
            $quote->setVirtualItemsQty(0);

            foreach ($quote->getAllVisibleItems() as $item) {
                if ($item->getParentItem()) {
                    continue;
                }

                $children = $item->getChildren();
                if ($children && $item->isShipSeparately()) {
                    foreach ($children as $child) {
                        if ($child->getProduct()->getIsVirtual()) {
                            $quote->setVirtualItemsQty($quote->getVirtualItemsQty() + $child->getQty()*$item->getQty());
                        }
                    }
                }

                if ($item->getProduct()->getIsVirtual()) {
                    $quote->setVirtualItemsQty($quote->getVirtualItemsQty() + $item->getQty());
                }
                $quote->setItemsCount($quote->getItemsCount()+1);
                $quote->setItemsQty((float) $quote->getItemsQty()+$item->getQty());
            }

            $quote->setData('trigger_recollect', 0);//it was 0
            $this->_validateCouponCode();

            Mage::dispatchEvent($this->_eventPrefix . '_collect_totals_after', array($this->_eventObject => $quote));

            $quote->setTotalsCollectedFlag(true);

            return $quote;
        }


    }

    protected function _validateCouponCode()
    {
        $quote = Mage::getSingleton('checkout/cart')->getQuote();
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
                    $quote->setCouponCode('');
                }
            }
        }
        return $quote;
    }
}