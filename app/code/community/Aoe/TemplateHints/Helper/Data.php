<?php

/**
 * Data helper
 *
 * @author Fabrizio Branca
 */
class Aoe_TemplateHints_Helper_Data extends Mage_Core_Helper_Abstract {

    /**
     * Get skin file content
     *
     * @param $file
     * @return string
     */
    public function getSkinFileContent($file) {
        $package = Mage::getSingleton('core/design_package');
        $areaBackup = $package->getArea();
        $path = $package
            ->setArea('frontend')
            ->getFilename($file, array('_type' => 'skin'));
        $content = file_get_contents($path);
        $package->setArea($areaBackup);
        return $content;
    }

}
