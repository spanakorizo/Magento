<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Tax
 * @copyright   Copyright (c) 2013 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * Tax totals calculation model
 */
class Compandsave_MultipleCoupon_Model_Sales_Total_Quote_Tax extends Mage_Tax_Model_Sales_Total_Quote_Tax
{

    protected function _calcUnitTaxAmount(
        $item, $rate, &$taxGroups = null, $taxId = null, $recalculateRowTotalInclTax = false
    )
    {
        /*
         * check if discount is originally zero into database
         */
        $item_discount = Mage::getSingleton('sales/quote_item')->load($item->getId());

        $qty = $item->getTotalQty();
        $inclTax = $item->getIsPriceInclTax();
        $price = $item->getTaxableAmount();
        $basePrice = $item->getBaseTaxableAmount();
        $rateKey = ($taxId == null) ? (string)$rate : $taxId;

        $isWeeeEnabled = $this->_weeeHelper->isEnabled();
        $isWeeeTaxable = $this->_weeeHelper->isTaxable();

        $hiddenTax = null;
        $baseHiddenTax = null;
        $weeeTax = null;
        $baseWeeeTax = null;
        $unitTaxBeforeDiscount = null;
        $weeeTaxBeforeDiscount = null;
        $baseUnitTaxBeforeDiscount = null;
        $baseWeeeTaxBeforeDiscount = null;

        switch ($this->_config->getCalculationSequence($this->_store)) {
            case Mage_Tax_Model_Calculation::CALC_TAX_BEFORE_DISCOUNT_ON_EXCL:
            case Mage_Tax_Model_Calculation::CALC_TAX_BEFORE_DISCOUNT_ON_INCL:
                $unitTaxBeforeDiscount = $this->_calculator->calcTaxAmount($price, $rate, $inclTax, false);
                $baseUnitTaxBeforeDiscount = $this->_calculator->calcTaxAmount($basePrice, $rate, $inclTax, false);

                if ($isWeeeEnabled && $isWeeeTaxable) {
                    $weeeTaxBeforeDiscount = $this->_calculateWeeeTax(0, $item, $rate, false);
                    $unitTaxBeforeDiscount += $weeeTaxBeforeDiscount;
                    $baseWeeeTaxBeforeDiscount = $this->_calculateWeeeTax(0, $item, $rate);
                    $baseUnitTaxBeforeDiscount += $baseWeeeTaxBeforeDiscount;
                }
                $unitTaxBeforeDiscount = $unitTax = $this->_calculator->round($unitTaxBeforeDiscount);
                $baseUnitTaxBeforeDiscount = $baseUnitTax = $this->_calculator->round($baseUnitTaxBeforeDiscount);
                break;
            case Mage_Tax_Model_Calculation::CALC_TAX_AFTER_DISCOUNT_ON_EXCL:
            case Mage_Tax_Model_Calculation::CALC_TAX_AFTER_DISCOUNT_ON_INCL:
                /*
                * rewrite here
                */

                $discountAmount = (float) $item_discount->getDiscountAmount() / $qty;
                $baseDiscountAmount = (float) $item_discount->getBaseDiscountAmount() / $qty;

                //We want to remove weee
                if ($isWeeeEnabled) {
                    $discountAmount = $discountAmount - $item->getWeeeDiscount() / $qty;
                    $baseDiscountAmount = $baseDiscountAmount - $item->getBaseWeeeDiscount() / $qty;
                }

                $unitTaxBeforeDiscount = $this->_calculator->calcTaxAmount($price, $rate, $inclTax, false);
                $unitTaxDiscount = $this->_calculator->calcTaxAmount($discountAmount, $rate, $inclTax, false);
                $unitTax = $this->_calculator->round(max($unitTaxBeforeDiscount - $unitTaxDiscount, 0));

                $baseUnitTaxBeforeDiscount = $this->_calculator->calcTaxAmount($basePrice, $rate, $inclTax, false);
                $baseUnitTaxDiscount = $this->_calculator->calcTaxAmount($baseDiscountAmount, $rate, $inclTax, false);
                $baseUnitTax = $this->_calculator->round(max($baseUnitTaxBeforeDiscount - $baseUnitTaxDiscount, 0));

                if ($isWeeeEnabled && $this->_weeeHelper->isTaxable()) {
                    $weeeTax = $this->_calculateRowWeeeTax($item->getWeeeDiscount(), $item, $rate, false);
                    $weeeTax = $weeeTax / $qty;
                    $unitTax += $weeeTax;
                    $baseWeeeTax = $this->_calculateRowWeeeTax($item->getBaseWeeeDiscount(), $item, $rate);
                    $baseWeeeTax = $baseWeeeTax / $qty;
                    $baseUnitTax += $baseWeeeTax;
                }

                $unitTax = $this->_calculator->round($unitTax);
                $baseUnitTax = $this->_calculator->round($baseUnitTax);

                //Calculate the weee taxes before discount
                $weeeTaxBeforeDiscount = 0;
                $baseWeeeTaxBeforeDiscount = 0;

                if ($isWeeeTaxable) {
                    $weeeTaxBeforeDiscount = $this->_calculateWeeeTax(0, $item, $rate, false);
                    $unitTaxBeforeDiscount += $weeeTaxBeforeDiscount;
                    $baseWeeeTaxBeforeDiscount = $this->_calculateWeeeTax(0, $item, $rate);
                    $baseUnitTaxBeforeDiscount += $baseWeeeTaxBeforeDiscount;
                }

                $unitTaxBeforeDiscount = max(0, $this->_calculator->round($unitTaxBeforeDiscount));
                $baseUnitTaxBeforeDiscount = max(0, $this->_calculator->round($baseUnitTaxBeforeDiscount));

                if ($inclTax && $discountAmount > 0) {
                    $hiddenTax = $unitTaxBeforeDiscount - $unitTax;
                    $baseHiddenTax = $baseUnitTaxBeforeDiscount - $baseUnitTax;
                    $this->_hiddenTaxes[] = array(
                        'rate_key' => $rateKey,
                        'qty' => $qty,
                        'item' => $item,
                        'value' => $hiddenTax,
                        'base_value' => $baseHiddenTax,
                        'incl_tax' => $inclTax,
                    );
                } elseif ($discountAmount > $price) { // case with 100% discount on price incl. tax
                    $hiddenTax = $discountAmount - $price;
                    $baseHiddenTax = $baseDiscountAmount - $basePrice;
                    $this->_hiddenTaxes[] = array(
                        'rate_key' => $rateKey,
                        'qty' => $qty,
                        'item' => $item,
                        'value' => $hiddenTax,
                        'base_value' => $baseHiddenTax,
                        'incl_tax' => $inclTax,
                    );
                }
                // calculate discount compensation
                // We need the discount compensation when dont calculate the hidden taxes
                // (when product does not include taxes)
                if (!$item->getNoDiscount() && $item->getWeeeTaxApplied()) {
                    $item->setDiscountTaxCompensation($item->getDiscountTaxCompensation() +
                    $unitTaxBeforeDiscount * $qty - max(0, $unitTax) * $qty);
                }
                break;
        }

        $rowTax = $this->_store->roundPrice(max(0, $qty * $unitTax));
        $baseRowTax = $this->_store->roundPrice(max(0, $qty * $baseUnitTax));
        $item->setTaxAmount($item->getTaxAmount() + $rowTax);
        $item->setBaseTaxAmount($item->getBaseTaxAmount() + $baseRowTax);
        if (is_array($taxGroups)) {
            $taxGroups[$rateKey]['tax'] = max(0, $rowTax);
            $taxGroups[$rateKey]['base_tax'] = max(0, $baseRowTax);
        }

        $rowTotalInclTax = $item->getRowTotalInclTax();
        if (!isset($rowTotalInclTax) || $recalculateRowTotalInclTax) {
            if ($this->_config->priceIncludesTax($this->_store)) {
                $item->setRowTotalInclTax($price * $qty);
                $item->setBaseRowTotalInclTax($basePrice * $qty);
            } else {
                $item->setRowTotalInclTax(
                    $item->getRowTotalInclTax() + ($unitTaxBeforeDiscount - $weeeTaxBeforeDiscount) * $qty);
                $item->setBaseRowTotalInclTax(
                    $item->getBaseRowTotalInclTax() +
                    ($baseUnitTaxBeforeDiscount - $baseWeeeTaxBeforeDiscount) * $qty);
            }
        }

        return $this;
    }


