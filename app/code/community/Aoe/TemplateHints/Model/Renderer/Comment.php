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
            "\n".
            '<!-- [START: %1$s] %4$s'."\n".'%5$s -->'.
            "\n".
            '%3$s'.
            "\n".
            '<!-- [END: %1$s] %4$s -->'.
            "\n",
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
        
    /**
    * convert nested array to tab indented list
    *
    * @param array $array
    * @param array $skipKeys
    * @param int $indentationLevel
    * @return string
    */
    protected function arrayToTabList(array $array, array $skipKeys=array(), $indentationLevel = 1) {
        $output = '';
        foreach($array as $key => $value) {
            if (in_array($key, $skipKeys, true)) {
                continue;
            }
    
            $output .= $this->tabsForIndentation($indentationLevel);
    
            if (!is_array($value)) {
                if (!is_int($key)) {
                    $output .= ucfirst($key) . ":\n";
                    $output .= $this->tabsForIndentation($indentationLevel+1);
                }
                $output .= $value . "\n";
            }
            else {
                $output .= ucfirst($key) . ":\n";
                $output .= $this->arrayToTabList($value, $skipKeys, $indentationLevel+1);
            }
        }
    
        return $output;
    }
    
    
    /**
    * Outputs Tabs
    *
    * @param int $indentationLevel
    * @return string
    */
    protected function tabsForIndentation($indentationLevel) {
        $output = '';
        for($i = 0; $i < $indentationLevel; $i++) {
            $output .= "\t";
        }
    
        return $output;
    }
}
