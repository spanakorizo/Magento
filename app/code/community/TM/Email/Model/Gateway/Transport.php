<?php
class TM_Email_Model_Gateway_Transport extends Mage_Core_Model_Abstract
{
    const TYPE_SENDMAIL = 0;
    const TYPE_SMTP     = 1;

    const SECURE_NONE = 0;//false;
    const SECURE_SSL  = 1;//'SSL';
    const SECURE_TLS  = 2;//'TLS';

    public function _construct()
    {
        parent::_construct();
        $this->_init('tm_email/gateway_transport');
    }

    public function getOptionArray()
    {
        return $this->_getResource()->getOptionArray();
    }

    public function getSsl()
    {
        $secure = $this->getSecure();
        if (self::SECURE_SSL == $secure) {
            return 'SSL';
        } elseif (self::SECURE_TLS == $secure) {
            return 'TLS';
        }
        return false;
    }

    public function getTransport()
    {
        $type = $this->getType();
        if (self::TYPE_SMTP != $type) {
//            throw new Exception(
//               Mage::helper('tm_email')->__('Protocol type incorrect'
//            ));
            ini_set('SMTP', Mage::getStoreConfig('system/smtp/host'));
            ini_set('smtp_port', Mage::getStoreConfig('system/smtp/port'));

            $setReturnPath = Mage::getStoreConfig(
                Mage_Core_Model_Email_Template::XML_PATH_SENDING_SET_RETURN_PATH
            );
            switch ($setReturnPath) {
                case 1:
                    $returnPathEmail = $this->getSenderEmail();
                    break;
                case 2:
                    $returnPathEmail = Mage::getStoreConfig(
                        Mage_Core_Model_Email_Template::XML_PATH_SENDING_RETURN_PATH_EMAIL
                    );
                    break;
                default:
                    $returnPathEmail = null;
                    break;
            }

            if ($returnPathEmail !== null) {
                $transport = new Zend_Mail_Transport_Sendmail("-f".$returnPathEmail);
//                Zend_Mail::setDefaultTransport($mailTransport);
            } else {
                $transport = new Zend_Mail_Transport_Sendmail();
            }
            return $transport;
        }

        $config = array();

        $port = $this->getPort();
        if (!empty($port)) {
            $config['port'] = $port;
        }
        if ($this->getSsl()) {
            $config['ssl'] = $this->getSsl();
        }
        $user = $this->getUser();
        $password = $this->getPassword();
        if ($user && $password) {
//            $config['auth'] = 'login';
            $config['username'] = $user;
            $config['password'] = $password;
        }
        $config['auth'] = $this->getAuth();
        return new Zend_Mail_Transport_Smtp($this->getHost(), $config);
    }
}