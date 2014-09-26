<?php
class Compandsave_OrderFilter_Block_Adminhtml_Bookmark
    extends Mage_Adminhtml_Block_Widget_Grid_Container

{
    protected function _construct()
    {
        $this->_blockGroup = 'compandsave_orderfilter';
        $this->_controller = 'adminhtml_bookmark';
        $this->_headerText = $this->__('Order Filter Bookmarks');
        $this->_addButtonLabel = $this->__('Add Filter');
        parent::_construct();
    }
}