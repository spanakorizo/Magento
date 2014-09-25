<?php
include_once "Mage/Contacts/controllers/IndexController.php";

class TM_Helpmate_Contacts_IndexController extends Mage_Contacts_IndexController
{

    public function addActionLayoutHandles()
    {
        parent::addActionLayoutHandles();

        if (!Mage::getStoreConfig("helpmate/ticketForm/noOverrideContactUsForm")
            && 'contacts_index_index' === strtolower($this->getFullActionName())) {

            $update = $this->getLayout()->getUpdate();
            $update->addHandle('helpmate_contacts_add_ticket');
        }

        return $this;
    }
}
