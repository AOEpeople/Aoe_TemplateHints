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

    protected $classMethodCache = array();
    protected $remoteCallEnabled;
    protected $remoteCallUrlTemplate;



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

        if ($this->getRemoteCallEnabled()) {
            $fileAndLine = Mage::helper('aoe_templatehints/classInfo')->findFileAndLine($info['class']);
            if ($fileAndLine) {
                $url = sprintf($this->getRemoteCallUrlTemplate(), $fileAndLine['file'], $fileAndLine['line']);
                $info['class'] = sprintf($this->getRemoteCallLinkTemplate(),
                    $url,
                    $info['class']
                );
            }
        }

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

            if ($this->getRemoteCallEnabled()) {
                $url = sprintf($this->getRemoteCallUrlTemplate(), Mage::getBaseDir('design') . DS . $templateFile, 0);
                $info['template'] = sprintf($this->getRemoteCallLinkTemplate(),
                    $url,
                    $templateFile
                );
            }

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

        $info['methods'] = $this->getClassMethods(get_class($block));

        return $info;
    }



    /**
     * Check if remote call is enabled in configuration
     *
     * @return bool
     */
    public function getRemoteCallEnabled() {
        if (is_null($this->remoteCallEnabled)) {
            $this->remoteCallEnabled = Mage::getStoreConfigFlag('dev/aoe_templatehints/enablePhpstormRemoteCall');
        }
        return $this->remoteCallEnabled;
    }



    /**
     * Get remote call url template
     *
     * @return mixed
     */
    public function getRemoteCallUrlTemplate() {
        if (is_null($this->remoteCallUrlTemplate)) {
            $this->remoteCallUrlTemplate = Mage::getStoreConfig('dev/aoe_templatehints/remoteCallUrlTemplate');
        }
        return $this->remoteCallUrlTemplate;
    }



    /**
     * Get link template for remote calls
     *
     * @return string
     */
    public function getRemoteCallLinkTemplate() {
        return '<a href="%s" onclick="var ajax = new XMLHttpRequest(); ajax.open(\'GET\', this.href); ajax.send(null); return false">%s</a>';
    }



    /**
     * Get block methods (incl. methods of parent classes)
     *
     * @param string $className
     * @return array
     */
    public function getClassMethods($className) {

        if (!isset($this->classMethodCache[$className])) {

            $info = array();

            $rClass = new ReflectionClass($className);

            $currentClass = $rClass;
            $currentClassName = $currentClass->getName();
            $currentMethods = get_class_methods($currentClass->getName());
            $parentClass = $currentClass->getParentClass();

            $level = 1;
            while ($parentClass && $level < 6) {

                $parentClassName = $parentClass->getName();

                if (!in_array($currentClassName, array('Mage_Core_Block_Abstract', 'Mage_Core_Block_Template'))) {
                    $parentMethods = get_class_methods($parentClassName);
                    $tmp = array_diff($currentMethods, $parentMethods);
                    $info[$currentClassName] = array();

                    // render methods to "methodName($paramter1, $parameter2, ...)"
                    foreach ($tmp as $methodName) {

                        $parameters = array();
                        foreach ($currentClass->getMethod($methodName)->getParameters() as $parameter) { /* @var $parameter ReflectionParameter */
                            $parameters[] = '$'. $parameter->getName();
                        }

                        if (count($parameters) > 3) {
                            $parameters = array_slice($parameters, 0, 2);
                            $parameters[] = '...';
                        }

                        $info[$currentClassName][] = $methodName . '(' . implode(', ', $parameters) . ')';
                    }
                } else {
                    $info[$currentClassName] = array('(skipping)');
                    $parentMethods = array();
                }

                $level++;

                $currentClass = $parentClass;
                $currentClassName = $currentClass->getName();
                $currentMethods = $parentMethods;
                $parentClass = $currentClass->getParentClass();
            }

            $this->classMethodCache[$className] = $info;
        }

        return $this->classMethodCache[$className];
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
	$i = 0;
        while ($i++ < 20 && $step instanceof Mage_Core_Block_Abstract) {
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
	$i = 0;
        while ($i++ < 20 && $step instanceof Mage_Core_Block_Abstract) {
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
