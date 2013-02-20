<?php

/**
 * Template hints
 *
 * @author Fabrizio Branca
 */
class Aoe_TemplateHints_Model_Renderer_Opentip extends Aoe_TemplateHints_Model_Renderer_Abstract {

	/**
	 * Init
	 *
	 * @param $wrappedHtml
	 * @return string|void
	 */
	public function init($wrappedHtml) {
		$helper = Mage::helper('aoe_templatehints'); /* @var $helper Aoe_TemplateHints_Helper_Data */

		$wrappedHtml .= '<script type="text/javascript">' . $helper->getSkinFileContent('aoe_templatehints/js/opentip.min.js') . '</script>';
		$wrappedHtml .= '<script type="text/javascript">' . $helper->getSkinFileContent('aoe_templatehints/js/excanvas.js') . '</script>';
		$wrappedHtml .= '<script type="text/javascript">' . $helper->getSkinFileContent('aoe_templatehints/js/aoe_templatehints.js') . '</script>';
		$wrappedHtml .= '<style type="text/css">' . $helper->getSkinFileContent('aoe_templatehints/css/aoe_templatehints.css') . '</style>';
		$wrappedHtml .= '<style type="text/css">' . $helper->getSkinFileContent('aoe_templatehints/css/opentip.css') . '</style>';

		return $wrappedHtml;
	}



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
			'<div id="tpl-hint-%1$s" class="tpl-hint %2$s">
				%3$s
				<div id="tpl-hint-%1$s-title" style="display: none;">%4$s</div>
				<div id="tpl-hint-%1$s-infobox" style="display: none;">%5$s</div>
			</div>',
			$id,
			$blockInfo['cache-status'],
			$blockContent,
			$this->renderTitle($blockInfo),
			$this->renderBox($blockInfo, $path)
		);

		return $wrappedHtml;
	}



	/**
	 * Render title
	 *
	 * @param array $info
	 * @return string
	 */
	protected function renderTitle(array $info) {
		$title = $info['name'];
		if ($info['name'] != $info['alias'] && $info['alias']) {
			$title .= ' (alias: ' . $info['alias'] . ')';
		}
		return $title;
	}



	/**
	 * Render box
	 *
	 * @param array $info
	 * @param array $path
	 * @return string
	 */
	protected function renderBox(array $info, array $path) {
		$output = '';

		$output .= '<dl>';

		foreach ($info as $label => $value) {

			if (in_array($label, array('name', 'alias', 'cache-status'))) {
				continue;
			}

			$output .= '<dt>'.ucfirst($label).':</dt><dd>';
			$output .= $value;
			$output .= '</dd>';
		}

		$output .= '<dt>'.Mage::helper('aoe_templatehints')->__('Block nesting').':</dt><dd>';
			$output .= '<ul class="path">';
			foreach ($path as $step) {
				$output .= '<li>'.$this->renderTitle($step).'</li>';
			}
			$output .= '</ul>';
		$output .= '</dd>';

		$output .= '</dl>';

		return $output;
	}


}
