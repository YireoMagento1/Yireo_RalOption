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
 * Class Yireo_RalOption_Observer_ReplaceTags
 */
class Yireo_RalOption_Observer_ReplaceTags
{
    /**
     * @var Mage_Core_Controller_Request_Http
     */
    private $request;

    /**
     * Yireo_RalOption_Observer_ReplaceTags constructor.
     */
    public function __construct()
    {
        $this->request = Mage::app()->getRequest();
    }

    /**
     * Listen to the event core_block_abstract_to_html_after
     *
     * @parameter Varien_Event_Observer $observer
     *
     * @return $this
     */
    public function execute(Varien_Event_Observer $observer)
    {
        if ($this->isProductPage()) {
            return $this;
        }

        $transport = $observer->getEvent()->getTransport();
        $block = $observer->getEvent()->getBlock();

        if ($this->isBlockAllowed($block)) {
            $html = $transport->getHtml();
            $html = $this->removeRalCode($html);
            $transport->setHtml($html);
        }

        return $this;
    }

    /**
     * @param string $string
     *
     * @return string
     */
    private function removeRalCode($string)
    {
        return preg_replace('/\{\{RAL([^\}]{0,})\}\}/', '', $string);
    }

    /**
     * @return bool
     */
    private function isProductPage()
    {
        if ($this->request && strstr($this->request->getRequestString(), '/catalog_product/')) {
            return true;
        }

        return false;
    }

    /**
     * @param Mage_Core_Block_Abstract $block
     *
     * @return bool
     */
    private function isBlockAllowed(Mage_Core_Block_Abstract $block)
    {
        $matchBlocks = array('root', 'items');
        if (in_array($block->getNameInLayout(), $matchBlocks)) {
            return true;
        }

        return false;
    }
}
