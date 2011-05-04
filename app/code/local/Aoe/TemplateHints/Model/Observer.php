<?php

/**
 * Template hints
 *
 * @author Fabrizio Branca <fabrizio.branca@aoemedia.de>
 */
class Aoe_TemplateHints_Model_Observer {

	protected $showHints;



	/**
	 * Check if hints should be displayed
	 *
	 * @return bool
	 */
	public function showHints() {
		if (is_null($this->showHints)) {
			$this->showHints = false;
			if (Mage::helper('core')->isDevAllowed()) {
				if (Mage::getModel('core/cookie')->get('ath') || Mage::getSingleton('core/app')->getRequest()->get('ath')) {
					$this->showHints = true;
				}
			}
		}
		return $this->showHints;
	}



	/**
	 * Event core_block_abstract_to_html_after
	 *
	 * @param Varien_Event_Observer $params
	 * @return void
	 * @author Fabrizio Branca <fabrizio.branca@aoemedia.de>
	 * @since 2011-01-24
	 */
	public function core_block_abstract_to_html_after(Varien_Event_Observer $params) {

		if (!$this->showHints()) {
			return;
		}

		$block = $params->getBlock(); /* @var $block Mage_Core_Block_Abstract */
		$transport = $params->getTransport();

		$info = array();

		$moduleName = $block->getModuleName();

		$info['MODULE'] = $moduleName;
		$info['PATH'] = $this->getBlockPath($block);

		if ($block instanceof Mage_Cms_Block_Block) {
			$info['CMS-BLOCK-ID'] = $block->getBlockId();
		}

		if ($block instanceof Mage_Cms_Block_Page) {
			$info['CMS-PAGE-ID'] = $block->getPage()->getIdentifier();
		}

		$templateFile = $block->getTemplateFile();
		if ($templateFile) {
			$info['TEMPLATE'] = $templateFile;
		}

		$color = Mage::getStoreConfig('dev/debug/border_color_notcached'); // not cached
		$cacheInfo = $this->getCacheInfo($block);
		if ($cacheInfo) {
			$info['CACHE'] = $cacheInfo;
			$color = Mage::getStoreConfig('dev/debug/border_color_cached'); // cached
		} elseif ($this->isWithinCachedBlock($block)) {
			$color = Mage::getStoreConfig('dev/debug/border_color_cached_inherit'); // not cached, but within cached
		}

		$title = array();
		foreach ($info as $key => $value) {
			$title[] = "$key: $value";
		}
		$title = implode(' // ', $title);

		// wrap info around block output
		$html = $transport->getHtml();
		$html = '<div class="tpl-hint" title="'.$title.'" style="border: 1px dotted '.$color.'; margin:2px; padding:2px; overflow: hidden;">' . $html . '</div>';
		$transport->setHtml($html);
	}



	/**
	 * Check if a block is within another one that is cached
	 *
	 * @param Mage_Core_Block_Abstract $block
	 * @return bool
	 * @author Fabrizio Branca <fabrizio.branca@aoemedia.de>
	 * @since 2011-01-24
	 */
	protected function isWithinCachedBlock(Mage_Core_Block_Abstract $block) {
		$step = $block;
		while ($step instanceof Mage_Core_Block_Abstract) {
			if (!is_null($step->getCacheLifetime())) {
				return true;
			}
			$step = $step->getParentBlock();
		}
		return false;
	}



	/**
	 * Get path information of a block
	 *
	 * @param Mage_Core_Block_Abstract $block
	 * @return string
	 * @author Fabrizio Branca <fabrizio.branca@aoemedia.de>
	 * @since 2011-01-24
	 */
	protected function getBlockPath(Mage_Core_Block_Abstract $block) {
		$blockPath = '';
		$step = $block;
		$aliasName = '';
		while ($step instanceof Mage_Core_Block_Abstract) {
			$aliasNamePart = $step->getNameInLayout();
			$alias = $step->getBlockAlias();
			if ($aliasNamePart != $alias) {
				$aliasNamePart = 'name: '.$aliasNamePart;
				if ($alias) {
					$aliasNamePart .= ' /alias: '. $alias ;
				}
			} else {
				$aliasNamePart = 'alias/name: '.$aliasNamePart;
			}
			$blockPath .= (!empty($blockPath) ? ' <- ' : '') . get_class($step) . ' ('.$aliasNamePart.') ';
			$step = $step->getParentBlock();
		}
		return $blockPath;
	}



	/**
	 * Get cache information of a block
	 *
	 * @param Mage_Core_Block_Abstract $block
	 * @return string
	 * @author Fabrizio Branca <fabrizio.branca@aoemedia.de>
	 * @since 2011-01-24
	 */
	protected function getCacheInfo(Mage_Core_Block_Abstract $block) {
		$cacheLifeTime = $block->getCacheLifetime();
		$cacheInfo = '';
		if (!is_null($cacheLifeTime)) {
			$cacheLifeTime = (intval($cacheLifeTime) == 0) ? 'forever' : intval($cacheLifeTime) . ' sec';
			$cacheInfo = 'Lifetime: ' . $cacheLifeTime .', ';
			$cacheInfo .= 'Key:' . $block->getCacheKey() . ', ';
			$cacheInfo .= 'Tags: ' . implode(',', $block->getCacheTags()) . '';
		}
		return $cacheInfo;
	}

}
