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
 * Class Yireo_RalOption_Model_Observer
 */
class Yireo_RalOption_Model_Observer
{
    /**
     * Listen to the event core_block_abstract_to_html_before
     *
     * @param Varien_Event_Observer $observer
     *
     * @return $this
     * @deprecated Flush the cache
     */
    public function coreBlockAbstractToHtmlBefore($observer)
    {
        return $this;
    }

    /**
     * Listen to the event core_block_abstract_to_html_after
     *
     * @parameter Varien_Event_Observer $observer
     *
     * @return $this
     * @deprecated Flush the cache
     */
    public function coreBlockAbstractToHtmlAfter(Varien_Event_Observer $observer)
    {
        return $this;
    }

    /**
     * @param Varien_Event_Observer $observer
     *
     * @return mixed
     * @deprecated Flush the cache
     */
    public function salesQuoteItemSaveBefore(Varien_Event_Observer $observer)
    {
        return $this;
    }
}
