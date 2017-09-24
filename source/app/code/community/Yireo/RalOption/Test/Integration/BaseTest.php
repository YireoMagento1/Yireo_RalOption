<?php
class Yireo_RalOption_Test_Integration_BaseTest extends PHPUnit_Framework_TestCase
{
    public function testWhetherProductsExist()
    {
        $productCollection = $this->getProductCollection();
        $this->assertNotEmpty($productCollection->count());
    }

    /**
     * @return Mage_Catalog_Model_Resource_Product_Collection
     */
    private function getProductCollection()
    {
        return (new Yireo_RalOption_Test_Integration_DataProvider_ProductCollection)->getData();
    }
}