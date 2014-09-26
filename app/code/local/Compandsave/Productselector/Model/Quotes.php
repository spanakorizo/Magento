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
        $quote = Mage::getSingleton('checkout/session')->getQuote();

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

            //$newDescription = $ruleData['description'];
            
			//if($newDescription == null)
			//$newDescription = $couponcode;
			//must need to retrive onl and new rul from system apply before calculation of discount
			$newRuleId = $ruleData['rule_id'];    
            $oldRuleId = $quote->getAppliedRuleIds();
			
            $canAddItems = $quote->isVirtual()? ('billing') : ('shipping');

			$quote->getSubtotal(0);
			$quote->getBaseSubtotal(0);
			
            foreach ($quote->getAllAddresses() as $address) {
				
				$address->setSubtotal(0);
				$address->setBaseSubtotal(0);

 				$address->setGrandTotal(0);
				$address->setBaseGrandTotal(0); 

                if($canAddItems == $address->getAddressType()){

                    /* $old_discount = $address->getDiscountAmount(); // -5
                    $old_description = $address->getDiscountDescription(); //DAD4
                    $old_grandTotal = $address->getGrandTotal();
                    $old_BasegrandTotal = $address->getBaseGrandTotal();  */ // 95

                    $address->collectTotals();
                    	
                    $quote->setSubtotal((float) $address->getSubtotal());
					$quote->setBaseSubtotal((float) $address->getBaseSubtotal());
					
					if(strlen($oldRuleId))
						$quote->setAppliedRuleIds($oldRuleId.','.$newRuleId);
					else
						$quote->setAppliedRuleIds($newRuleId);
					
					$appliedruleid = explode(',',$quote->getAppliedRuleIds());
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
								$totaldiscount = $totaldiscount + $quote->getItemsQty() * $getdiscountAmount;
                                $basetotaldiscount = $basetotaldiscount + $quote->getItemsQty() * $getdiscountAmount;
							}
						    //pending action type buy_x_get_y if buy X qty then get Y amount discount
						}
					
					}
                    $quote->setAppliedRuleIds(strrev(rtrim($savedRuleId,','))); //use rtrim to remove , from end of string also can substr with 0 -1

					//total discount amount = $totaldiscount
					//calculate grand total
					$grandTotal = $address->getSubtotal() + $address->getTaxAmount() + $address->getShippingAmount() + $address->getShippingTaxAmount() - $address->getShippingDiscountAmount() - $totaldiscount;
                    $basegrandTotal = $address->getBaseSubtotal() + $address->getBaseTaxAmount() + $address->getBaseShippingAmount() + $address->getBaseShippingTaxAmount() - $address->getBaseShippingDiscountAmount() - $basetotaldiscount;

                    $subtotalwithdiscount = $address->getSubtotal() - $totaldiscount;
                    $basesubtotalwithdiscount = $address->getBaseSubtotal() - $basetotaldiscount;

                    $subtotalwithTax = $address->getSubtotal() + $address->getTaxAmount() + $address->getShippingTaxAmount();
                    $BasesubtotalwithTax = $address->getBaseSubtotal() + $address->getBaseTaxAmount() + $address->getBaseShippingTaxAmount();

					//=========set Quote Total ==================//
                    //Grand total
					$quote->setGrandTotal((float) $grandTotal);
					$quote->setBaseGrandTotal((float) $basegrandTotal);

                    //set Sub total with discount
                    $quote->setSubtotalWithDiscount((float) $subtotalwithdiscount);
                    $quote->setBaseSubtotalWithDiscount((float) $basesubtotalwithdiscount);



					//============set quote shipping address resource model value ==========//
                    $address->setGrandTotal((float) $grandTotal);
                    $address->setBaseGrandTotal((float) $basegrandTotal);

                    $address->setSubtotalInclTax($subtotalwithTax);
                    $address->setSubtotalInclTax($BasesubtotalwithTax);

                    if(strlen($fullDescripttion))
                        $address->setDiscountDescription(rtrim($fullDescripttion,','));

                    $address->setDiscountAmount(-($totaldiscount));
                    $address->setBaseDiscountAmount(-$basetotaldiscount);
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