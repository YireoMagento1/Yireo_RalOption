<?php
/**
 * RalOption plugin for Magento
 *
 * @package     Yireo_RalOption
 * @author      Yireo (https://www.yireo.com/)
 * @copyright   Copyright 2016 Yireo (https://www.yireo.com/)
 * @license     Open Source License (OSL v3)
 */

/**
 * Class Yireo_RalOption_Helper_Palette
 */
class Yireo_RalOption_Helper_Palette extends Mage_Core_Helper_Abstract
{
    /**
     * @return array
     */
    public function getAutoDetectedPalettes()
    {
        $paths = $this->getSearchablePaths();
        $palettes = [];

        foreach ($paths as $path) {
            $files = $this->getPhpFilesInPath($path);

            foreach ($files as $file) {
                $palette = $this->getObjectFromFile($file);

                if (!$palette instanceof Yireo_RalOption_Api_PaletteInterface) {
                    continue;
                }

                $palettes[] = $palette;
            }
        }

        return $palettes;
    }

    /**
     * @param $file string
     *
     * @return object
     */
    protected function getObjectFromFile($file)
    {
        $className = $this->getClassNameFromFile($file);
        return new $className;
    }

    /**
     * @param $file string
     *
     * @return string
     */
    protected function getClassNameFromFile($file)
    {
        $baseName = preg_replace('/\.php$/', '', basename($file));
        $baseFolder = basename(dirname($file));
        $className = 'Yireo_RalOption_'.$baseFolder.'_'.$baseName;

        return $className;
    }

    /**
     * @return array
     * @throws Yireo_RalOption_Exception_Palette
     */
    public function getPalettesFromEvent()
    {
        $palettes = [];

        $paletteStrings = [];
        Mage::dispatchEvent('raloption_get_palettes', ['palettes', $paletteStrings]);

        foreach ($paletteStrings as $paletteString) {
            if (!is_string($paletteString)) {
                throw new Yireo_RalOption_Exception_Palette(sprintf('Palette added through event %s is not a string', 'raloption_get_palettes'));
            }

            try {
                $palette = new $paletteString;
            } catch(Exception $e) {
                throw new Yireo_RalOption_Exception_Palette(sprintf('Unable to create palette class from string %s', $paletteString));
            }


            if (!$palette instanceof Yireo_RalOption_Api_PaletteInterface) {
                throw new Yireo_RalOption_Exception_Palette(sprintf('Class %s does not implement Yireo_RalOption_Api_PaletteInterface', $paletteString));
            }

            $palettes[] = $palette;
        }

        return $palettes;
    }

    /**
     * @param $path
     *
     * @return array
     */
    protected function getPhpFilesInPath($path)
    {
        $files = [];

        if (!is_dir($path)) {
            return $files;
        }

        if (!$handle = opendir($path)) {
            return $files;
        }

        while (($file = readdir($handle)) !== false) {
            if ($this->isValidPhpFile($path . '/' . $file)) {
                $files[] = $path . '/' . $file;
            }
        }

        closedir($handle);

        return $files;
    }

    /**
     * @param $paletteFile
     *
     * @return bool
     */
    protected function isValidPhpFile($paletteFile)
    {
        if (!is_file($paletteFile)) {
            return false;
        }

        if (preg_match('/(.*)\.php/', $paletteFile) === false) {
            return false;
        }

        return true;
    }

    /**
     * @return array
     */
    protected function getSearchablePaths()
    {
        $paths = array(
            BP . DS . 'app' . DS . 'code' . DS . 'local' . DS . 'Yireo' . DS . 'RalOption' . DS . 'Palette',
            BP . DS . 'app' . DS . 'code' . DS . 'community' . DS . 'Yireo' . DS . 'RalOption' . DS . 'Palette',
            BP . DS . 'app' . DS . 'code' . DS . 'local' . DS . 'Yireo' . DS . 'RalOption' . DS . 'Helper',
            BP . DS . 'app' . DS . 'code' . DS . 'community' . DS . 'Yireo' . DS . 'RalOption' . DS . 'Helper',
        );

        return $paths;
    }
}
