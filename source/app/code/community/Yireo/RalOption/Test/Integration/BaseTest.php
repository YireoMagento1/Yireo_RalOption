<?php
class Yireo_RalOption_Test_Integration_BaseTest extends PHPUnit_Framework_TestCase
{
    public function testWhetherProductsExist()
    {
        $productCollection = $this->getProductCollection();
        $this->assertNotEmpty($productCollection->count());
    }

    private function getProductCollection()
    {
        static $collection = null;

        if ($collection !== null) {
            return $collection;
        }

        $collection = Mage::getModel('catalog/product')->getCollection();
        $collection->addAttributeToFilter('raloption_palette', array('neq' => ''));
        return $collection;
    }
}