<?php
/**
 * RalOption plugin for Magento
 *
 * @package     Yireo_RalOption
 * @author      Yireo (https://www.yireo.com/)
 * @copyright   Copyright 2016 Yireo (https://www.yireo.com/)
 * @license     Open Source License (OSL v3)
 */

/**
 * Class Yireo_RalOption_Block_Adminhtml_Catalog_Product_Edit_Tab_Options_Option
 *
 * Yireo mod: Simple override to allow for changing the template
 */
class Yireo_RalOption_Block_Adminhtml_Catalog_Product_Edit_Tab_Options_Option extends
    Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Options_Option
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('raloption/rewrite/catalog/product/edit/options/option.phtml');
    }
}