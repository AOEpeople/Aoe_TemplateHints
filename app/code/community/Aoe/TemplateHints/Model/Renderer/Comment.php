<?php

/**
 * Template hints
 *
 * @author Fabrizio Branca
 */
class Aoe_TemplateHints_Model_Renderer_Comment extends Aoe_TemplateHints_Model_Renderer_Abstract {



	/**
	 * Render template hint
	 *
	 * @param Mage_Core_Block_Abstract $block
	 * @param $blockContent
	 * @param $id
	 * @return string|void
	 */
	public function render(Mage_Core_Block_Abstract $block, $blockContent, $id) {

		$helper = Mage::helper('aoe_templatehints/blockInfo'); /* @var $helper Aoe_TemplateHints_Helper_BlockInfo */

		$path = $helper->getBlockPath($block);
		$blockInfo = $helper->getBlockInfo($block);

		$wrappedHtml = sprintf(
			'<!-- [START: %1$s] %4$s'."\n".'%5$s -->'.
			'%3$s'.
			'<!-- [END: %1$s] %4$s -->',
			$id,
			$blockInfo['cache-status'],
			$blockContent,
			$helper->renderTitle($blockInfo),
			$this->renderBox($blockInfo, $path)
		);

		return $wrappedHtml;
	}



	/**
	 * Render box
	 *
	 * @param array $info
	 * @param array $path
	 * @return string
	 */
	protected function renderBox(array $info, array $path) {

		$helper = Mage::helper('aoe_templatehints/blockInfo'); /* @var $helper Aoe_TemplateHints_Helper_BlockInfo */

		$output = '';

		foreach ($info as $label => $value) {

			if (in_array($label, array('name', 'alias'))) {
				continue;
			}
			$output .= "\t" . ucfirst($label) . ":\n";
			$output .= "\t\t$value\n";
		}

		$output .= "\t" . $helper->__('Block nesting').":\n";
		foreach ($path as $step) {
			$output .= "\t\t" . $helper->renderTitle($step) . "\n";
		}

		return $output;
	}


}
