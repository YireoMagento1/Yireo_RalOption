<?php
class Yireo_RalOption_Test_Integration_Scenarios_AddProductToCartTest extends PHPUnit_Framework_TestCase
{
    public function testAddProductToCart()
    {
        /** @var Mage_Customer_Model_Session $session */
        $session = Mage::getSingleton('customer/session');

        /** @var Mage_Checkout_Model_Cart $cart */
        $cart = Mage::getSingleton('checkout/cart');
        $cart->init();

        /** @var Mage_Catalog_Model_Product $product */
        $product = $this->getProductCollection()->getFirstItem();
        $product->load($product->getId());

        $options = [];
        $palette = $this->getPaletteFromProduct($product);
        $priceRules = $palette->getPriceRules();
        if (empty($priceRules)) {
            throw new RuntimeException('No price rules found');
        }

        $priceRule = $priceRules[0];
        $priceRuleId = 0;
        foreach ($priceRules as $priceRuleId => $priceRule) {
            break;
        }

        $customOptions = Mage::getModel('catalog/product_option')->getProductOptionCollection($product);
        foreach ($customOptions as $customOption) {
            if ($customOption->getType() === 'ralcolor') {
                $options[$customOption->getOptionId()] = $priceRuleId;
                continue;
            }

            if ($customOption->getType() === 'drop_down') {
                $values = Mage::getSingleton('catalog/product_option_value')->getValuesCollection($customOption);
                foreach ($values as $value) {
                    $options[$customOption->getOptionId()] = $value->getOptionTypeId();
                }
            }
        }

        $parameter = array(
            'product' => $product->getId(),
            'qty' => '1',
            'options' => $options
        );

        /** @var Yireo_RalOption_Helper_PriceHandler $priceHandler */
        $priceHandler = Mage::helper('raloption/priceHandler');
        $priceHandler->setProduct($product)->setPalette($palette);
        $priceDifference = $priceHandler->getPriceByCode((string) $priceRuleId);

        $request = new Varien_Object();
        $request->setData($parameter);
        $cart->addProduct($product, $request);
        $cart->save();

        $items = $cart->getItems();
        foreach ($items as $item) {
            /** @var Mage_Sales_Model_Quote_Item $item */
            if ($item->getProductId() !== $product->getId()) {
                continue;
            }

            $item->calcRowTotal();
            $item->save();

            $expectedPrice = (float) $item->getOriginalPrice() + (float) $priceDifference;
            $this->assertEquals($expectedPrice, $item->getOriginalCustomPrice());
        }
    }

    /**
     * @param Mage_Catalog_Model_Product $product
     * @return Yireo_RalOption_Api_PaletteInterface
     */
    private function getPaletteFromProduct(Mage_Catalog_Model_Product $product)
    {
        $palette = $product->getData('raloption_palette');

        if (empty($palette)) {
            throw new RuntimeException('Palette is empty');
        }

        return new $palette;
    }

    /**
     * @return Mage_Catalog_Model_Resource_Product_Collection
     */
    private function getProductCollection()
    {
        return (new Yireo_RalOption_Test_Integration_DataProvider_ProductCollection)->getData();
    }
}
