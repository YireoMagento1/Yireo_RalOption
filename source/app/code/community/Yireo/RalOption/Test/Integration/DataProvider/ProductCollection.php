<?php
class Yireo_RalOption_Test_Integration_DataProvider_ProductCollection
{
    /**
     * @return Mage_Catalog_Model_Resource_Product_Collection
     */
    public function getData()
    {
        static $collection = null;

        if ($collection !== null) {
            return $collection;
        }

        /** @var Mage_Catalog_Model_Resource_Product_Collection $collection */
        $collection = Mage::getModel('catalog/product')->getCollection();
        $collection->addAttributeToFilter('raloption_palette', array('like' => 'Yireo_%'))
            ->addAttributeToSelect('raloption_palette')
            ->setPageSize(1)
            ->setCurPage(0);

        return $collection;
    }
}