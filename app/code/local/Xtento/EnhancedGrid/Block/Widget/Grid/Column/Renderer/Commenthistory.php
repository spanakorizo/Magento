<?php

/**
 * Product:       Xtento_EnhancedGrid (1.4.6)
 * ID:            N/W+h1YQ5V9LjSr4Chjc6LFc95fJOqSQtLq5zrXLDNA=
 * Packaged:      2014-06-10T20:04:35+00:00
 * Last Modified: 2014-05-12T23:33:27+02:00
 * File:          app/code/local/Xtento/EnhancedGrid/Block/Widget/Grid/Column/Renderer/Commenthistory.php
 * Copyright:     Copyright (c) 2014 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_EnhancedGrid_Block_Widget_Grid_Column_Renderer_Commenthistory extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $html = '';

        $statusHistoryEntries = $row->getAllStatusHistory();
        foreach ($statusHistoryEntries as $statusHistoryEntry) {
            $comment = $statusHistoryEntry->getComment();
            if (!empty($comment)) {
                $html .= $comment . "\n";
            }
        }

        if (Mage::helper('xtento_enhancedgrid')->isMageExport()) {
            return strip_tags($html);
        }

        $html = nl2br(Mage::helper('core/string')->truncate($html, $this->getColumn()->getData('truncate_chars') ? $this->getColumn()->getData('truncate_chars') : 130, '', $_remainder));

        if ($_remainder) {
            $_id = 'id' . uniqid();
            $html .= '... <span id="' . $_id . '">' . nl2br($_remainder) . '</span>';
            $html .= '<script type="text/javascript">
            $(\'' . $_id . '\').hide();
            $(\'' . $_id . '\').up().observe(\'mouseover\', function(){$(\'' . $_id . '\').show();});
            $(\'' . $_id . '\').up().observe(\'mouseout\',  function(){$(\'' . $_id . '\').hide();});
            </script>
            ';
        }

        return $html;
    }

    /*
     * Return dummy filter.
     */
    public function getFilter()
    {
        return false;
    }
}

?>