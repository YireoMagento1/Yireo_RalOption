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
 * Class Yireo_RalOption_Helper_Data
 */
class Yireo_RalOption_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Helper-method to fetch a specific value from the Magento Configuration
     *
     * @parameter string $key
     * @parameter mixed $default_value
     *
     * @return mixed
     */
    public function getConfigValue($key = null, $default_value = null)
    {
        $value = Mage::getStoreConfig($key);
        if (empty($value)) {
            $value = $default_value;
        }

        return $value;
    }

    /**
     * Helper-method to get the opposite-color based on color-contrast
     *
     * @parameter string $color
     * @parameter string $dark
     * @parameter string $light
     *
     * @return string
     */
    public function getOppositeColor($color, $dark = '#000000', $light = '#FFFFFF')
    {
        if (empty($color)) {
            return $dark;
        }

        return (hexdec($color) > 0xffffff / 2) ? $dark : $light;
    }

    /**
     * Helper-method to get the configured color-palette
     *
     * @return array
     */
    public function getCodes($paletteClassName = null)
    {
        if (empty($paletteClassName)) {
            $paletteClassName = $this->getConfigValue('catalog/raloption/palette');
        }

        $palette = $this->getPaletteInstance($paletteClassName);
        return $palette->getCodes();
    }

    /**
     * @return string
     */
    protected function getDefaultPaletteName()
    {
        $name = ucfirst($this->getConfigValue('catalog/raloption/palette'));

        if (empty($name)) {
            $name = 'Yireo_RalOption_Palette_Palette1';
        }

        return $name;
    }

    /**
     * @param $className string
     *
     * @return Yireo_RalOption_Api_PaletteInterface
     * @throws Exception
     */
    public function getPaletteInstance($className = null)
    {
        if (empty($className)) {
            $className = $this->getDefaultPaletteName();
        }

        if (empty($className)) {
            throw new Exception('Empty RalOption palette name');
        }

        if (!preg_match('/(\w+)_(\w+)_(\w+)_(\w+)/', $className)) {
            $className = 'Yireo_RalOption_Palette_' . $className;
        }

        $palette = new $className;

        if (!$palette instanceof Yireo_RalOption_Api_PaletteInterface) {
            throw new Exception('Unknown RalOption palette');
        }

        return new $palette;
    }

    /**
     * Helper-method to fetch a color by RAL-code
     *
     * @param string $code
     *
     * @return string
     */
    public function getColorByCode($code = null)
    {
        $codes = $this->getCodes();
        if (isset($codes[$code])) {
            return $codes[$code];
        }

        return null;
    }

    /**
     * Helper-method to fetch a RAL-code by color
     *
     * @param string $color
     *
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
     * @return array
     */
    public function getPriceRules()
    {
        $palette = $this->getPaletteInstance();
        return $palette->getPriceRules();
    }

    /**
     * Get a specific price for a specific RAL-code
     *
     * @param string $code
     * @param int|Mage_Catalog_Block_Product_Price $originalPrice
     * @param boolean $differenceOnly
     *
     * @return string
     */
    public function getPriceByCode($code, $originalPrice, $differenceOnly = true)
    {
        if (is_object($originalPrice)) {
            $originalPrice = $originalPrice->getPrice();
        }

        $priceRules = $this->getPriceRules();
        if (empty($priceRules)) {
            if ($differenceOnly == true) {
                return 0;
            } else {
                return $originalPrice;
            }
        }

        $newPrice = ($differenceOnly == false) ? $originalPrice : 0;
        foreach ($priceRules as $priceCode => $priceRule) {
            if ($priceCode == $code) {
                if (isset($priceRule['percentage'])) {
                    $newPrice = (($originalPrice / 100) * $priceRule['percentage']);
                    if ($differenceOnly == false) $newPrice = $originalPrice + $newPrice;
                } elseif (isset($priceRule['fixed'])) {
                    $newPrice = $priceRule['fixed'];
                    if ($differenceOnly == false) $newPrice = $originalPrice + $newPrice;
                } elseif (isset($priceRule['relative'])) {
                    $newPrice = $priceRule['relative'];
                    if ($differenceOnly == false) $newPrice = $originalPrice + $newPrice;
                }
            }
        }

        return $newPrice;
    }

    /**
     * Get a specific price for a specific RAL-code
     *
     * @param string $code
     * @param Mage_Catalog_Model_Product $product
     * @param boolean $differenceOnly
     *
     * @return string
     */
    public function getProductPriceByCode($code, Mage_Catalog_Model_Product $product, $differenceOnly = true)
    {
        $price = $this->getProductPrice($product);
        return $this->getPriceByCode($code, $price, $differenceOnly);
    }

    /**
     * Get the final price for a specific product
     *
     * @param Mage_Catalog_Model_Product $price
     *
     * @return string
     */
    public function getProductPrice(Mage_Catalog_Model_Product $product)
    {
        $price = $product->getSpecialPrice();
        if (!empty($price)) {
            return $price;
        }

        $price = $product->getFinalPrice();
        if (!empty($price)) {
            return $price;

        }

        $price = $product->getPrice();
        return $price;
    }
}
