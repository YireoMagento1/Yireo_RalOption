<?php
/**
 * RalOption plugin for Magento
 *
 * @package     Yireo_RalOption
 * @author      Yireo (https://www.yireo.com/)
 * @copyright   Copyright 2017 Yireo (https://www.yireo.com/)
 * @license     Open Source License (OSL v3)
 */

/**
 * Class Yireo_RalOption_Helper_PriceHandler
 */
class Yireo_RalOption_Helper_PriceHandler
{
    /**
     * @var
     */
    private $product;

    /**
     * @var
     */
    private $palette;

    /**
     * @param Mage_Catalog_Model_Product $product
     *
     * @return $this
     */
    public function setProduct(Mage_Catalog_Model_Product $product)
    {
        $this->product = $product;
        return $this;
    }

    /**
     * @param Yireo_RalOption_Api_PaletteInterface $palette
     *
     * @return $this
     */
    public function setPalette(Yireo_RalOption_Api_PaletteInterface $palette)
    {
        $this->palette = $palette;
        return $this;
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
        if (!$this->product instanceof Mage_Catalog_Model_Product) {
            throw new InvalidArgumentException(self::class . '::getPriceByCode() requires a valid product');
        }

        if (!$this->palette instanceof Yireo_RalOption_Api_PaletteInterface) {
            throw new InvalidArgumentException(self::class . '::getPriceByCode() requires a valid palette');
        }

        $originalPrice = (double)$this->getProductPrice();

        $priceRules = $this->palette->getPriceRules();
        if (empty($priceRules)) {
            return (!$differenceOnly) ? $originalPrice : 0;
        }

        $newPrice = ($differenceOnly === false) ? $originalPrice : 0;

        foreach ($priceRules as $priceCode => $priceRule) {
            if ((string) $priceCode !== $code) {
                continue;
            }

            if (isset($priceRule['percentage'])) {
                $newPrice = (($originalPrice / 100) * $priceRule['percentage']);
                if ($differenceOnly === false) {
                    $newPrice = $originalPrice + $newPrice;
                }
            }

            if (isset($priceRule['fixed'])) {
                $newPrice = $priceRule['fixed'];
                if ($differenceOnly == false) {
                    $newPrice = $originalPrice + $newPrice;
                }
            }

            if (isset($priceRule['relative'])) {
                $newPrice = $priceRule['relative'];
                if ($differenceOnly == false) {
                    $newPrice = $originalPrice + $newPrice;
                }
            }
        }

        return $newPrice;
    }

    /**
     * Get the final price for a specific product
     *
     * @return string
     */
    private function getProductPrice()
    {
        $price = $this->product->getSpecialPrice();
        if (!empty($price)) {
            return $price;
        }

        $price = $this->product->getFinalPrice();
        if (!empty($price)) {
            return $price;

        }

        $price = $this->product->getPrice();
        return $price;
    }

    /**
     * @param Mage_Sales_Model_Quote_Item_Option $option
     *
     * @return string
     */
    public function getRalValueFromOption(Mage_Sales_Model_Quote_Item_Option $option)
    {
        $optionId = $this->getOptionIdFromOption($option);

        /** @var Mage_Catalog_Model_Product $product */
        $product = $option->getProduct();

        // Loop through the configured product-options to match this option
        foreach ($product->getOptions() as $productOption) {
            /** @var Varien_Object $productOption */
            if ($productOption->getData('option_id') == $optionId && $this->isRalOption($productOption)) {
                return $option->getData('value');
            }
        }

        return '';
    }

    /**
     * @param Varien_Object $option
     *
     * @return int
     */
    private function getOptionIdFromOption($option)
    {
        return (int)preg_replace('/^option_/', '', $option->getData('code'));
    }


    /**
     * @param Varien_Object $option
     *
     * @return bool
     */
    private function isRalOption(Varien_Object $option)
    {
        if (preg_match('/\{\{RAL([^\}]{0,})\}\}/', $option->getData('title'))) {
            return true;
        }

        if ($option->getData('type') === 'ralcolor') {
            return true;
        }

        return false;
    }
}
