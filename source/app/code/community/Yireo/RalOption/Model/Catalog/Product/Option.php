<?php

/**
 * Class Yireo_RalOption_Model_Catalog_Product_Option
 */
class Yireo_RalOption_Model_Catalog_Product_Option extends Mage_Catalog_Model_Product_Option
{
    public function getGroupByType($type = null)
    {
        if($type == 'ralcolor') {
            return self::OPTION_GROUP_TEXT;
        }
        return parent::getGroupByType($type);
    }
}
