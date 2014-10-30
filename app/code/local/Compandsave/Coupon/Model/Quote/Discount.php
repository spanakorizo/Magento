<?php
/**
 * Magento Enterprise Edition
 * @category    Compandsave
 * @package     Compandsave_Coupon
 * @copyright   Copyright (c) 2014 Compandsave.com Inc. (http://www.compandsave.com)
 */

/**
 * Discount calculation model
 *
 * @category    Compandsave
 * @package     Compandsave_Coupon
 * @author      Compandsave.com Inc. Development Team <andrew@compandsave.com>
 */
class Compandsave_Coupon_Model_Quote_Discount extends Mage_Sales_Model_Quote_Address_Total_Abstract
{

    /**
     * Discount calculation object
     *
     * @var Compandsave_Coupon_Model_Validator
     */
    protected $_calculator;

    /**
     * Item Discount Summation for all coupon codes
     * @var Array()
     */
    Protected $_discountAmount;
    Protected $_basediscountAmount;
    /**
     * Initialize discount collector
     */

    public function __construct()
    {
        $this->setCode('discount');
        $this->_calculator = Mage::getSingleton('compandsave_coupon/validator');

    }

    /**
     * Collect address discount amount
     *
     * @param   Mage_Sales_Model_Quote_Address $address
     * @return  Compandsave_Coupon_Model_Quote_Discount
     */
    public function collect(Mage_Sales_Model_Quote_Address $address)
    {
        parent::collect($address);
        $quote = $address->getQuote();
        $store = Mage::app()->getStore($quote->getStoreId());
        $this->_calculator->reset($address);
        /*
         * Initialize total discount array
         */
        $this->_discountAmount = null;
        $this->_basediscountAmount = null;

        $items = $this->_getAddressItems($address);

        if (!count($items)) {
            return $this;
        }
        /**
         * Initialize Coupons array to null
         */
        $coupons = null;
        /**
         * Get All coupon Codes
         */
        $coupons = explode(',',$quote->getCouponCode());
        /**
         * Add an empty array to add auto coupon cause magento core apply auto coupon code with every coupon code
         */
        if($coupons[0] != null){

            $newarray[0] = '';

            $coupons = array_merge($coupons,$newarray);
        }


        foreach($coupons as $coupon){

            $eventArgs = array(
                'website_id'        => $store->getWebsiteId(),
                'customer_group_id' => $quote->getCustomerGroupId(),
                'coupon_code'       => $coupon,
            );

            $this->_calculator->init($store->getWebsiteId(), $quote->getCustomerGroupId(), $coupon);

            $this->_calculator->initTotals($items, $address);

            $address->setDiscountDescription(array());

            foreach ($items as $item) {

                if ($item->getNoDiscount()) {
                    $item->setDiscountAmount(0);
                    $item->setBaseDiscountAmount(0);
                }
                else {
                    /**
                     * Child item discount we calculate for parent
                     */
                    if ($item->getParentItemId()) {
                        continue;
                    }

                    $eventArgs['item'] = $item;
                    Mage::dispatchEvent('sales_quote_address_discount_item', $eventArgs);

                    if ($item->getHasChildren() && $item->isChildrenCalculated()) {

                        foreach ($item->getChildren() as $child) {
                            $flag = 1;
                            if($coupon == '')
                                $this->_calculator->process($child);
                            else
                                $this->_calculator->process($child, $flag);

                            $eventArgs['item'] = $child;
                            Mage::dispatchEvent('sales_quote_address_discount_item', $eventArgs);

                            $this->_aggregateItemDiscount($child);
                        }
                    }
                    else{

                        $flag = 1;

                        if($coupon == '')
                            $this->_calculator->process($item);
                        else
                            $this->_calculator->process($item, $flag);

                        $this->_aggregateItemDiscount($item);

                        $ruleid = $item->getAppliedRuleIds();

                        if(!empty($ruleid)){
                            $appliedruleIds[$item->getId()][] = $ruleid;
                        }
                        /**
                         * Set Discount on each item for all coupon code applied rules
                         */
                        $this->_discountAmount[$item->getId()] += $item->getDiscountAmount();
                        $this->_basediscountAmount[$item->getId()] += $item->getBaseDiscountAmount();

                    }
                }

                $item->setDiscountAmount($this->_discountAmount[$item->getId()]);
                $item->setBaseDiscountAmount($this->_basediscountAmount[$item->getId()]);
                $item->setAppliedRuleIds(implode(',',$appliedruleIds[$item->getId()]));
            }
        }



        /**
         * process weee amount
         */
        if (Mage::helper('weee')->isEnabled() && Mage::helper('weee')->isDiscounted($store)) {
            $this->_calculator->processWeeeAmount($address, $items);
        }

        /**
         * Process shipping amount discount
         */
        $address->setShippingDiscountAmount(0);
        $address->setBaseShippingDiscountAmount(0);
        if ($address->getShippingAmount()) {
            $this->_calculator->processShippingAmount($address);
            $this->_addAmount(-$address->getShippingDiscountAmount());
            $this->_addBaseAmount(-$address->getBaseShippingDiscountAmount());
        }

        $this->_calculator->prepareDescription($address);

        return $this;
    }

    /**
     * Aggregate item discount information to address data and related properties
     *
     * @param   Mage_Sales_Model_Quote_Item_Abstract $item
     * @return  Compandsave_Coupon_Model_Quote_Discount
     */
    protected function _aggregateItemDiscount($item)
    {

        $this->_addAmount(-$item->getDiscountAmount());
        $this->_addBaseAmount(-$item->getBaseDiscountAmount());
        return $this;
    }

    /**
     * Add discount total information to address
     *
     * @param   Mage_Sales_Model_Quote_Address $address
     * @return  Compandsave_Coupon_Model_Quote_Discount
     */
    public function fetch(Mage_Sales_Model_Quote_Address $address)
    {
        $amount = $address->getDiscountAmount();

        if ($amount != 0) {
            $description = $address->getDiscountDescription();
            if (strlen($description)) {
                $title = Mage::helper('sales')->__('Discount (%s)', $description);
            } else {
                $title = Mage::helper('sales')->__('Discount');
            }
            $address->addTotal(array(
                'code'  => $this->getCode(),
                'title' => $title,
                'value' => $amount
            ));
        }
        return $this;
    }


}