    protected function _calcRowTaxAmount(
        $item, $rate, &$taxGroups = null, $taxId = null, $recalculateRowTotalInclTax = false
    )
    {
        /*
         * check if discount is originally zero into database
         */

        $item_discount = Mage::getSingleton('sales/quote_item')->load($item->getId());

        $inclTax = $item->getIsPriceInclTax();
        $subtotal = $taxSubtotal = $item->getTaxableAmount();
        $baseSubtotal = $baseTaxSubtotal = $item->getBaseTaxableAmount();
        $rateKey = ($taxId == null) ? (string)$rate : $taxId;

        $isWeeeEnabled = $this->_weeeHelper->isEnabled();
        $isWeeeTaxable = $this->_weeeHelper->isTaxable();

        $hiddenTax = null;
        $baseHiddenTax = null;
        $weeeTax = null;
        $baseWeeeTax = null;
        $rowTaxBeforeDiscount = null;
        $baseRowTaxBeforeDiscount = null;
        $weeeRowTaxBeforeDiscount = null;
        $baseWeeeRowTaxBeforeDiscount = null;

        switch ($this->_helper->getCalculationSequence($this->_store)) {
            case Mage_Tax_Model_Calculation::CALC_TAX_BEFORE_DISCOUNT_ON_EXCL:
            case Mage_Tax_Model_Calculation::CALC_TAX_BEFORE_DISCOUNT_ON_INCL:
                $rowTaxBeforeDiscount = $this->_calculator->calcTaxAmount($subtotal, $rate, $inclTax, false);
                $baseRowTaxBeforeDiscount = $this->_calculator->calcTaxAmount($baseSubtotal, $rate, $inclTax, false);

                if ($isWeeeEnabled && $isWeeeTaxable) {
                    $weeeRowTaxBeforeDiscount = $this->_calculateRowWeeeTax(0, $item, $rate, false);
                    $rowTaxBeforeDiscount += $weeeRowTaxBeforeDiscount;
                    $baseWeeeRowTaxBeforeDiscount = $this->_calculateRowWeeeTax(0, $item, $rate);
                    $baseRowTaxBeforeDiscount += $baseWeeeRowTaxBeforeDiscount;
                }
                $rowTaxBeforeDiscount = $rowTax = $this->_calculator->round($rowTaxBeforeDiscount);
                $baseRowTaxBeforeDiscount = $baseRowTax = $this->_calculator->round($baseRowTaxBeforeDiscount);
                break;
            case Mage_Tax_Model_Calculation::CALC_TAX_AFTER_DISCOUNT_ON_EXCL:
            case Mage_Tax_Model_Calculation::CALC_TAX_AFTER_DISCOUNT_ON_INCL:

                /*
                *rewrite here
                 */
                $discountAmount = $item_discount->getDiscountAmount();
                $baseDiscountAmount = $item_discount->getBaseDiscountAmount();


                if ($isWeeeEnabled) {
                    $discountAmount = $discountAmount - $item->getWeeeDiscount();
                    $baseDiscountAmount = $baseDiscountAmount - $item->getBaseWeeeDiscount();
                }

                $rowTax = $this->_calculator->calcTaxAmount(
                    max($subtotal - $discountAmount, 0),
                    $rate,
                    $inclTax
                );
                $baseRowTax = $this->_calculator->calcTaxAmount(
                    max($baseSubtotal - $baseDiscountAmount, 0),
                    $rate,
                    $inclTax
                );

                if ($isWeeeEnabled && $this->_weeeHelper->isTaxable()) {
                    $weeeTax = $this->_calculateRowWeeeTax($item->getWeeeDiscount(), $item, $rate, false);
                    $rowTax += $weeeTax;
                    $baseWeeeTax = $this->_calculateRowWeeeTax($item->getBaseWeeeDiscount(), $item, $rate);
                    $baseRowTax += $baseWeeeTax;
                }

                $rowTax = $this->_calculator->round($rowTax);
                $baseRowTax = $this->_calculator->round($baseRowTax);

                //Calculate the Row Tax before discount
                $rowTaxBeforeDiscount = $this->_calculator->calcTaxAmount(
                    $subtotal,
                    $rate,
                    $inclTax,
                    false
                );
                $baseRowTaxBeforeDiscount = $this->_calculator->calcTaxAmount(
                    $baseSubtotal,
                    $rate,
                    $inclTax,
                    false
                );

                //Calculate the Weee taxes before discount
                $weeeRowTaxBeforeDiscount = 0;
                $baseWeeeRowTaxBeforeDiscount = 0;
                if ($isWeeeTaxable) {
                    $weeeRowTaxBeforeDiscount = $this->_calculateRowWeeeTax(0, $item, $rate, false);
                    $rowTaxBeforeDiscount += $weeeRowTaxBeforeDiscount;
                    $baseWeeeRowTaxBeforeDiscount = $this->_calculateRowWeeeTax(0, $item, $rate);
                    $baseRowTaxBeforeDiscount += $baseWeeeRowTaxBeforeDiscount;
                }

                $rowTaxBeforeDiscount = max(0, $this->_calculator->round($rowTaxBeforeDiscount));
                $baseRowTaxBeforeDiscount = max(0, $this->_calculator->round($baseRowTaxBeforeDiscount));

                if ($inclTax && $discountAmount > 0) {
                    $hiddenTax = $rowTaxBeforeDiscount - $rowTax;
                    $baseHiddenTax = $baseRowTaxBeforeDiscount - $baseRowTax;
                    $this->_hiddenTaxes[] = array(
                        'rate_key' => $rateKey,
                        'qty' => 1,
                        'item' => $item,
                        'value' => $hiddenTax,
                        'base_value' => $baseHiddenTax,
                        'incl_tax' => $inclTax,
                    );
                } elseif ($discountAmount > $subtotal) { // case with 100% discount on price incl. tax
                    $hiddenTax = $discountAmount - $subtotal;
                    $baseHiddenTax = $baseDiscountAmount - $baseSubtotal;
                    $this->_hiddenTaxes[] = array(
                        'rate_key' => $rateKey,
                        'qty' => 1,
                        'item' => $item,
                        'value' => $hiddenTax,
                        'base_value' => $baseHiddenTax,
                        'incl_tax' => $inclTax,
                    );
                }
                // calculate discount compensation
                if (!$item->getNoDiscount() && $item->getWeeeTaxApplied()) {
                    $item->setDiscountTaxCompensation($item->getDiscountTaxCompensation() +
                    $rowTaxBeforeDiscount - max(0, $rowTax));
                }
                break;
        }
        $item->setTaxAmount($item->getTaxAmount() + max(0, $rowTax));
        $item->setBaseTaxAmount($item->getBaseTaxAmount() + max(0, $baseRowTax));
        if (is_array($taxGroups)) {
            $taxGroups[$rateKey]['tax'] = max(0, $rowTax);
            $taxGroups[$rateKey]['base_tax'] = max(0, $baseRowTax);
        }

        $rowTotalInclTax = $item->getRowTotalInclTax();
        if (!isset($rowTotalInclTax) || $recalculateRowTotalInclTax) {
            if ($this->_config->priceIncludesTax($this->_store)) {
                $item->setRowTotalInclTax($subtotal);
                $item->setBaseRowTotalInclTax($baseSubtotal);
            } else {
                $item->setRowTotalInclTax(
                    $item->getRowTotalInclTax() + $rowTaxBeforeDiscount - $weeeRowTaxBeforeDiscount);
                $item->setBaseRowTotalInclTax($item->getBaseRowTotalInclTax() +
                $baseRowTaxBeforeDiscount - $baseWeeeRowTaxBeforeDiscount);
            }
        }
        return $this;
    }


