<?php
/**
 * RalOption plugin for Magento 
 *
 * @package     Yireo_RalOption
 * @author      Yireo (https://www.yireo.com/)
 * @copyright   Copyright 2015 Yireo (https://www.yireo.com/)
 * @license     Open Source License (OSL v3)
 */

class Yireo_RalOption_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Helper-method to fetch a specific value from the Magento Configuration
     *
     * @parameter string $key
     * @parameter mixed $default_value
     * @return mixed
     */
    public function getConfigValue($key = null, $default_value = null)
    {
        $value = Mage::getStoreConfig($key);
        if(empty($value)) $value = $default_value;
        return $value;
    }

    /**
     * Helper-method to get the opposite-color based on color-contrast
     *
     * @parameter string $color
     * @parameter string $dark
     * @parameter string $light
     * @return string
     */
    public function getOppositeColor($color, $dark = '#000000', $light = '#FFFFFF')
    {
        if(empty($color)) return $dark;
        return (hexdec($color) > 0xffffff/2) ? $dark : $light;
    }

    /**
     * Helper-method to get the palette helper
     *
     * @parameter null
     * @return array
     */
    public function getHelper($helperName = null)
    {
        if(empty($helperName)) {
            $helperName = $this->getConfigValue('catalog/raloption/palette');
        }

        $helper = Mage::helper('raloption/'.$helperName);
        return $helper;
    }

    /**
     * Helper-method to get the configured color-palette
     *
     * @parameter null
     * @return array
     */
    public function getCodes($helperName = null)
    {
        if(empty($helperName)) {
            $helperName = $this->getConfigValue('catalog/raloption/palette');
        }

        $helper = Mage::helper('raloption/'.$helperName);
        if(method_exists($helper, 'getCodes')) {
            return $helper->getCodes();
        }
        return array();
    }

    /**
     * Helper-method to fetch a color by RAL-code
     *
     * @parameter string $code
     * @return string
     */
    public function getColorByCode($code = null)
    {
        $codes = $this->getCodes();
        if(isset($codes[$code])) {
            return $codes[$code];
        }
        return null;
    }

    /**
     * Helper-method to fetch a RAL-code by color
     *
     * @parameter string $color
     * @return string
     */
    public function getCodeByColor($color = null)
    {
        $codes = $this->getCodes();
        return array_search($color, $codes);
    }

    /**
     * Helper-method to get the configured price-rules
     *
     * @parameter null
     * @return array
     */
    public function getPriceRules()
    {
        $helperName = $this->getConfigValue('catalog/raloption/palette');
        $helper = Mage::helper('raloption/'.$helperName);
        if(method_exists($helper, 'getPriceRules')) {
            return $helper->getPriceRules();
        }
        return array();
    }

    /**
     * Get a specific price for a specific RAL-code
     *
     * @parameter string $code
     * @parameter int $originalPrice
     * @parameter boolean $differenceOnly
     * @return string
     */
    public function getPriceByCode($code, $originalPrice, $differenceOnly = true)
    {
        if (is_object($originalPrice)) {
            $originalPrice = $originalPrice->getPrice();
        }

        $priceRules = $this->getPriceRules();
        if(empty($priceRules)) {
            if($differenceOnly == true) {
                return 0;
            } else {
                return $originalPrice;
            }
        }

        $newPrice = ($differenceOnly == false) ? $originalPrice : 0;
        foreach($priceRules as $priceCode => $priceRule) {
            if($priceCode == $code) {
                if(isset($priceRule['percentage'])) {
                    $newPrice = (($originalPrice / 100) * $priceRule['percentage']);
                    if($differenceOnly == false) $newPrice = $originalPrice + $newPrice;
                } elseif(isset($priceRule['fixed'])) {
                    $newPrice = $priceRule['fixed'];
                    if($differenceOnly == false) $newPrice = $originalPrice + $newPrice;
                } elseif(isset($priceRule['relative'])) {
                    $newPrice = $priceRule['relative'];
                    if($differenceOnly == false) $newPrice = $originalPrice + $newPrice;
                }
            }
        }

        return $newPrice;
    }
    
    /**
     * Get a specific price for a specific RAL-code
     *
     * @parameter string $code
     * @parameter Mage_Catalog_Model_Product $product
     * @parameter boolean $differenceOnly
     * @return string
     */
    public function getProductPriceByCode($code, $product, $differenceOnly = true)
    {
        $price = $this->getProductPrice($product);
        return $this->getPriceByCode($code, $price, $differenceOnly);
    }

    /**
     * Get the final price for a specific product
     *
     * @parameter Mage_Catalog_Model_Product $price
     * @return string
     */
    public function getProductPrice($product)
    {
        $price = $product->getSpecialPrice();
        if(empty($price)) $price = $product->getFinalPrice();
        if(empty($price)) $price = $product->getPrice();
        return $price;
    }
}
