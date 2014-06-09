<?php

/**
 * Product:       Xtento_XtCore (1.0.0)
 * ID:            N/W+h1YQ5V9LjSr4Chjc6LFc95fJOqSQtLq5zrXLDNA=
 * Packaged:      2014-05-02T21:30:46+00:00
 * Last Modified: 2011-12-26T17:43:29+01:00
 * File:          app/code/local/Xtento/XtCore/Model/Feed/Source/Updates/Type.php
 * Copyright:     Copyright (c) 2014 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_XtCore_Model_Feed_Source_Updates_Type
{
    const TYPE_NEW_RELEASE = 'NEW_RELEASE';
    const TYPE_UPDATED = 'UPDATE';
    const TYPE_PROMOTIONS = 'PROMOTIONS';
    const TYPE_OTHERINFO = 'OTHER_INFO';

    public function toOptionArray()
    {
        return array(
            array('value' => self::TYPE_UPDATED, 'label' => Mage::helper('xtcore')->__('Updates for installed extensions')),
            array('value' => self::TYPE_NEW_RELEASE, 'label' => Mage::helper('xtcore')->__('New Extensions')),
            array('value' => self::TYPE_PROMOTIONS, 'label' => Mage::helper('xtcore')->__('Discounts/Promotions')),
            array('value' => self::TYPE_OTHERINFO, 'label' => Mage::helper('xtcore')->__('Other information'))
        );
    }
}