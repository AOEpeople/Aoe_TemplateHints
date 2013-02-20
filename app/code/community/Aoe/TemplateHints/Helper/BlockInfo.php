<?php

/**
 * Block info data helper
 *
 * @author Fabrizio Branca
 */
class Aoe_TemplateHints_Helper_BlockInfo extends Mage_Core_Helper_Abstract {

	CONST TYPE_CACHED = 'cached';
	CONST TYPE_NOTCACHED = 'notcached';
	CONST TYPE_IMPLICITLYCACHED = 'implicitlycached';



	/**
	 * Get block information
	 *
	 * @param Mage_Core_Block_Abstract $block
	 * @param bool $fullInfo
	 * @return array
	 */
	public function getBlockInfo(Mage_Core_Block_Abstract $block, $fullInfo=true) {
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
			$info['cache-key-info'] = is_array($block->getCacheKeyInfo())
						? implode(', ', $block->getCacheKeyInfo())
						: $block->getCacheKeyInfo()
					;
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
	public function getBlockPath(Mage_Core_Block_Abstract $block) {
		$blockPath = array();
		$step = $block->getParentBlock();
		while ($step instanceof Mage_Core_Block_Abstract) {
			$blockPath[] = $this->getBlockInfo($step, false);
			$step = $step->getParentBlock();
		}
		return $blockPath;
	}



	/**
	 * Check if a block is within another one that is cached
	 *
	 * @param Mage_Core_Block_Abstract $block
	 * @return bool
	 * @author Fabrizio Branca
	 * @since 2011-01-24
	 */
	public function isWithinCachedBlock(Mage_Core_Block_Abstract $block) {
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
	 * Render title
	 *
	 * @param array $info
	 * @return string
	 */
	public function renderTitle(array $info) {
		$title = $info['name'];
		if ($info['name'] != $info['alias'] && $info['alias']) {
			$title .= ' (alias: ' . $info['alias'] . ')';
		}
		return $title;
	}


}
