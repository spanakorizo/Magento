<?php
class Compandsave_Catalog_Model_Product_Type_Grouped extends Mage_Catalog_Model_Product_Type_Grouped
{
    public function getAssociatedProducts($product = null)
    {
        if (!$this->getProduct($product)->hasData($this->_keyAssociatedProducts)) {
            $associatedProducts = array();

            if (!Mage::app()->getStore()->isAdmin()) {
                $this->setSaleableStatus($product);
            }

            $collection = $this->getAssociatedProductCollection($product)
                ->addAttributeToSelect('*')
                //->addFilterByRequiredOptions()
                ->setPositionOrder()
                //->addOrderedQty()
                ->addStoreFilter($this->getStoreFilter($product))
                ->addAttributeToFilter('status', array('in' => $this->getStatusFilters($product)));
                /*
            foreach ($collection as $item) {
                $skus[] = $item->getsku();  
            }

            $collection_qty = Mage::getResourceModel('reports/product_collection')
            ->addOrderedQty()
            ->addAttributeToSelect('*')
            ->addAttributeToFilter('sku', $skus)
            ->setOrder('ordered_qty', 'desc');
            */
            $write = Mage::getSingleton('core/resource')->getConnection('core_read');
            foreach ($collection as $item) {
                $query = "SELECT SUM(`qty_ordered`) as total FROM `sales_flat_order_item` WHERE sku='" . $item->getsku() . "'";
                $readresult=$write->query($query);

                while ($row = $readresult->fetch() ) {
                    $item['ordered_qty']=round($row['total']);
                    break;
                }
                $associatedProducts[] = $item;
                
            }

            usort($associatedProducts, function($a, $b) {
                return $b['ordered_qty'] - $a['ordered_qty'];
            });
            /*
            if (count($collection_qty) < count($collection)) {
                foreach ($collection as $item) {
                    $check_flag = 1;
                    foreach($collection_qty as $item_qty) {
                        if ($item->getsku() == $item_qty->getsku()) {$check_flag = 0; break;}
                    }
                    if ($check_flag == 1) {
                        
                        
                        //$query = "SELECT SUM(`qty_ordered`) as total FROM `sales_flat_order_item` WHERE sku = 'ZINK-Lexmark-14-15-Combo2'";
                        
                        $associatedProducts[] = $item;

                    }
                }

            }*/


            $this->getProduct($product)->setData($this->_keyAssociatedProducts, $associatedProducts);
        }
        return $this->getProduct($product)->getData($this->_keyAssociatedProducts);
    }

    
}