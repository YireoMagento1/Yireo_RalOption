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
 * Class Yireo_RalOption_Observer_Quote_PriceHandler
 */
class Yireo_RalOption_Observer_Quote_PriceHandler
{
    /**
     * @var Yireo_RalOption_Helper_Data
     */
    protected $helper;

    /**
     * Yireo_RalOption_Observer_Quote_PriceHandler constructor.
     */
    public function __construct()
    {
        $this->helper = Mage::helper('raloption');
    }

    /**
     * @param Varien_Event_Observer $observer
     *
     * @return $this
     * @deprecated Use execute() instead
     */
    public function salesQuoteItemSaveBefore(Varien_Event_Observer $observer)
    {
        return $this;
    }

    /**
     * @param Varien_Event_Observer $observer
     *
     * @return $this
     */
    public function execute(Varien_Event_Observer $observer)
    {
        $event = $observer->getEvent();
        $quoteItem = $this->getQuoteItemFromEvent($event);

        // Loop through the current items options, to detect a RAL-option
        $ralValue = '';
        foreach ($quoteItem->getOptions() as $option) {
            $ralValue = (string) $this->getRalValueFromOption($option);
            if (!empty($ralValue)) {
                break;
            }
        }

        /** @var Mage_Catalog_Model_Product $product */
        $product = Mage::getModel('catalog/product')->load($quoteItem->getProduct()->getId());

        // Search for a modified price with the current RAL-value
        $newPrice = $this->helper->setProduct($product)->getPriceByCode($ralValue, false);
        if ($newPrice > 0) {
            $quoteItem->setOriginalCustomPrice($newPrice);
        }

        return $this;
    }

    /**
     * @param Mage_Sales_Model_Quote_Item_Option $option
     *
     * @return string
     */
    protected function getRalValueFromOption(Mage_Sales_Model_Quote_Item_Option $option)
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
    protected function getOptionIdFromOption($option)
    {
        return (int)preg_replace('/^option_/', '', $option->getData('code'));
    }

    /**
     * @param Varien_Object $option
     *
     * @return bool
     */
    protected function isRalOption(Varien_Object $option)
    {
        if (preg_match('/\{\{RAL([^\}]{0,})\}\}/', $option->getData('title'))) {
            return true;
        }

        if ($option->getData('type') === 'ralcolor') {
            return true;
        }

        return false;
    }

    /**
     * @param Varien_Event $event
     *
     * @return Mage_Sales_Model_Quote_Item
     */
    protected function getQuoteItemFromEvent(Varien_Event $event)
    {
        $quoteItem = $event->getDataObject();
        if (!empty($quoteItem)) {
            return $quoteItem;
        }

        $quoteItem = $event->getObject();
        return $quoteItem;
    }
}
