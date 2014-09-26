<?php
class TM_Akismet_Model_Service 
{
    /**
     *
     * @var string
     */
    protected $_apiKey = null;

    /**
     *
     * @var Zend_Service_Akismet
     */
    protected $_service = null;

    /**
     *
     * @return string
     */
    protected function _getApiKey()
    {
        if(is_null($this->_apiKey)){
            $this->_apiKey = Mage::getStoreConfig('akismet/general/api_key');
        }
        return $this->_apiKey;
    }

    /**
     *
     * @return Zend_Service_Akismet
     */
    protected function _getService()
    {
        if (null === $this->_service) {
            $this->_service = new Zend_Service_Akismet(
                $this->_getApiKey(), Mage::getUrl('/')
            );
        }
        return $this->_service;
    }

    /**
     *
     * @param string $author
     * @param string $email
     * @param string $content
     * @return bool
     */
    public function isSpam($author, $email, $content)
    {
        if (!Mage::getStoreConfig('akismet/general/enabled')) {
            return false;
        }
        // Verify akismet api key
        $service = $this->_getService();
        if (!$service->verifyKey($this->_getApiKey())) {
            Mage::getSingleton('core/session')->addError(
                Mage::helper('akismet')->__(
                    'Invalid Akismet API Key'
                )
            );
            return false;
        }
        
        $helper = Mage::helper('core/http');
        $data = array(
            'user_ip'              => $helper->getRemoteAddr(),
            'user_agent'           => $helper->getHttpUserAgent(),
            'comment_type'         => 'contact',
            'comment_author'       => $author, 
            'comment_author_email' => $email,
            'comment_content'      => $content
        );

        // Check if the submit post is spam
        if ($service->isSpam($data)) {
            Mage::getSingleton('core/session')->addError(
                Mage::helper('akismet')->__(
                    'Sorry, your message has triggered a spam. Please either change message text or contact us in other way.'
                )
            );
            return true;
        }

        return false;
    }

}