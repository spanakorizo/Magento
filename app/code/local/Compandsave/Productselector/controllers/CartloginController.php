<?php
class Compandsave_Productselector_CartloginController extends Mage_Core_Controller_Front_Action
{
    public function indexAction(){
        // if customer is not logged in
        if(!Mage::getSingleton('customer/session')->isLoggedIn())
        {
            // get the email and load the customer by id
            $login = $this->getRequest()->getPost('login');
            $email = $login['username'];
            $customer = Mage::getModel('customer/customer')
                ->setWebsiteId(Mage::app()->getStore()
                    ->getWebsiteId())->loadByEmail($email);
            $quote = Mage::getSingleton('checkout/cart')->getQuote();

            //If the customer exists, log them in by forwarding to loginPost
            if($customer->getId())
            {
                // just make the customer log in
                $mysession = Mage::getSingleton('customer/session');
                $mysession->setBeforeAuthUrl(Mage::getUrl('checkout/cart'));
                $mysession->setAfterAuthUrl(Mage::getUrl('checkout/cart'));
                $this->_forward('loginPost','account','customer');
            }
            else
            {
                //There is no customer with that email.
            }
        }
        $this->_redirect('checkout/cart');
        return;
    }
    public function showAction(){
        $this->loadLayout();
        $this->renderLayout();
    }
}