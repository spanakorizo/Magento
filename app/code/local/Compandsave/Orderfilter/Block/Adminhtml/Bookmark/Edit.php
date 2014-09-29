<?php
class Compandsave_OrderFilter_Block_Adminhtml_Bookmark_Edit
    extends Mage_Adminhtml_Block_Widget_Form_Container
{
    protected function _construct()
    {
        parent::_construct();
        $this->_objectId = 'id';
        $this->_blockGroup = 'compandsave_orderfilter';
        $this->_controller = 'adminhtml_bookmark';
        $this->_mode = 'edit';
    }
    protected function _prepareLayout()
    {
        $this->_updateButton('save', 'label', $this->__('Save Filter'));
        $this->_updateButton('delete', 'label', $this->__('Delete Filter'));
        $this->_addButton('save_and_continue', array(
            'label' => Mage::helper('compandsave_orderfilter')->__('Save And Continue Edit'),
            'onclick' => 'saveAndContinueEdit()',
            'class' => 'save',
        ), -100);

        $this->_formScripts[] = "
function saveAndContinueEdit(){
editForm.submit($('edit_form').action+'back/edit/');
}
";
        return parent::_prepareLayout();
    }

    public function getHeaderText()
    {
        $model = Mage::registry('current_bookmark');
        if ($model && $model->getId()) {
            return $this->__('Edit Filter "%s" (%s)',
                $this->escapeHtml($model->getName()),
                $this->escapeHtml($model->getId())
            );
        } else {
            return $this->__('New Filter');
        }
    }
}