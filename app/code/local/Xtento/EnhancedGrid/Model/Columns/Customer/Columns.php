<?php

/**
 * Product:       Xtento_EnhancedGrid (1.4.6)
 * ID:            %!uniqueid!%
 * Packaged:      %!packaged!%
 * Last Modified: 2014-06-10T18:46:16+02:00
 * File:          app/code/local/Xtento/EnhancedGrid/Model/Columns/Customer/Columns.php.sample
 * Copyright:     Copyright (c) 2014 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_EnhancedGrid_Model_Columns_Customer_Columns extends Mage_Core_Model_Abstract
{

    /*
     * In order to add custom columns, rename the Columns.php.sample file to Columns.php
     *
     * In this function, you need to add your own columns then. The format is array(grid_type => array(your_columns))
     *
     * Below is an example for a column added for the GRID_SALES_ORDER type (grid types can be found in the Xtento_EnhancedGrid_Model_Grid class)
     *
     * Just copy the below array, and adjust all fields. Array is built as following:
     *
       'your_field_id' => array(
         'header' => Mage::helper('xtento_enhancedgrid')->__('Column Title'),
         'id' => 'your_field_id',
         'index' => 'joined_field_name',
         'filter_index' => 'joined_table_name.joined_field_name',
         'join_left' => array(
             'name' => array('joined_table_name' => Mage::getSingleton('core/resource')->getTableName('sales/order_payment')),
             'cond' => 'main_table.entity_id = joined_field_name.parent_id', // parent_id for example
             'cols' => array('joined_field_name' => 'joined_table_name.field_name_in_joined_table')
         ),
         'type' => 'options', // text, options, etc.
         'options' => Mage::helper('xtcore/payment')->getPaymentMethodList(true, false, false, null), // For example if a dropdown should be shown - array as seen in the getPaymentMethodList class
         'filter' => 'Xtento_EnhancedGrid_Block_Widget_Grid_Column_Filter_PaymentMethod', // Custom filter for column
         'renderer' => 'Xtento_EnhancedGrid_Block_Widget_Grid_Column_Renderer_PaymentMethod', // Custom renderer for class
         'change_renderer' => false, // Can the admin change the renderer in the grid customization section?
         'change_filter' => false // Can the admin change the filter in the grid customization section?
       )

        Sample for the "order weight" column, joined from the sales_flat_order table:

        'weight' => array(
            'header' => Mage::helper('xtento_enhancedgrid')->__('Order Weight'),
            'id' => 'weight',
            'index' => 'weight',
            'filter_index' => 'order.weight',
            'join_left' => array(
                'name' => array('order' => Mage::getSingleton('core/resource')->getTableName('sales/order')),
                'cond' => 'main_table.entity_id = order.entity_id',
                'cols' => array('weight' => 'order.weight')
            ),
            'type' => 'text'
        ),
     */
    public function getCustomColumns()
    {
        $customColumns = array(
            Xtento_EnhancedGrid_Model_Grid::GRID_SALES_ORDER => array(
                'order_type' => array(
                    'header' => Mage::helper('xtento_enhancedgrid')->__('Order Type'),
                    'id' => 'order_type',
                    'index' => 'order_type',
                    'filter_index' => 'order.order_type',
                    'join_left' => array(
                        'name' => array('order' => Mage::getSingleton('core/resource')->getTableName('sales/order')),
                        'cond' => 'main_table.entity_id = order.entity_id',
                        'cols' => array('order_type' => 'order.order_type')
                    ),
                    'type' => 'text'
                ), 
                'order_type_value' => array(
                    'header' => Mage::helper('xtento_enhancedgrid')->__('Order Type Value'),
                    'id' => 'order_type_value',
                    'index' => 'order_type_value',
                    'filter_index' => 'order.order_type_value',
                    'join_left' => array(
                        'name' => array('order' => Mage::getSingleton('core/resource')->getTableName('sales/order')),
                        'cond' => 'main_table.entity_id = order.entity_id',
                        'cols' => array('order_type_value' => 'order.order_type_value')
                    ),
                    'type' => 'text'
                ),
                'batch_number' => array(
                    'header' => Mage::helper('xtento_enhancedgrid')->__('Batch Number'),
                    'id' => 'batch_number',
                    'index' => 'batch_number',
                    'filter_index' => 'order.batch_number',
                    'join_left' => array(
                        'name' => array('order' => Mage::getSingleton('core/resource')->getTableName('sales/order')),
                        'cond' => 'main_table.entity_id = order.entity_id',
                        'cols' => array('batch_number' => 'order.batch_number')
                    ),
                    'type' => 'text'
                )
            )
        );

        return $customColumns;
    }
}