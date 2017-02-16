<?php
/**
 * RalOption plugin for Magento 
 *
 * @category    design_default
 * @package     Yireo_RalOption
 * @author      Yireo (https://www.yireo.com/)
 * @copyright   Copyright 2015 Yireo (https://www.yireo.com/)
 * @license     Open Source License (OSL v3)
 */

/**
 * Class Yireo_RalOption_Model_Backend_Source_Sorting
 */
class Yireo_RalOption_Model_Backend_Source_Sorting
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => 'default', 'label'=> Mage::helper('raloption')->__('Default')),
            array('value' => 'ral', 'label'=> Mage::helper('raloption')->__('RAL-code')),
            array('value' => 'color', 'label'=> Mage::helper('raloption')->__('RGB-color')),
        );
    }

}
