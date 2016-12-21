<?php
/**
 * RalOption plugin for Magento
 *
 * @package     Yireo_RalOption
 * @author      Yireo (https://www.yireo.com/)
 * @copyright   Copyright 2015 Yireo (https://www.yireo.com/)
 * @license     Open Source License (OSL v3)
 */

/**
 * Class Yireo_RalOption_Model_Observer
 */
class Yireo_RalOption_Model_Observer
{
    /**
     * @var Yireo_RalOption_Helper_Data
     */
    protected $helper;

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
    public function coreBlockAbstractToHtmlBefore($observer)
    {
        // Check for the block
        $block = $observer->getEvent()->getBlock();
        if (strstr(get_class($block), 'Mage_Catalog_Block_Product_View_Options_Type') == false) {
            return $this;
        }

        // Get the option title
        $option = $block->getOption();
        $optionTitle = $option->getTitle();

        // Determine the default value
        if ($block->getDefaultValue() != '') {
            $defaultValue = $block->getDefaultValue();
        }

        // Interpret the {{RAL}} tag
        if (preg_match('/\{\{RAL([^\}]{0,})\}\}/', $optionTitle, $optionMatch)) {
            $defaultValue = (!empty($optionMatch[1])) ? trim($optionMatch[1]) : null;
            $optionTitle = trim(str_replace($optionMatch[0], '', $optionTitle));

            // Select by ralcolor type
        } else {
            if ($option->getData('type') != 'ralcolor') {
                return $this;
            }
        }

        // Remove the tag from this title
        $option->setTitle($optionTitle);

        // Get the current product and use it to load a product-specific palette
        $current_product = Mage::registry('current_product');
        $helperName = '';
        if (!empty($current_product)) {
            $helperName = $current_product->getData('raloption_palette');
        }

        // Get the helper
        $palette = $this->helper->getPaletteInstance($helperName);

        // Set default values
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
        $ralcodes = array();
        foreach (array_keys($this->helper->getCodes($helperName)) as $ralcode) {
            $ralcodes[] = "'" . $ralcode . "'";
        }

        $block->setRalCodesArray($ralcodes);

        // Override all existing option-values
        $newValues = array();
        foreach (array_keys($this->helper->getCodes($helperName)) as $ralcode) {
            $newValues[$ralcode] = $ralcode; // Set a new price, instead of a new value
        }

        $option->setValues($newValues);

        // Set the matrix
        $matrix = $this->helper->getCodes($helperName);
        $matrixSorting = $this->helper->getConfigValue('catalog/raloption/sorting');
        if ($matrixSorting == 'ral') {
            ksort($matrix);
        }

        if ($matrixSorting == 'color') {
            asort($matrix);
        }

        $block->setRalCodesMatrix($matrix);

        // Modify the original block
        $block->setTemplate('raloption/type.phtml');
        $block->setDisclaimer($this->helper->getConfigValue('catalog/raloption/disclaimer'));
        $block->setOption($option);

        return $this;
    }

    /**
     * Listen to the event core_block_abstract_to_html_after
     *
     * @parameter Varien_Event_Observer $observer
     *
     * @return $this
     */
    public function coreBlockAbstractToHtmlAfter($observer)
    {
        $request = Mage::app()->getRequest();
        if ($request && strstr($request->getRequestString(), '/catalog_product/')) {
            return $this;
        }

        $transport = $observer->getEvent()->getTransport();
        $block = $observer->getEvent()->getBlock();

        $matchBlocks = array('root', 'items');
        if (in_array($block->getNameInLayout(), $matchBlocks)) {
            $html = $transport->getHtml();
            $html = preg_replace('/\{\{RAL([^\}]{0,})\}\}/', '', $html);
            $transport->setHtml($html);
        }

        return $this;
    }

    /**
     * @param $observer
     *
     * @return mixed
     * @deprecated
     */
    public function salesQuoteItemSaveBefore($observer)
    {
        $newObserver = new Yireo_RalOption_Observer_Quote_PriceHandler;
        return $newObserver->salesQuoteItemSaveBefore($observer);
    }
}
