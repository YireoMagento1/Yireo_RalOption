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
     * @param double $currency
     * @param bool $showPrefix
     *
     * @return string
     */
    public function currency($currency, $showPrefix = true)
    {
        $prefix = '';
        if ($showPrefix) {
            $prefix = ($currency < 0) ? '-' : '+';
            $currency = abs($currency);
        }

        /** @var Mage_Core_Helper_Data $coreHelper */
        $coreHelper = Mage::helper('core');
        return $prefix . $coreHelper->currency($currency);
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
     * @param $paletteName string
     *
     * @return Yireo_RalOption_Api_PaletteInterface
     * @throws Exception
     */
    public function getPaletteInstance($paletteName = null)
    {
        if (empty($paletteName)) {
            $product = $this->getProduct();
            $paletteName = $product->getRaloptionPalette();
        }

        if (empty($paletteName)) {
            $paletteName = $this->getDefaultPaletteName();
        }

        if (empty($paletteName)) {
            throw new Exception('Empty RalOption palette name');
        }

        if (!preg_match('/(\w+)_(\w+)_(\w+)_(\w+)/', $paletteName)) {
            $paletteName = 'Yireo_RalOption_Palette_' . $paletteName;
        }

        $palette = new $paletteName;

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
     * Get a specific price for a specific RAL-code
     *
     * @param string $code
     * @param bool $differenceOnly
     *
     * @return string
     */
    public function getPriceByCode($code, $differenceOnly = true)
    {
        $product = $this->getProduct();
        $palette = $this->getPaletteInstance();

        /** @var Yireo_RalOption_Helper_PriceHandler $priceHandler */
        $priceHandler = Mage::helper('raloption/priceHandler');
        $priceHandler->setProduct($product)->setPalette($palette);

        $price = $priceHandler->getPriceByCode($code, $differenceOnly);
        return $price;
    }

    /**
     * @return mixed
     */
    private function getProduct()
    {
        $product = Mage::registry('current_product');
        if (empty($product)) {
            throw new InvalidArgumentException('No valid product found');
        }

        return $product;
    }
}
