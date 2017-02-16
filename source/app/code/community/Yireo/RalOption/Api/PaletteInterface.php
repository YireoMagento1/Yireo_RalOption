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
 * Interface Yireo_RalOption_Api_PaletteInterface
 */
interface Yireo_RalOption_Api_PaletteInterface
{
    /**
     * Helper-method to return the palette-codes
     *
     * @parameter null
     * @return array
     */
    public function getCodes();

    /**
     * Helper-method to return the palette-codes
     *
     * @parameter null
     * @return array
     */
    public function getPriceRules();

    /**
     * Helper-method to return the default color
     *
     * @parameter null
     * @return array
     */
    public function getDefault();
}
