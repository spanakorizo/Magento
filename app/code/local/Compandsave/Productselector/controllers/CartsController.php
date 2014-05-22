<?php
class Compandsave_Productselector_CartsController extends Mage_Core_Controller_Front_Action
{
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
        
        foreach ($productIds as $product_id) {
		
            if ($product_id == '') {
                continue;
            }
			$qty = $this->getRequest()->getParam('qty'.$product_id);

			if ($qty <= 0) continue;
			/* $request = new Varien_Object();
			$request->setData($param); */
			$product = Mage::getModel('catalog/product')->load($product_id);
						
            if ($product->getTypeId() == Mage_Catalog_Model_Product_Type::TYPE_SIMPLE) {
                
				if ($product->getId() && $product->isVisibleInCatalog()) {
                    try {
						
						
                        $cart->getQuote()->addProduct($product,$qty);
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
        if($flag == 0)
		    Mage::getSingleton('checkout/session')->setCartWasUpdated(true);
        if ($this->getRequest()->isXmlHttpRequest()) {
            exit('1');
        }
        $this->_redirect('checkout/cart');
	}

}