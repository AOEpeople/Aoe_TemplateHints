<?php

class Aoe_TemplateHints_Helper_Data extends Mage_Core_Helper_Abstract {

	/**
	 * Get skin file content
	 *
	 * @param $file
	 * @return string
	 */
	public function getSkinFileContent($file) {
		$path = Mage::getSingleton('core/design_package')
			->setArea('frontend')
			->getFilename($file, array('_type' => 'skin'));
		$content = file_get_contents($path);
		return $content;
	}

}
