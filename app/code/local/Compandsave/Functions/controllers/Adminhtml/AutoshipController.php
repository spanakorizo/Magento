<?php 
error_reporting(E_ALL);
ini_set('display_errors',1);

require_once("easypost-php-master/lib/easypost.php");

class Compandsave_Functions_Adminhtml_AutoshipController
    extends Mage_Adminhtml_Controller_Action
{
    //

    //set key
     


    /**
     * Instantiate our grid container block and add to the page content.
     * When accessing this admin index page, we will see a grid of all
     * coupon variable currently available in our Magento instance, along with
     * a button to add a new one if we wish.
     */
    public function indexAction()
    {
       
        // instantiate the grid container
        /*
        $DuplicateBlock = $this->getLayout()
            ->createBlock('compandsave_functions_adminhtml/duplicate');

        // Add the grid container as the only item on this page
        $this->loadLayout()
            ->_addContent($DuplicateBlock)
            ->renderLayout();*/
        
        $this->loadLayout();
        /*
        $block = $this->getLayout()->createBlock(
        'Mage_Adminhtml_Block_Template',
        'duplicatepage',
        array('template' => 'cas_functions/duplicate/hello.phtml')
        );*/
        $block = $this->getLayout()->createBlock('adminhtml/template')->setTemplate('cas_functions/autoship/view.phtml');
        $this->getLayout()->getBlock('content')->append($block);
        
        $this->renderLayout();
    }

    public function shipAction() {
        //
        //test site key

        \EasyPost\EasyPost::setApiKey('ThiApS0dVpUZBCLg7lD0aw'); //test site key

        $ship_collection = Mage::getModel('sales/order')->getCollection()
            ->addAttributeToFilter('status', 'pending')
            ->addAttributeToFilter('order_type', 'Autoship')
            ->addAttributeToFilter('order_type_value', 'Autoship')
            ->addAttributeToSelect('*');
        $ship_count = count($ship_collection);
        //echo $ship_count . "<br>";

        foreach ($ship_collection as $shipment) {

            $buyshiplabel = new buyshiplabel(); 

            $addressid = $shipment->getShippingAddress()->getId();
            $shippingaddress = Mage::getModel('sales/order_address')->load($addressid);
            $timestamp = $_GET['batch_id'];

            $result_text = $buyshiplabel->buyshipment($shipment->getId(), $shipment->getCustomerName(), $shippingaddress->getStreetFull(), '', $shippingaddress->getCity(), $shippingaddress->getState(), $shippingaddress->getPostcode(), '', '5', $shipment->getCustomerId(), '', '', $timestamp);
            $result = explode("&", $result_text);

            if ($result[0] == "N") {
              $shipment->setOrderType('Error')->setOrderTypeValue($result[2])->save();
              echo "error with " . $result[1]; 
            }
            else {
              echo "success with " . $result[1];
            }
            //echo $shipment->getId() . " " . $shippingaddress->getStreetFull() . " " . $shipment->getCustomerEmail() . "<br>";
        }
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
            case 'edit':
            case 'delete':
                // intentionally no break
            default:
                $adminSession = Mage::getSingleton('admin/session');
                $isAllowed = $adminSession
                    ->isAllowed('compandsave_Functions/autoship');
                break;
        }

        return $isAllowed;
    }
}
?>


