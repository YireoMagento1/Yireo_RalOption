<?php
class Yireo_RalOption_Test_Integration_Helper_DataTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Yireo_RalOption_Helper_Data
     */
    private $helper;

    public function setUp()
    {
        $this->helper = Mage::helper('raloption');
    }

    public function testCurrency()
    {
        $this->assertContains('span', $this->helper->currency(100));
    }

    public function testGetOppositeColor()
    {
        $oppositeColor = $this->helper->getOppositeColor('#ffcc00');
        $this->assertNotEquals('#ffcc00', $oppositeColor);
    }

    public function testGetCodes()
    {
        $this->assertNotEmpty($this->helper->getCodes());
    }
}