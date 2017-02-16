<?php
/**
 * RalOption plugin for Magento
 *
 * @category    design_default
 * @package     Yireo_RalOption
 * @author      Yireo (https://www.yireo.com/)
 * @copyright   Copyright 2016 Yireo (https://www.yireo.com/)
 * @license     Open Source License (OSL v3)
 */

/**
 * Class Yireo_RalOption_Model_Catalog_Product_Option
 */
class Yireo_RalOption_Model_Catalog_Product_Option extends Mage_Catalog_Model_Product_Option
{
    /**
     * Override of original method
     *
     * @param null $type
     *
     * @return string
     */
    public function getGroupByType($type = null)
    {
        if($type == 'ralcolor') {
            return self::OPTION_GROUP_TEXT;
        }

        return parent::getGroupByType($type);
    }
}
