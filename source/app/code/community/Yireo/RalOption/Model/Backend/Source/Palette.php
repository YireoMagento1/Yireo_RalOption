<?php
/**
 * RalOption plugin for Magento 
 *
 * @category    design_default
 * @package     Yireo_RalOption
 * @author      Yireo (https://www.yireo.com/)
 * @copyright   Copyright 2015 Yireo (https://www.yireo.com/)
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
        return self::_toOptionArray();
    }

    /**
     * Options getter (static)
     *
     * @return array
     */
    static public function _toOptionArray()
    {
        $paths = array(
            BP.DS.'app'.DS.'code'.DS.'local'.DS.'Yireo'.DS.'RalOption'.DS.'Helper'.DS,
            BP.DS.'app'.DS.'code'.DS.'community'.DS.'Yireo'.DS.'RalOption'.DS.'Helper'.DS,
        );

        $helpers = array();
        foreach($paths as $path) {
            if(is_dir($path)) {
                if($handle = opendir($path)) {
                    while(($file = readdir($handle)) !== false) {
                        if(is_file($path.$file) && preg_match('/^Palette([0-9]+)\.php/', $file, $match)) {
                            $value = strtolower(preg_replace('/\.php$/', '', $file));
                            $label = Mage::helper('raloption')->__('Palette').' '.$match[1];
                            $helpers[] = array('value' => $value, 'label'=> $label);
                        }
                    }
                    closedir($handle);
                }
            }
        }

        return $helpers;
    }

    /**
     * Set attribute instance
     *
     * @param Mage_Catalog_Model_Resource_Eav_Attribute $attribute
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
     * @return array
     */
    public function getAllOptions()
    {
        $options = self::_toOptionArray();
        array_unshift($options, array('value'=>'', 'label'=>''));
        return $options;
    }
}
