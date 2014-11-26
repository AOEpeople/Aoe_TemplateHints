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
        
        $output .= $this->arrayToTabList($info, array('name', 'alias'));   
        
        if (count($path) > 0) { 
            $output .= "\t" . $helper->__('Block nesting').":\n";
            foreach ($path as $step) {
                $output .= "\t\t" . $helper->renderTitle($step) . "\n";
            }
        }
        
        return $output;
    }
    
    protected function arrayToTabList(array $array, array $skipKeys=array(), $nestingTabs = "\t") {
        $output = '';
        foreach ($array as $key => $value) {
            if (in_array($key, $skipKeys, true)) {
                continue;
            }
            if (is_array($value)) {
                $nestingTabs .= "\t";
                $value = $this->arrayToTabList($value, $skipKeys, $nestingTabs);
                if (strpos($value, "\t\t") < 2) {
                    $value = substr($value, 2);
                }
                else {
                    $value = substr($value, 1);
                }
                $nestingTabs = substr($nestingTabs, -1);
            }
            if (is_int($key)) {
                $output .= "$nestingTabs\t$value\n";
            } else {
                $output .= "$nestingTabs" . ucfirst($key) . ":\n";
                $output .= "$nestingTabs\t$value\n";
            }
    
        }
        return $output;
    }
}