<?php
class buyshiplabel {


//private $address;
//private $verified_address;
//private $from_address;
//private $parcel;
//private $shipment;
//private $trackingcode;
//private $shipid;
//private $label_url;
//private $orderrate;


public function buyshipment($orderid, $customername, $customerstreet1, $customerstreet2, $customercity, $customerstate, $customerzip, $customerphone, $weight, $customerid, $orderdate, $productcode, $timestamp) {
  $errorflag = false;

  $address = \EasyPost\Address::create(array(
  'name' => $customername,
  'street1' => $customerstreet1,
  'street2' => $customerstreet2,
  'city' => $customercity,
  'state' => $customerstate,
  'zip' => $customerzip,
  'phone' => $customerphone, 
  'country' => 'US'
));

try 
    {

    $verified_address = $address->verify();
    
    $from_address = \EasyPost\Address::create(
    array(
        "name"    => "Shipping Receiving",
        "company" => "CompAndSave",
        "street1" => "38929 Cherry ST",
        "street2" => " ",
        "city"    => "Newark",
        "state"   => "CA",
        "zip"     => "94560",
        "phone"   => "415-379-7678"
    )
);
$parcel = \EasyPost\Parcel::create(
    array(
        "predefined_package" => "Parcel",
        "weight" => $weight 
    )
);
$shipment = \EasyPost\Shipment::create(
    array(
        "to_address"   => $verified_address,
        "from_address" => $from_address,
        "parcel"       => $parcel,
        "carrier" => 'USPS', 
        "service" => 'First' 
    )
);




     }
    catch(Exception $e) {return "N&" . $orderid . "&address"; $errorflag=true;}

//echo $verified_address;
    //echo $errorflag;

if ($errorflag!=true)
{

    try 
       {
        //echo ($shipment->rates[0]->_values["service"]);
        //$shipment->buy($shipment->rates[0]);
        $shipment->buy($shipment->lowest_rate(array('USPS'), array('First')));


//$shipment->label(array("file_format" => "zpl"));

$trackingcode = $shipment->tracking_code;
$shipid = $shipment->id;
$label_url = $shipment->postage_label->label_url;
//$label_zpl_url = $shipment->postage_label->label_zpl_url;
//$orderrate = $shipment->rates[0]->rate;
$orderrate = $shipment->selected_rate->rate; 
        //print_r($shipment);
        //echo $shipment;


        //echo "normallabel: " . $shipment->postage_label->label_url;
        //echo "<br>";
        //echo $shipment->postage_label->label_zpl_url;
    }
    catch(Exception $e) {return "N&" . $orderid . "&shipment"; $errorflag=true;}
}

  if ($errorflag!=true) {

    $carrier_code = 'usps';
    $carrier_title = 'United States Postal Service';
    $order = Mage::getModel('sales/order')->load($orderid);

    $order->setAutoshipShipid($shipid)->setBatchNumber($timestamp)->setOrderTypeValue('Shipped')->setShippingAmount($orderrate)->save();
    if($order->canShip()) {
        
      $ti_shipmentid = Mage::getModel('sales/order_shipment_api')
        ->create($order->getIncrementId(), array());
        
      $ti_shipment = Mage::getModel('sales/order_shipment')->getCollection()->addFieldToFilter('order_id', $order['entity_id'])->getFirstItem();
      $ti_shipment->setShippingLabel($label_url);



      $track = Mage::getModel('sales/order_shipment_track')
        ->setTrackNumber($trackingcode)
        ->setCarrierCode($carrier_code)
        ->setTitle($carrier_title)
        ->setOrderId($order['entity_id'])
        ->setParentID($ti_shipment['entity_id'])
        ->setCreatedAt($this->getShipDate())
        ->save();


      return "Y&" . $orderid . "&success";   
      
      }
    else 
      return "N&" . $orderid . "&unable to ship";
  }

//save in DB
/*
if($errorflag!=true)
{
$username = "DBCOMPANDSAVE";
$password = "CAS123cas!";
$hostname = "DBCOMPANDSAVE.db.11475010.hostedresource.com"; 
$databasename = "DBCOMPANDSAVE";

$dbhandle = mysql_connect($hostname, $username, $password);
  if($dbname) die("Unable to connect to MySQL");

mysql_select_db($databasename) or die("can't find dbname");


date_default_timezone_set('America/Los_Angeles');
//$shipdate = date('m/d/Y h:i:s a', time());
$ship_date = date('Y/m/d h:i:s a', time());

//insert info in database
$query = "INSERT INTO Orders_Tracking (OrderID, Tracking_Code, Label_Url, ZPL_Url, weight, Street1, Street2, City, State, Zip, Phone, CustomerID, CustomerName, ShipID, OrderDate, ShipRate, Ship_Date, ProductCode, Flag_2) VALUES('$orderid', '$trackingcode', '$label_url', '$label_zpl_url', '$weight', '$customerstreet1', '$customerstreet2', '$customercity', '$customerstate', '$customerzip', '$customerphone', '$customerid', '$customername', '$shipid', '$orderdate', '$orderrate', '$ship_date', '$productcode', '$timestamp')";
$result = mysql_query($query);
mysql_close($dbhandle);
echo "Y&" . $orderid . "&" . $trackingcode . "&" . $label_url . "&" . $orderrate;
}

*/




}

  private function getShipDate() {
    date_default_timezone_set('America/Los_Angeles');
    $date = date('Y-d-m h:i:s a', time());

    return $date;

  }



}


?>