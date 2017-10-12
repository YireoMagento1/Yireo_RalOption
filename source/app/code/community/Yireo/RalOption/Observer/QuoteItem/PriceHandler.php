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
 * Class Yireo_RalOption_Observer_QuoteItem_PriceHandler
 */
class Yireo_RalOption_Observer_QuoteItem_PriceHandler
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
     */
    public function execute(Varien_Event_Observer $observer)
    {
        $event = $observer->getEvent();
        $quoteItem = $this->getQuoteItemFromEvent($event);

        $ralValue = $this->getRalValueFromOptions($quoteItem);
        if (empty($ralValue)) {
            return $this;
        }

        /** @var Mage_Catalog_Model_Product $product */
        $product = Mage::getModel('catalog/product')->load($quoteItem->getProduct()->getId());

        $newPrice = $this->helper->setProduct($product)->getPriceByCode($ralValue, true);
        if ($newPrice !== 0) {
            $this->updateQuoteItemPrice($quoteItem, $newPrice);
        }

        if ($quoteItem->getRalOptionUpdateCart()) {
            $quoteItem->setRalOptionUpdateCart(false);
            $quote = $this->getQuote();
            $quote->setTotalsCollectedFlag(false);
            $quote->collectTotals();
            $quote->save();
        }

        return $this;
    }

    /**
     * @param Mage_Sales_Model_Quote_Item $quoteItem
     * @param $priceDiference
     *
     * @return bool
     * @throws Exception
     */
    protected function updateQuoteItemPrice(Mage_Sales_Model_Quote_Item $quoteItem, $priceDiference)
    {
        $originalPrice = $quoteItem->getOriginalPrice();

        if (empty($originalPrice)) {
            $originalPrice = $quoteItem->getCalculationPrice();
        }

        if (empty($originalPrice)) {
            throw new Exception('Could not locate original price');
        }

        $quoteItem->setCustomPrice($originalPrice + $newPrice);
        $quoteItem->setOriginalCustomPrice($originalPrice + $newPrice);
        return true;
    }

    /**
     * @param Mage_Sales_Model_Quote_Item $quoteItem
     *
     * @return string
     */
    private function getRalValueFromOptions(Mage_Sales_Model_Quote_Item $quoteItem)
    {
        foreach ($quoteItem->getOptions() as $option) {
            $ralValue = (string)$this->getRalValueFromOption($option);
            if (!empty($ralValue)) {
                return $ralValue;
            }
        }

        return '';
    }

    /**
     * @param Mage_Sales_Model_Quote_Item_Option $option
     *
     * @return string
     */
    private function getRalValueFromOption(Mage_Sales_Model_Quote_Item_Option $option)
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

    /**
     * @param Varien_Event $event
     *
     * @return Mage_Sales_Model_Quote_Item
     * @throws Exception
     */
    private function getQuoteItemFromEvent(Varien_Event $event)
    {
        $quoteItem = $event->getQuoteItem();
        if ($quoteItem instanceof Mage_Sales_Model_Quote_Item) {
            return $quoteItem;
        }

        $quoteItem = $event->getDataObject();
        if ($quoteItem instanceof Mage_Sales_Model_Quote_Item) {
            return $quoteItem;
        }

        $quoteItem = $event->getObject();
        if ($quoteItem instanceof Mage_Sales_Model_Quote_Item) {
            return $quoteItem;
        }

        $quote = $this->getQuote();
        $product = $event->getProduct();
        $quoteItem = $quote->getItemByProduct($product);
        if ($quoteItem instanceof Mage_Sales_Model_Quote_Item) {
            $quoteItem->setRalOptionUpdateCart(true);
            return $quoteItem;
        }

        $data = $event->getData();
        throw new Exception('Could not locate quote item: ' . implode(',', array_keys($data)));
    }

    /**
     * @return Mage_Sales_Model_Quote
     */
    private function getQuote()
    {
        /** @var Mage_Checkout_Model_Cart $cart */
        $cart = Mage::getModel('checkout/cart');
        return $cart->getQuote();
    }
}
