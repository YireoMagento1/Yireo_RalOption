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
 * Class Yireo_RalOption_Observer_AddOptionBlock
 */
class Yireo_RalOption_Observer_AddOptionBlock
{
    /**
     * @var Yireo_RalOption_Helper_Data
     */
    private $helper;

    /**
     * Yireo_RalOption_Model_Observer constructor.
     */
    public function __construct()
    {
        $this->helper = Mage::helper('raloption');
    }

    /**
     * Listen to the event core_block_abstract_to_html_before
     *
     * @param Varien_Event_Observer $observer
     *
     * @return $this
     */
    public function execute($observer)
    {
        // Check for the block
        $block = $observer->getEvent()->getBlock();
        if (!$this->isBlockAllowed($block)) {
            return $this;
        }

        // Get the option title
        $option = $block->getOption();
        if ($option->getData('type') !== 'ralcolor') {
            return $this;
        }

        $defaultValue = $this->getDefaultValueFromBlock($block);
        $option->setTitle($this->cleanOptionTitle($option->getTitle()));

        // Get the current product and use it to load a product-specific palette
        $paletteName = $this->getPaletteName();
        $palette = $this->helper->getPaletteInstance($paletteName);
        $defaultColor = $palette->getDefault();

        if (empty($defaultColor)) {
            $defaultColor = $this->helper->getColorByCode($defaultValue);
        }

        if (empty($defaultValue)) {
            $defaultValue = $this->helper->getCodeByColor($defaultColor);
        }

        $option->setDefaultValue($defaultValue);
        $option->setDefaultColor($defaultColor);

        // Set the RAL-codes
        $ralCodes = $this->helper->getCodes($paletteName);
        $ralCodesArray = $this->convertCodesToStringArray($ralCodes);
        $block->setRalCodesArray($ralCodesArray);

        // Override all existing option-values
        $newValues = $this->convertCodesToArray($ralCodes);
        $option->setValues($newValues);

        // Set the matrix
        $matrix = $this->getMatrixFromPalette($paletteName);
        $block->setRalCodesMatrix($matrix);

        // Modify the original block
        $block->setTemplate('raloption/type.phtml');
        $block->setDisclaimer($this->helper->getConfigValue('catalog/raloption/disclaimer'));
        $block->setOption($option);

        return $this;
    }

    /**
     * @param string $optionTitle
     *
     * @return string
     */
    private function cleanOptionTitle($optionTitle)
    {
        return preg_replace('/\{\{RAL([^\}]{0,})\}\}/', '', $optionTitle);
    }

    /**
     * @param Mage_Core_Block_Abstract $block
     *
     * @return string
     */
    private function getDefaultValueFromBlock(Mage_Core_Block_Abstract $block)
    {
        $option = $block->getOption();
        $optionTitle = $option->getTitle();
        $defaultValue = trim((string)$block->getDefaultValue());

        if (preg_match('/\{\{RAL([^\}]{0,})\}\}/', $optionTitle, $optionMatch)) {
            $defaultValue = (!empty($optionMatch[1])) ? trim($optionMatch[1]) : '';
        }

        return $defaultValue;
    }

    /**
     * @return string
     */
    private function getPaletteName()
    {
        $currentProduct = $this->getCurrentProduct();
        if (!empty($currentProduct)) {
            return $currentProduct->getData('raloption_palette');
        }

        return '';
    }

    /**
     * @return Mage_Catalog_Model_Product|null
     */
    private function getCurrentProduct()
    {
        return Mage::registry('current_product');
    }

    /**
     * @param Mage_Core_Block_Abstract $block
     *
     * @return bool
     */
    private function isBlockAllowed(Mage_Core_Block_Abstract $block)
    {
        if (!strstr(get_class($block), 'Mage_Catalog_Block_Product_View_Options_Type')) {
            return false;
        }

        return true;
    }

    /**
     * @param array $codes
     *
     * @return array
     */
    private function convertCodesToArray($codes)
    {
        $return = array();
        foreach (array_keys($codes) as $code) {
            $return[$code] = $code;
        }

        return $return;
    }

    /**
     * @param array $codes
     *
     * @return array
     */
    private function convertCodesToStringArray($codes)
    {
        $return = array();
        foreach (array_keys($codes) as $code) {
            $return[] = "'" . $code . "'";
        }

        return $return;
    }

    /**
     * @param string $paletteName
     *
     * @return array
     */
    private function getMatrixFromPalette($paletteName)
    {
        $matrix = $this->helper->getCodes($paletteName);
        $matrixSorting = $this->helper->getConfigValue('catalog/raloption/sorting');
        if ($matrixSorting == 'ral') {
            ksort($matrix);
        }

        if ($matrixSorting == 'color') {
            asort($matrix);
        }

        return $matrix;
    }
}
