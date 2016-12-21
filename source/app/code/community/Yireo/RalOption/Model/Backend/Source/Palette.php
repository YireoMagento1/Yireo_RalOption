<?php

/**
 * RalOption plugin for Magento
 *
 * @category    design_default
 * @package     Yireo_RalOption
 * @author      Yireo (https://www.yireo.com/)
 * @copyright   Copyright 2016 Yireo (https://www.yireo.com/)
 * @license     Open Source License (OSL v3)
 */
class Yireo_RalOption_Model_Backend_Source_Palette extends Mage_Eav_Model_Entity_Attribute_Source_Table
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $helper = $this->getHelper();
        $palettes = $helper->getAutoDetectedPalettes();
        $palettes = array_merge($palettes, $helper->getPalettesFromEvent());

        $options = [];

        foreach ($palettes as $palette) {
            $label = get_class($palette);
            $value = get_class($palette);
            $options[] = array('value' => $value, 'label' => $label);
        }

        return $options;
    }

    /**
     * @return Yireo_RalOption_Helper_Palette
     */
    public function getHelper()
    {
        return Mage::helper('raloption/palette');
    }

    /**
     * Set attribute instance
     *
     * @param Mage_Catalog_Model_Resource_Eav_Attribute $attribute
     *
     * @return Mage_Eav_Model_Entity_Attribute_Frontend_Abstract
     */
    public function setAttribute($attribute)
    {
        $this->_attribute = $attribute;
        return $this;
    }

    /**
     * Retrieve option array with empty value
     *
     * @param $withEmpty bool
     * @param $defaultValues bool
     *
     * @return array
     */
    public function getAllOptions($withEmpty = true, $defaultValues = false)
    {
        $options = $this->toOptionArray();

        if ($withEmpty) {
            array_unshift($options, array('value' => '', 'label' => ''));
        }

        return $options;
    }
}
