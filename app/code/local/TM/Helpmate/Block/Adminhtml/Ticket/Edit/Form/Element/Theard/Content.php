<?php
class TM_Helpmate_Block_Adminhtml_Ticket_Edit_Form_Element_Theard_Content extends Mage_Adminhtml_Block_Widget
{
    /**
     *
     * @var TM_Helpmate_Model_Ticket
     */
    protected $_ticket;

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('tm/helpmate/ticket/edit/form/element/theard/content.phtml');

        $this->_ticket = Mage::registry('helpmate_ticket_data');
    }

    /**
     *
     * @return TM_Helpmate_Model_Ticket
     */
    public function getTicket()
    {
        return $this->_ticket;
    }

    /**
     *
     * @return array()
     */
    public function getTheards()
    {
        return $this->getTicket()->getTheards();
    }

    /**
     *
     * @param array $theard
     * @return string
     */
    public function getTheardOwnerTitle(array $theard)
    {
        if (null === $theard['user_id']) {

            $ticket = $this->getTicket();
            $username = $ticket->getEmail();
            $customerId = $ticket->getCustomerId();
            if (!empty($customerId)) {
                $username = Mage::getModel('customer/customer')
                    ->load($customerId)
                    ->getName();
            }

            return $this->helper('helpmate')->__('User') . ' ' . $username;
        }

        return $this->helper('helpmate')->__('Admin') . ' ' .
            Mage::getModel('admin/user')
                ->load($theard['user_id'])
                ->getName();
    }

    /**
     *
     * @param array $theard
     * @return string
     */
    public function getTheardCreatedAt(array $theard, $dateType = 'date', $format = 'medium')
    {
        if (!isset($theard['created_at'])) {
            return '';
        }
        if ('date' === $dateType) {
            return $this->helper('core')->formatDate($theard['created_at'], $format);
        }
        return $this->helper('core')->formatTime($theard['created_at'], $format);
    }

    /**
     *
     * @param array $theard
     * @return string
     */
    public function getTheardModifiedAt(array $theard, $dateType = 'date', $format = 'medium')
    {
        if (!isset($theard['created_at'])) {
            return '';
        }
        if ('date' === $dateType) {
            return $this->helper('core')->formatDate($theard['modified_at'], $format);
        }
        return $this->helper('core')->formatTime($theard['modified_at'], $format);
    }

    /**
     *
     * @param array $theard
     * @return string
     */
    public function getTheardStatus(array $theard)
    {
        return (isset($theard['status']) ?
            (Mage::getModel('helpmate/status')->getOptionTitle($theard['status'])) : '');
    }

    /**
     *
     * @param array $theard
     * @return string
     */
    public function getTheardDepartment(array $theard)
    {
        return Mage::getModel('helpmate/department')
            ->load($theard['department_id'])
            ->getName();
    }

    public function getTheardPriority(array $theard)
    {
        return (isset($theard['priority']) ?
            (Mage::getModel('helpmate/priority')->getOptionTitle($theard['priority'])) : '');
    }

    public function getTheardText(array $theard)
    {
        if (empty($theard['text'])) {
            return '';
        }

        $isSecure = Mage::getStoreConfig('helpmate/ticketForm/secure');

        $helper = Mage::helper('purify');
        if ($isSecure && $helper) {
            return $helper->purify(nl2br($theard['text']));
        }

        $content = $theard['text'];

        // text/html convert pseudo text/palin
        $tags = array (
            0 => '~<h[123][^>]+>~si',
            1 => '~<h[456][^>]+>~si',
            2 => '~<table[^>]+>~si',
            3 => '~<tr[^>]+>~si',
            4 => '~<li[^>]+>~si',
            5 => '~<br[^>]+>~si',
            6 => '~<p[^>]+>~si',
            7 => '~<div[^>]+>~si',
        );
        $content = preg_replace($tags, "\n", $content);
        $content = preg_replace('~</t(d|h)>\s*<t(d|h)[^>]+>~si', ' - ', $content);
        $content = preg_replace('~<[^>]+>~s', '', $content);
        // reducing spaces
        $content = preg_replace('~ +~s', ' ', $content);
        $content = preg_replace('~^\s+~m', '', $content);
        $content = preg_replace('~\s+$~m', '', $content);
        // reducing newlines
        $content = preg_replace('~\n+~s', "\n", $content);

        $_content = '';
        $isOld = false;
        $content = wordwrap($content, 170, "\n");
        foreach (explode("\n", $content) as $_line) {
            $_isOld = ('>' === $_line[0]) ? true : false;
            if ($_isOld && !$isOld) {
                $isOld = true;
                $_content .= '<span>' . $this->escapeHtml($_line) . "</span><div>";
                continue;
            }
            if (!$_isOld && $isOld) {
                $isOld = false;
                $_content .= "</div>\n";
            }
            $_content .= $this->escapeHtml($_line) . "\n";
        }
//        $content = $this->escapeHtml($content, array('div', 'span', 'hr'));
        return "<pre class=\"theard_content\" style=\"white-space:pre-wrap\">" .
            "<code>" .
                $_content .
            '</code>' .
        '</pre>';
    }

    public function getTheardFileUrl(array $theard)
    {
        $path = Mage::getBaseUrl('media') . 'helpmate' . DS;
        $files = array_filter(explode(';', $theard['file']));

        foreach ($files as &$file) {
            $file = $path . $file;
        }

        return $files;
    }
}
