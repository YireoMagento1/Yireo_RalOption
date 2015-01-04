<?php
/**
 * RalOption plugin for Magento 
 *
 * @package     Yireo_RalOption
 * @author      Yireo (http://www.yireo.com/)
 * @copyright   Copyright 2015 Yireo (http://www.yireo.com/)
 * @license     Open Source License (OSL v3)
 */

class Yireo_RalOption_Model_Observer
{
    /*
     * Listen to the event core_block_abstract_to_html_before
     * 
     * @access public
     * @parameter Varien_Event_Observer $observer
     * @return $this
     */
    public function coreBlockAbstractToHtmlBefore($observer)
    {
        // Check for the block
        $block = $observer->getEvent()->getBlock();
        if(strstr(get_class($block), 'Mage_Catalog_Block_Product_View_Options_Type') == false) {
            return $this;
        }

        // Get the option title
        $option = $block->getOption();
        $optionTitle = $option->getTitle();

        // Determine the default value
        if($block->getDefaultValue() != '') {
            $defaultValue = $block->getDefaultValue();
        }

        // Interpret the {{RAL}} tag
        if(preg_match('/\{\{RAL([^\}]{0,})\}\}/', $optionTitle, $optionMatch)) {
            $defaultValue = (!empty($optionMatch[1])) ? trim($optionMatch[1]) : null; 
            $optionTitle = trim(str_replace($optionMatch[0], '', $optionTitle));

        // Select by ralcolor type
        } else {
            if($option->getData('type') != 'ralcolor') {
                return $this;
            }
        }

        // Remove the tag from this title
        $option->setTitle($optionTitle);

        // Get the current product and use it to load a product-specific palette
        $current_product = Mage::registry('current_product');
        if(!empty($current_product)) {
            $helperName = $current_product->getData('raloption_palette');
        }

        // Get the helper
        $helper = Mage::helper('raloption')->getHelper($helperName);

        // Set default values
        $defaultColor = null;
        if(method_exists($helper, 'getDefault')) $defaultColor = $helper->getDefault();
        if(empty($defaultColor)) $defaultColor = Mage::helper('raloption')->getColorByCode($defaultValue);
        if(empty($defaultValue)) $defaultValue = Mage::helper('raloption')->getCodeByColor($defaultColor);
        $option->setDefaultValue($defaultValue);
        $option->setDefaultColor($defaultColor);

        // Set the RAL-codes
        $ralcodes = array();
        foreach(array_keys(Mage::helper('raloption')->getCodes($helperName)) as $ralcode) {
            $ralcodes[] = "'".$ralcode."'";
        }
        $block->setRalCodesArray($ralcodes);

        // Override all existing option-values
        $newValues = array();
        foreach(array_keys(Mage::helper('raloption')->getCodes($helperName)) as $ralcode) {
            $newValues[$ralcode] = $ralcode; // Set a new price, instead of a new value
        }
        $option->setValues($newValues);

        // Set the matrix
        $matrix = Mage::helper('raloption')->getCodes($helperName);
        $matrixSorting = Mage::helper('raloption')->getConfigValue('catalog/raloption/sorting');
        if($matrixSorting == 'ral') ksort($matrix);
        if($matrixSorting == 'color') asort($matrix);
        $block->setRalCodesMatrix($matrix);

        // Modify the original block
        $block->setTemplate('raloption/type.phtml');
        $block->setDisclaimer(Mage::helper('raloption')->getConfigValue('catalog/raloption/disclaimer'));
        $block->setOption($option);

        return $this;
    }

    /*
     * Listen to the event core_block_abstract_to_html_after
     * 
     * @access public
     * @parameter Varien_Event_Observer $observer
     * @return $this
     */
    public function coreBlockAbstractToHtmlAfter($observer)
    {
        $request = Mage::app()->getRequest();
        if($request && strstr($request->getRequestString(), '/catalog_product/')) {
            return $this;
        }

        $transport = $observer->getEvent()->getTransport();
        $block = $observer->getEvent()->getBlock();

        $matchBlocks = array('root', 'items');
        if(in_array($block->getNameInLayout(), $matchBlocks)) {
            $layout = Mage::app()->getLayout();
            $html = $transport->getHtml();
            $html = preg_replace('/\{\{RAL([^\}]{0,})\}\}/', '', $html);
            $transport->setHtml($html);
        }

        return $this;
    }

    public function salesQuoteItemSaveBefore($observer)
    {
        $event = $observer->getEvent();
        $quoteItem = $event->getDataObject();
        if(empty($quoteItem)) $quoteItem = $event->getObject();
        $product = $quoteItem->getProduct();

        // Loop through the current items options, to detect a RAL-option
        $ralOptionMatch = false;
        $ralValue = null;
        foreach($quoteItem->getOptions() as $option) {

            $optionId = preg_replace('/^option_/', '', $option->getData('code'));
            $product = $option->getProduct();

            // Loop through the configured product-options to match this option
            foreach($product->getOptions() as $productOption) {
                if($productOption->getData('option_id') == $optionId) {
                    if(preg_match('/\{\{RAL([^\}]{0,})\}\}/', $productOption->getData('title'))) {
                        $ralOptionMatch = true;
                        break;
                    }
                }
            }

            // @todo: Check whether RAL-option ordered is correct

            // Set the current RAL-value
            if($ralOptionMatch == true) {
                $ralValue = $option->getData('value');
                break;
            }
        }

        // Get the current price
        $product = Mage::getModel('catalog/product')->load($quoteItem->getProduct()->getId());
        $originalQuoteItemPrice = $quoteItem->getOriginalPrice();

        // Search for a modified price with the current RAL-value 
        $newPrice = Mage::helper('raloption')->getProductPriceByCode($ralValue, $product);
        if($newPrice != 0) {
            $newPrice = $originalQuoteItemPrice + $newPrice;
            $quoteItem->setOriginalCustomPrice($newPrice);
        }

        return $this;
    }
}
