<?php

/**
 * Abstract renderer base class
 *
 * @author Fabrizio Branca
 */
abstract class Aoe_TemplateHints_Model_Renderer_Abstract {



    /**
     * This method will be called only once
     * (before the first template hint is rendered)
     *
     * @param string $content
     * @return string
     */
    public function init($content) {
        return $content;
    }



    /**
     * Render template hint
     *
     * @param Mage_Core_Block_Abstract $block
     * @param string $blockContent
     * @param int $id
     * @return string
     */
    public function render(Mage_Core_Block_Abstract $block, $blockContent, $id) {
        return $blockContent;
    }



}
