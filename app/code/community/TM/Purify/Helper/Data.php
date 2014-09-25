<?php
class TM_Purify_Helper_Data extends Mage_Core_Helper_Abstract implements Zend_Filter_Interface
{
    /**
     *
     * @var HTMLPurifier
     */
    protected  $_purifier;

    protected function _getHTMLPurifierInstance()
    {
        if (!$this->_purifier instanceof HTMLPurifier) {
            require_once 'HTMLPurifier/HTMLPurifier.includes.php';
//            require_once 'HTMLPurifier/HTMLPurifier.auto.php';
//            $options = array(
//                // Allow only paragraph tags
//                // and anchor tags wit the href attribute
//                array('HTML.Allowed','p,a[href]'),
//                // Format end output with Tidy
//                array('Output.TidyFormat', true),
//                // Assume XHTML 1.0 Strict Doctype
//                array('HTML.Doctype', 'XHTML 1.0 Strict'),
//                // Disable cache, but see note after the example
//                array('Cache.DefinitionImpl', null)
//            );
            // Configuring HTMLPurifier
            $config = HTMLPurifier_Config::createDefault();
//            foreach ($options as $option) {
//                $config->set($option[0], $option[1]);
//            }
            $this->_purifier = new HTMLPurifier($config);
        }
        return $this->_purifier;
    }

    /**
     *
     * @param string $html
     * @return string
     */
    public function purify($html)
    {
        return $this->_getHTMLPurifierInstance()
            ->purify($html);
    }

    /**
     *
     * @param string $html
     * @return string
     */
    public function filter($html)
    {
        return $this->purify($html);
    }
}
