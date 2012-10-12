<?php

/**
 * Template hints
 *
 * @author Fabrizio Branca
 */
class Aoe_TemplateHints_Model_Observer {

	CONST TYPE_CACHED = 'cached';
	CONST TYPE_NOTCACHED = 'notcached';
	CONST TYPE_IMPLICITLYCACHED = 'implicitlycached';

	/**
	 * @var bool
	 */
	protected $showHints;

	/**
	 * @var bool
	 */
	protected $codeWritten = false;

	/**
	 * @var int
	 */
	protected $hintId = 0;



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
	 * @author Fabrizio Branca
	 * @since 2011-01-24
	 */
	public function core_block_abstract_to_html_after(Varien_Event_Observer $params) {

		if (!$this->showHints()) {
			return;
		}

		$block = $params->getBlock(); /* @var $block Mage_Core_Block_Abstract */

		$transport = $params->getTransport();

		$path = $this->getBlockPath($block);
		$blockInfo = $this->getBlockInfo($block);

		$this->hintId++;

		$title = $this->renderTitle($blockInfo);
		$content = $this->renderBox($blockInfo, $path);

		$wrappedHtml = '';
		$wrappedHtml .= "<!-- [START] $title\n$content -->";
		$wrappedHtml .= $transport->getHtml();
		$wrappedHtml .= '<!-- [END] '.$title.' -->';

		$transport->setHtml($wrappedHtml);
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

		foreach ($info as $label => $value) {

			if (in_array($label, array('name', 'alias', 'cache-status'))) {
				continue;
			}
			$output .= '  '.ucfirst($label).":\n";
			$output .= '    '.$value.":\n";
		}

		$output .= "  Block nesting:\n";
		foreach ($path as $step) {
			$output .= '    '.$this->renderTitle($step)."\n";
		}

		return $output;
	}



	/**
	 * Check if a block is within another one that is cached
	 *
	 * @param Mage_Core_Block_Abstract $block
	 * @return bool
	 * @author Fabrizio Branca
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
	 * Get block information
	 *
	 * @param Mage_Core_Block_Abstract $block
	 * @param bool $fullInfo
	 * @return array
	 */
	protected function getBlockInfo(Mage_Core_Block_Abstract $block, $fullInfo=true) {
		$info = array(
			'name' => $block->getNameInLayout(),
			'alias' => $block->getBlockAlias(),
		);

		if (!$fullInfo) {
			return $info;
		}

		$info['class'] = get_class($block);
		$info['module'] = $block->getModuleName();

		if ($block instanceof Mage_Cms_Block_Block) {
			$info['cms-blockId'] = $block->getBlockId();
		}
		if ($block instanceof Mage_Cms_Block_Page) {
			$info['cms-pageId'] = $block->getPage()->getIdentifier();
		}
		$templateFile = $block->getTemplateFile();
		if ($templateFile) {
			$info['template'] = $templateFile;
		}

		// cache information
		$info['cache-status'] = self::TYPE_NOTCACHED;

		$cacheLifeTime = $block->getCacheLifetime();
		if (!is_null($cacheLifeTime)) {

			$info['cache-lifetime'] = (intval($cacheLifeTime) == 0) ? 'forever' : intval($cacheLifeTime) . ' sec';
			$info['cache-key'] = $block->getCacheKey();
			$info['tags'] = implode(',', $block->getCacheTags());

			$info['cache-status'] = self::TYPE_CACHED;
		} elseif ($this->isWithinCachedBlock($block)) {
			$info['cache-status'] = self::TYPE_IMPLICITLYCACHED; // not cached, but within cached
		}

		return $info;
	}



	/**
	 * Get path information of a block
	 *
	 * @param Mage_Core_Block_Abstract $block
	 * @return string
	 * @author Fabrizio Branca
	 * @since 2011-01-24
	 */
	protected function getBlockPath(Mage_Core_Block_Abstract $block) {
		$blockPath = array();
		$step = $block->getParentBlock();
		while ($step instanceof Mage_Core_Block_Abstract) {
			$blockPath[] = $this->getBlockInfo($step, false);
			$step = $step->getParentBlock();
		}
		return $blockPath;
	}


}
