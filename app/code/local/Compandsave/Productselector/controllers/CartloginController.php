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
            
            //If the customer exists, log them in by forwarding to loginPost
            if($customer->getId())
            {

                $password = $login['password']; //validate password
                if($customer->validatePassword($password)){
                    $mysession = Mage::getSingleton('customer/session');
                    $mysession->setBeforeAuthUrl(Mage::getUrl('checkout/cart'));
                    $mysession->setAfterAuthUrl(Mage::getUrl('checkout/cart'));
                    $this->_forward('loginPost','account','customer');
                    $this->_redirect('checkout/cart');
                }
                else{
                    $this->_redirect('checkout/cart',array( '_query' => array('email' => Mage::helper('core')->encrypt($email))));
                }

            }
            else
            {
                $this->_redirect('checkout/cart',array( '_query' => array('email' => Mage::helper('core')->encrypt($email))));
				
            }
        }
        
        return;
    }
    public function showAction(){
        $this->loadLayout();
        $this->renderLayout();
    }

    public function createAction(){
        // if customer is not logged in
        if(!Mage::getSingleton('customer/session')->isLoggedIn())
        {

            $password = (string) $this->getRequest()->getPost('password');
            $confirmation = (string) $this->getRequest()->getPost('confirmation');
            $email = (string) $this->getRequest()->getPost('email');
            $firstname = (string) $this->getRequest()->getPost('firstname');
            $lastname = (string) $this->getRequest()->getPost('lastname');

            if (!Zend_Validate::is(trim($firstname), 'NotEmpty') || !Zend_Validate::is(trim($lastname), 'NotEmpty') || !Zend_Validate::is(trim($email), 'NotEmpty')|| !Zend_Validate::is(trim($password), 'NotEmpty') ) {
                $this->_redirect('checkout/cart',array( '_query' => array('empty' => 'true')));
                return;
            }

            if (!Zend_Validate::is($email, 'EmailAddress')) {
                $this->_redirect('checkout/cart',array( '_query' => array('emailid' => Mage::helper('core')->encrypt($email),'firstname' => Mage::helper('core')->encrypt($firstname),'lastname' => Mage::helper('core')->encrypt($lastname))));
				return;
            }

            if (strlen($password) && !Zend_Validate::is($password, 'StringLength', array(6))) {
                $this->_redirect('checkout/cart',array( '_query' => array('emailid' => Mage::helper('core')->encrypt($email),'firstname' => Mage::helper('core')->encrypt($firstname),'lastname' => Mage::helper('core')->encrypt($lastname),'len' => Mage::helper('core')->encrypt('false'))));
				return;
            }

            if ($password != $confirmation) {
                $this->_redirect('checkout/cart',array( '_query' => array('emailid' => Mage::helper('core')->encrypt($email),'firstname' => Mage::helper('core')->encrypt($firstname),'lastname' => Mage::helper('core')->encrypt($lastname),'valid' => Mage::helper('core')->encrypt('false'))));
				return;
            }


            $customer = Mage::getModel('customer/customer')
                ->setWebsiteId(Mage::app()->getStore()
                    ->getWebsiteId())->loadByEmail($email);
            if(!$customer->getId()){  ///customer email not exist so we can add customer
                
				$mysession = Mage::getSingleton('customer/session');
                $mysession->setBeforeAuthUrl(Mage::getUrl('checkout/cart'));
                $mysession->setAfterAuthUrl(Mage::getUrl('checkout/cart'));
                $this->_forward('createPost','account','customer');
                $this->_redirect('checkout/cart');
            }
            else{
                $this->_redirect('checkout/cart',array( '_query' => array('emailid' => Mage::helper('core')->encrypt($email),'firstname' => Mage::helper('core')->encrypt($firstname),'lastname' => Mage::helper('core')->encrypt($lastname),'exist' => Mage::helper('core')->encrypt('true'))));
				return;

            }


        }

        return;
    }
	
	public function forgetpassAction(){
        // if customer is not logged in
        if(!Mage::getSingleton('customer/session')->isLoggedIn())
        {
            // get the email and load the customer by id
            $email = (string) $this->getRequest()->getPost('forget_email');
			if ($email) {
				if (!Zend_Validate::is($email, 'EmailAddress')) {
					$this->_redirect('checkout/cart',array( '_query' => array('forgetemail' => Mage::helper('core')->encrypt($email),'invalid' => Mage::helper('core')->encrypt('true') )));
					return;
				}
				$customer = Mage::getModel('customer/customer')
					->setWebsiteId(Mage::app()->getStore()
						->getWebsiteId())->loadByEmail($email);
				
				//If the customer exists, log them in by forwarding to loginPost
				if($customer->getId()){  ///customer email not exist so we can add customer
					
					try {
						$newResetPasswordLinkToken =  Mage::helper('customer')->generateResetPasswordLinkToken();
						$customer->changeResetPasswordLinkToken($newResetPasswordLinkToken);
						$customer->sendPasswordResetConfirmationEmail();
						$this->_redirect('checkout/cart',array( '_query' => array('forgetemail' => Mage::helper('core')->encrypt($email),'success' => Mage::helper('core')->encrypt('true') )));
						return;
					} catch (Exception $exception) {
						$this->_redirect('checkout/cart',array( '_query' => array('forgetemail' => Mage::helper('core')->encrypt($email))));
						return;
					}
					
				}
				else{
					$this->_redirect('checkout/cart',array( '_query' => array('forgetemail' => Mage::helper('core')->encrypt($email))));
					return;

				}
			}
        }
        
        return;
    }
}