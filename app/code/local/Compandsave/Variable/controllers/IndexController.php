<?php
class Compandsave_Variable_IndexController extends Mage_Core_Controller_Front_Action{
    public function indexAction(){
        Mage::getModel('compandsave_variable/observer')->variable_static_code_insert();
        Mage::getModel('compandsave_variable/observer')->productselector_json_code_insert();
    }
}