    protected function _aggregateTaxPerRate(
        $item, $rate, &$taxGroups, $taxId = null, $recalculateRowTotalInclTax = false
    )
    {
        /*
         * check if discount is originally zero into database
         */

        $item_discount = Mage::getSingleton('sales/quote_item')->load($item->getId());

        $inclTax = $item->getIsPriceInclTax();
        $rateKey = ($taxId == null) ? (string)$rate : $taxId;
        $taxSubtotal = $subtotal = $item->getTaxableAmount();
        $baseTaxSubtotal = $baseSubtotal = $item->getBaseTaxableAmount();

        $isWeeeEnabled = $this->_weeeHelper->isEnabled();
        $isWeeeTaxable = $this->_weeeHelper->isTaxable();

        if (!isset($taxGroups[$rateKey]['totals'])) {
            $taxGroups[$rateKey]['totals'] = array();
            $taxGroups[$rateKey]['base_totals'] = array();
            $taxGroups[$rateKey]['weee_tax'] = array();
            $taxGroups[$rateKey]['base_weee_tax'] = array();
        }

        $hiddenTax = null;
        $baseHiddenTax = null;
        $weeeTax = null;
        $baseWeeeTax = null;
        $discount = 0;
        $rowTaxBeforeDiscount = 0;
        $baseRowTaxBeforeDiscount = 0;
        $weeeRowTaxBeforeDiscount = 0;
        $baseWeeeRowTaxBeforeDiscount = 0;


        switch ($this->_helper->getCalculationSequence($this->_store)) {
            case Mage_Tax_Model_Calculation::CALC_TAX_BEFORE_DISCOUNT_ON_EXCL:
            case Mage_Tax_Model_Calculation::CALC_TAX_BEFORE_DISCOUNT_ON_INCL:
                $rowTaxBeforeDiscount = $this->_calculator->calcTaxAmount($subtotal, $rate, $inclTax, false);
                $baseRowTaxBeforeDiscount = $this->_calculator->calcTaxAmount($baseSubtotal, $rate, $inclTax, false);

                if ($isWeeeEnabled && $isWeeeTaxable) {
                    $weeeRowTaxBeforeDiscount = $this->_calculateRowWeeeTax(0, $item, $rate, false);
                    $baseWeeeRowTaxBeforeDiscount = $this->_calculateRowWeeeTax(0, $item, $rate);
                    $rowTaxBeforeDiscount += $weeeRowTaxBeforeDiscount;
                    $baseRowTaxBeforeDiscount += $baseWeeeRowTaxBeforeDiscount;
                    $taxGroups[$rateKey]['weee_tax'][] = $this->_deltaRound($weeeRowTaxBeforeDiscount,
                        $rateKey, $inclTax);
                    $taxGroups[$rateKey]['base_weee_tax'][] = $this->_deltaRound($baseWeeeRowTaxBeforeDiscount,
                        $rateKey, $inclTax);
                }
                $taxBeforeDiscountRounded = $rowTax = $this->_deltaRound($rowTaxBeforeDiscount, $rateKey, $inclTax);
                $baseTaxBeforeDiscountRounded = $baseRowTax = $this->_deltaRound($baseRowTaxBeforeDiscount,
                    $rateKey, $inclTax, 'base');
                $item->setTaxAmount($item->getTaxAmount() + max(0, $rowTax));
                $item->setBaseTaxAmount($item->getBaseTaxAmount() + max(0, $baseRowTax));
                break;
            case Mage_Tax_Model_Calculation::CALC_TAX_AFTER_DISCOUNT_ON_EXCL:
            case Mage_Tax_Model_Calculation::CALC_TAX_AFTER_DISCOUNT_ON_INCL:
                if ($this->_helper->applyTaxOnOriginalPrice($this->_store)) {
                    $discount = $item->getOriginalDiscountAmount();
                    $baseDiscount = $item->getBaseOriginalDiscountAmount();
					
                } else {

                        $discount = $item_discount->getDiscountAmount();
                        $baseDiscount = $item_discount->getBaseDiscountAmount();

					
                }
				
                //We remove weee discount from discount if weee is not taxed
                if ($isWeeeEnabled) {
                    $discount = $discount - $item->getWeeeDiscount();
                    $baseDiscount = $baseDiscount - $item->getBaseWeeeDiscount();
                }
                $taxSubtotal = max($subtotal - $discount, 0);
                $baseTaxSubtotal = max($baseSubtotal - $baseDiscount, 0);

                $rowTax = $this->_calculator->calcTaxAmount($taxSubtotal, $rate, $inclTax, false);
                $baseRowTax = $this->_calculator->calcTaxAmount($baseTaxSubtotal, $rate, $inclTax, false);

                if ($isWeeeEnabled && $this->_weeeHelper->isTaxable()) {
                    $weeeTax = $this->_calculateRowWeeeTax($item->getWeeeDiscount(), $item, $rate, false);
                    $rowTax += $weeeTax;
                    $baseWeeeTax = $this->_calculateRowWeeeTax($item->getBaseWeeeDiscount(), $item, $rate);
                    $baseRowTax += $baseWeeeTax;
                    $taxGroups[$rateKey]['weee_tax'][] = $weeeTax;
                    $taxGroups[$rateKey]['base_weee_tax'][] = $baseWeeeTax;
                }

                $rowTax = $this->_deltaRound($rowTax, $rateKey, $inclTax);
                $baseRowTax = $this->_deltaRound($baseRowTax, $rateKey, $inclTax, 'base');

                $item->setTaxAmount($item->getTaxAmount() + max(0, $rowTax));
                $item->setBaseTaxAmount($item->getBaseTaxAmount() + max(0, $baseRowTax));

                //Calculate the Row taxes before discount
                $rowTaxBeforeDiscount = $this->_calculator->calcTaxAmount(
                    $subtotal,
                    $rate,
                    $inclTax,
                    false
                );
                $baseRowTaxBeforeDiscount = $this->_calculator->calcTaxAmount(
                    $baseSubtotal,
                    $rate,
                    $inclTax,
                    false
                );


                if ($isWeeeTaxable) {
                    $weeeRowTaxBeforeDiscount = $this->_calculateRowWeeeTax(0, $item, $rate, false);
                    $rowTaxBeforeDiscount += $weeeRowTaxBeforeDiscount;
                    $baseWeeeRowTaxBeforeDiscount = $this->_calculateRowWeeeTax(0, $item, $rate);
                    $baseRowTaxBeforeDiscount += $baseWeeeRowTaxBeforeDiscount;
                }

                $taxBeforeDiscountRounded = max(
                    0,
                    $this->_deltaRound($rowTaxBeforeDiscount, $rateKey, $inclTax, 'tax_before_discount')
                );
                $baseTaxBeforeDiscountRounded = max(
                    0,
                    $this->_deltaRound($baseRowTaxBeforeDiscount, $rateKey, $inclTax, 'tax_before_discount_base')
                );

                if (!$item->getNoDiscount()) {
                    if ($item->getWeeeTaxApplied()) {
                        $item->setDiscountTaxCompensation($item->getDiscountTaxCompensation() +
                        $taxBeforeDiscountRounded - max(0, $rowTax));
                    }
                }

                if ($inclTax && $discount > 0) {
                    $roundedHiddenTax = $taxBeforeDiscountRounded - max(0, $rowTax);
                    $baseRoundedHiddenTax = $baseTaxBeforeDiscountRounded - max(0, $baseRowTax);
                    $this->_hiddenTaxes[] = array(
                        'rate_key' => $rateKey,
                        'qty' => 1,
                        'item' => $item,
                        'value' => $roundedHiddenTax,
                        'base_value' => $baseRoundedHiddenTax,
                        'incl_tax' => $inclTax,
                    );
                }
                break;
        }

        $rowTotalInclTax = $item->getRowTotalInclTax();
        if (!isset($rowTotalInclTax) || $recalculateRowTotalInclTax) {
            if ($this->_config->priceIncludesTax($this->_store)) {
                $item->setRowTotalInclTax($subtotal);
                $item->setBaseRowTotalInclTax($baseSubtotal);
            } else {
                $item->setRowTotalInclTax(
                    $item->getRowTotalInclTax() + $taxBeforeDiscountRounded - $weeeRowTaxBeforeDiscount);
                $item->setBaseRowTotalInclTax(
                    $item->getBaseRowTotalInclTax()
                    + $baseTaxBeforeDiscountRounded
                    - $baseWeeeRowTaxBeforeDiscount);
            }
        }

        $taxGroups[$rateKey]['totals'][] = max(0, $taxSubtotal);
        $taxGroups[$rateKey]['base_totals'][] = max(0, $baseTaxSubtotal);
        $taxGroups[$rateKey]['tax'][] = max(0, $rowTax);
        $taxGroups[$rateKey]['base_tax'][] = max(0, $baseRowTax);
        return $this;
    }

}
