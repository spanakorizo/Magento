<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

class Compandsave_Functions_Adminhtml_PackagelistController
    extends Mage_Adminhtml_Controller_Action
{
    /**
     * Instantiate our grid container block and add to the page content.
     * When accessing this admin index page, we will see a grid of all
     * coupon variable currently available in our Magento instance, along with
     * a button to add a new one if we wish.
     */
    public function indexAction()
    {
       

        
        $this->loadLayout();

        $block = $this->getLayout()->createBlock('adminhtml/template')->setTemplate('cas_functions/packagelist/view.phtml');
        $this->getLayout()->getBlock('content')->append($block);
        
        $this->renderLayout(); 
    }

    public function createbatchAction()
    {
       

        /*
        $this->loadLayout();

        $block = $this->getLayout()->createBlock('adminhtml/template')->setTemplate('cas_functions/packagelist/view.phtml');
        $this->getLayout()->getBlock('content')->append($block);
        
        $this->renderLayout(); */
        $orders = $_GET['orders'];
        if ($orders == "") {echo "no order";}
        else {
            $order_collection = explode(",", $orders);

            //$date = new DateTime();
            date_default_timezone_set('America/Los_Angeles');
            $batchnum = date('YmdHis', time());
            $count = 0;

            foreach ($order_collection as $order_id) {
                if ($order_id != "") {
                    $order = Mage::getModel('sales/order')->load($order_id);
                    $order->setBatchNumber($batchnum)->save();
                    $count++;

                }
            }

            echo "we've update " . $count . " orders";
        }
    }

    public function printAction()
    {
       

        if ($_GET['orderid'] != "") {
            $order_id = $_GET['orderid'];
            echo $this->printlabel($order_id);
        }

        else if ($_GET['batchnum'] != "") {
            $batch_id = $_GET['batchnum'];
            
            $order_collection = Mage::getModel('sales/order')->getCollection()
            ->addAttributeToFilter('batch_number', $batch_id);

            foreach ($order_collection as $order) {
                echo $this->printlabel($order->getId());
            }

            
        }

        else if ($_GET['print'] == "autoship") {
            //autoship need to be checked, status
            $order_collection = Mage::getModel('sales/order')->getCollection()
            ->addAttributeToFilter('status', 'processing')
            ->addAttributeToFilter('order_type', 'Autoship')
            ->addAttributeToFilter('order_type_value', 'Shipped');

            foreach ($order_collection as $order) {
                //echo $order->getId();

                echo $this->printlabel($order->getId());
                $order->setOrderTypeValue('Printed')->setStatus('complete')->setState('complete')->save();
            }
        }
        /*
        $this->loadLayout();

        $block = $this->getLayout()->createBlock('adminhtml/template')->setTemplate('cas_functions/packagelist/view.phtml');
        $this->getLayout()->getBlock('content')->append($block);
        
        $this->renderLayout(); */
    }


    public function printlabel($orderid) {

$phone = "877.777.0127";
$website = "www.tomatoink.com";
$company = "TomatoInk.com";
$address = "38929 CHERRY ST, NEWARK, CA94560";
$order = Mage::getModel('sales/order')->load($orderid);

$orderdate = $order->getCreatedAt();
$customerid = $order->getCustomerId();



$print_text = "<html><body bgcolor='#FFFFFF' text='#000000' leftmargin='0' topmargin='0' marginwidth='0' marginheight='0'><table width='100%' border='0' cellspacing='0' cellpadding='0' align='center' valign='top' style='page-break-after:always;'><tr> <td width='100%' bgcolor='#FFFFFF' valign='top'><style type='text/css'>BODY {FONT: 11px Verdana; COLOR: #000000; letter-spacing: -0.1em;}TABLE {FONT: 11px Verdana; COLOR: #000000; letter-spacing: -0.1em;}TD {FONT: 11px Verdana; COLOR: #000000; letter-spacing: -0.1em;}.pos_receipt td {FONT: 11px Courier; COLOR: #000000; letter-spacing: -0.1em;}</style></td></tr><tr><td><table cellpadding='0' cellspacing='0' width='100%' align='center'><tr> <td align='center'><table cellpadding='0' cellspacing='0' width='600'><tr><td width='100%'><table width='600' border='0' cellspacing='0' cellpadding='0'><tr> <td width='400'><img src='//www.tomatoink.com/v/vspfiles/templates/10/images/company/logo.gif'></td><td width='200' align='right'><table width='180' border='0' cellpadding='0' cellspacing='0'><tr> <td width='90' align='left' colspan='2'><b><font style='font-size:18px;font-weight:bolder;'>PACKING SLIP</font></b></td></tr><tr><td width='90' align='left'><b>Date:</b></td><td width=90><b>Order#:</b></td></tr><tr> <td width='75' align='left'>" . date('Y-m-d', strtotime($orderdate)) . "</td><td><font size='3'><b>" . $orderid . "</b></font></td></tr></table></td></tr></table></td></tr><tr> <td width='100%'><img src='//www.compandsave.com/v/vspfiles/templates/10/images/clear1x1.gif' width='1' height='1' border=0></td></tr></table><br><table width='602' border='0' cellspacing='0' cellpadding='1' bgcolor='#EEEEEE'><tr> <td> <table border='0' cellspacing='0' cellpadding='2' width='100%' bgcolor='#FFFFFF'><tr> <td width='50%' bgcolor='#EEEEEE'><b>Bill To:</b> (CustomerID# " . $customerid . ")</td><td width='50%' bgcolor='#EEEEEE'><b>Ship To:</b></td></tr></table><table cellspacing='0' cellpadding='2' width='100%' border=0><tr> <td colspan=2><img src='//www.compandsave.com/v/vspfiles/templates/10/images/pixel_black.gif' height='2' width='100%'></td></tr></table><table cellspacing='0' cellpadding='5' width='100%' border='0' bgcolor='#FFFFFF'><tr valign='top'><td width='50%' bgcolor='#F9F9F9'>";


// bcomname is billing company name 
$shippingId = $order->getShippingAddress()->getId();

    // Get shipping address data using the id
$shippingaddress = Mage::getModel('sales/order_address')->load($shippingId);

$billingId = $order->getBillingAddress()->getId();

    // Get billing address data using the id
$billingaddress = Mage::getModel('sales/order_address')->load($billingId);

$payment = $order->getPayment();

if ($billingaddress->getCompany() != "") {
    $print_text .= $billingaddress->getCompany() . "<br>";
}


//set billing address
    $print_text .= $billingaddress->getFirstname() . "&nbsp" . $billingaddress->getLastname() . "<br>" . $billingaddress->getStreetFull() . "<br>" . $billingaddress->getCity() . "," . $billingaddress->getRegion() . "&nbsp;" . $billingaddress->getPostcode() . "<br>" . $billingaddress->getCountryId() . "<br>" . $billingaddress->getTelephone() . "<br>" . $billingaddress->getEmail() . "</td><td width='50%' bgcolor='#F9F9F9'><font size='3'><b>";

if ($shippingaddress->getCompany() != "") {
    $print_text .= $shippingaddress->getCompany() . "<br>";
}


//set shipping address


    $cclast = "";
    $payment_method = $payment->getMethod();
    if ($payment_method == "verisign") {
        
        $payment_method = $payment->getCcType();
        $cclast = "**** **** **** " . $payment->getCcLast4();
        $cclast .= " (" . $payment->getCcExpMonth . "/" . $payment->getCcExpYear() . ")";

    }
    

    $print_text .= $shippingaddress->getFirstname() . "&nbsp" . $shippingaddress->getLastname() . "<br>" . $shippingaddress->getStreetFull() . "<br>" . $shippingaddress->getCity() . "," . $shippingaddress->getRegion() . "&nbsp;" . $shippingaddress->getPostcode() . "<br>" . $shippingaddress->getCountryId() . "<br>" . $shippingaddress->getTelephone() . "</b></font></td></tr></table></td></tr></table><br><table width='602' border='0' cellspacing='0' cellpadding='1' bgcolor='#EEEEEE'> <tr> <td><table cellspacing='0' cellpadding='2' width='100%' border='0' bgcolor='#FFFFFF'><tr> <td width='50%' bgcolor='#EEEEEE'><b>Payment Method:</b></td><td width='50%' bgcolor='#EEEEEE'><b>Shipping Method:</b></td></tr></table><table cellspacing='0' cellpadding='2' width='100%' border='0'> <tr> <td colspan=2><img src='//www.compandsave.com/v/vspfiles/templates/10/images/pixel_black.gif' height='2' width='100%'></td></tr></table><table cellspacing='0' cellpadding='5' width='100%' border='0' bgcolor='#FFFFFF'><tr valign='top'><td width='50%' bgcolor='#F9F9F9'><input type='hidden' class='pciaas_NumberPart'/><b>Credit Card</b>:&nbsp;<span class='span_CardType'>" . $payment->getMethod() . "</span><br>" . $cclast . "<br></td><td width='50%' bgcolor='#F9F9F9'>" . $order->getShippingDescription() . "</td></tr></table></td></tr></table><br>";

    $print_text.= "<table width='602' border='0' cellspacing='0' cellpadding='1' bgcolor='#EEEEEE'><tr><td> <table cellspacing='0' cellpadding='2' width='100%' border='0'><td align='LEFT'  width='12%' bgcolor='#EEEEEE'><b>Code</b></td> <td align='LEFT'  width='67%' bgcolor='#EEEEEE'><b>Description</b></td><td align='LEFT'  width='6%' bgcolor='#EEEEEE'><b>Qty</b></td><td align='right' width='7%' bgcolor='#EEEEEE'><b>Loc</b></td><tr><td colspan=5><img src='//www.compandsave.com/v/vspfiles/templates/10/images/pixel_black.gif' height='2' width='100%'></td></tr>";

    

    $items = $order->getAllItems(); 

    $couponcode = "THANKS";
    $coupondsc = "16%";
    $couponExpire = "12/31/2014";

    $sum = 0;
    $ink_count = 0;
    $toner_count = 0;
    $ribbon_count = 0;
    $paper_count = 0;
    $usb_count = 0;

    foreach ($items as $item) {

        $sku = $item->getSku();

        $product = Mage::getModel('catalog/product')->loadByAttribute('sku',$sku);
        if ($product)
        $productinfo = "<tr><td bgcolor='#F9F9F9' width='12%' valign='TOP'><nobr>" . $sku . "</nobr></td><td bgcolor='#F9F9F9' align='LEFT' width='67%' valign='TOP'><b>" . $item->getName() . "</b></td><td bgcolor='#F9F9F9' width='6%'  valign='CENTER'><span style='font-size:15px'><b>" . number_format($item->getQtyOrdered()) . "</b></span></td><td bgcolor='#F9F9F9' align='right'  width='7%'  valign='CENTER'>" . $product->getWarehouseLocation() . "</td></tr>";


        $print_text.= $productinfo;

$Arr=explode("-", $sku);
    $code=$Arr[0];

    //'count ink
    If (($code == "WINK") || ($code == "ZINK") || ($code=="INK")) {
        $ink_count += number_format($item->getQtyOrdered());
    } 

    //count toner
    If (($code == "WTONER") || ($code == "ZTONER") || ($code == "ZTNDR") || ($code == "TONER") || ($code=="DRUM")) {
        $toner_count += number_format($item->getQtyOrdered());
    } 

    //count ribbon
    If ($code == "RIBBON") {
        $ribbon_count += number_format($item->getQtyOrdered());
    } 


    //count usb
    If ($code == "USB") {
        $usb_count += number_format($item->getQtyOrdered());
    }

     //count photo paper
    If ($code == "PH") {
        $paper_count += number_format($item->getQtyOrdered());
    }


    $sum += number_format($item->getQtyOrdered());


    }
    $print_text.= "</table></td></tr></table><br><style type='text/css'>table.sample {background-color: #fff;}</style><table width='600' border='0' cellspacing='0' cellpadding='0'><tr><td bgcolor='#FFFFFF' style='color:#000; border-style:dashed; border-color:#0066CC; border-width:1px; padding:8px' width='33%' align='center' ><span style='font-size:20px; font-weight:bold;'>Thanks!</span><br>We appreciate your business.<br>As a thank you, please use<br>coupon code <span style='text-decoration:underline; font-weight:800'>" . $couponcode . "</span> to save<br><span style='text-decoration:underline; font-weight:800'>" . $coupondsc . "</span> on your next order.<br> Expires " . $couponExpire . ".</td><td bgcolor='#FFFFFF' style='color:#000; border-style:dashed; border-color:#FF0099; border-width:1px;  padding:8px' width='33%' align='center'><span style='font-size:20px; font-weight:bold'>CAUTION:</span><br>To prevent leaks and damages<br> to your property, please read<br>instructions on the back<br><span style='text-decoration:underline; font-weight:800'>before installing new<br> cartridges and toner.</span></td><td bgcolor='#fff' style='color:#0066CC; padding:0px; border-style:solid; border-width:1px;' width='33%'><center><table border='0' cellspacing='0' cellpadding='0' class='sample' width='100%' height='115'><tr><td height='40' style='vertical-align:top; font-size:1.2em; font-weight:bold; padding-top:14px;'>";

    If ($ink_count>0) 
     $print_text .= "&nbsp;Ink : " . $ink_count . "&nbsp;&nbsp;&nbsp;&nbsp;";

     If ($toner_count>0) 
     $print_text .= "&nbsp;Toner : " . $toner_count . "&nbsp;&nbsp;&nbsp;&nbsp;";

     If ($ribbon_count>0)
     $print_text .= "&nbsp;Ribbon: " . $ribbon_count . "&nbsp;&nbsp;&nbsp;&nbsp;";

     If ($paper_count>0)
     $print_text .= "&nbsp;Photo Paper: " . $paper_count . "&nbsp;&nbsp;&nbsp;&nbsp;";

     If ($usb_count>0)
     $print_text .= "&nbsp;USB: " . $usb_count . "&nbsp;&nbsp;&nbsp;&nbsp;";

     if ($sum-$ink_count-$toner_count-$ribbon_count-$paper_count-$usb_count > 0 )
        {$print_text .= "&nbsp;Others:" . $sum-$ink_count-$toner_count-$ribbon_count-$paper_count-$usb_count . "&nbsp;&nbsp;&nbsp;&nbsp;";}
    
     $print_text .= "</td></tr>";
     If (($sum != $ink_count) && ($sum != $toner_count) && ($sum != $ribbon_count) && ($sum != $paper_count) && ($sum != $usb_count))
     $print_text .= "<tr height='30'><td style='font-size:1.6em; font-weight:bold; vertical-align:bottom; padding-bottom:9px;'>&nbsp;Total: " . $sum . "</td></tr>";

     $print_text .= "</table></center></td></tr>";

   

    $print_text.= "<tr><td colspan='3' style='font-family:Verdana, Helvetica, sans-serif; font-size:11px; font-weight:400; color:#000000; text-align:center;' valign='top'>All of our products are backed with a 1-Year Money Back Guarantee.<br>If you have any questions about our products or services, please feel free to call us at <b><span style='color:#06C; text-decoration:underline'>" . $phone . "</span></b> or visit our website at <b><span style='color:#06C; text-decoration:underline'>" . $website . "</span></b><br><br><b><span style='font-size:11px;'><center>" . $company . "<span style='font-size:10px;'>&nbsp;</span><span style='font-size:9px;'>&#8226;</span><span style='font-size:10px;'>&nbsp;</span>" . $address . "<span style='font-size:10px;'>&nbsp;</span><span style='font-size:9px;'>&#8226;</span><span style='font-size:10px;'>&nbsp;</span>" . $phone . "</center></b></td></tr></table></td></tr></table></td></tr></table></body></html>";
    return $print_text;
    }


    protected function _isAllowed()
    {
        /**
         * we include this switch to demonstrate that you can add action
         * level restrictions in your ACL rules. The isAllowed() method will
         * use the ACL rule we have configured in our adminhtml.xml file:
         * - acl
         * - - resources
         * - - - admin
         * - - - - children
         * - - - - - smashingmagazine_coupondirectory
         * - - - - - - children
         * - - - - - - - coupon
         *
         * eg. you could add more rules inside coupon for edit and delete.
         */
        
        $actionName = $this->getRequest()->getActionName();
        switch ($actionName) {
            case 'index':
            case 'createbatch':
            case 'print':
                // intentionally no break
            default:
                $adminSession = Mage::getSingleton('admin/session');
                $isAllowed = $adminSession
                    ->isAllowed('compandsave_functions/packagelist');
                break;
        }

        return $isAllowed;
    }
    
}