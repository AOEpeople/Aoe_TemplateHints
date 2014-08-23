<?php

/**
 * Template hints
 *
 * @author Fabrizio Branca
 */
class Aoe_TemplateHints_Model_Renderer_Opentip extends Aoe_TemplateHints_Model_Renderer_Abstract {

    /**
     * @var array
     */
    protected $aStatistics = array(
        Aoe_TemplateHints_Helper_BlockInfo::TYPE_CACHED => 0,
        Aoe_TemplateHints_Helper_BlockInfo::TYPE_IMPLICITLYCACHED => 0,
        Aoe_TemplateHints_Helper_BlockInfo::TYPE_NOTCACHED => 0,
    );



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
     * Get CSS class for the hint
     *
     * @return string
     */
    protected function getHintClass()
    {
        return 'tpl-hint tpl-hint-border';
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

        $this->aStatistics[$blockInfo['cache-status']]++;

        $wrappedHtml = sprintf(
            '<div id="tpl-hint-%1$s" class="%2$s">
                %3$s
                <div id="tpl-hint-%1$s-title" style="display: none;">%4$s</div>
                <div id="tpl-hint-%1$s-infobox" style="display: none;">%5$s</div>
            </div>',
            $id,
            $this->getHintClass() . ' ' . $blockInfo['cache-status'],
            $blockContent,
            $helper->renderTitle($blockInfo),
            $this->renderBox($blockInfo, $path)
        );

        $showStatistics = false; // experimental (want to add some styling here...)
        if ($showStatistics && $blockInfo['name'] == 'core_profiler') {
            $wrappedHtml .= '<pre>'.print_r($this->aStatistics, true).'</pre>';
        }

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

        $output .= '<dl>';

        $output .= $this->arrayToDtDd($info, array('name', 'alias', 'cache-status'));

        if (count($path) > 0) {
            $output .= '<dt>'.$helper->__('Block nesting').':</dt><dd>';
                $output .= '<ul class="path">';
                foreach ($path as $step) {
                    $output .= '<li>'.$helper->renderTitle($step).'</li>';
                }
                $output .= '</ul>';
            $output .= '</dd>';
        }

        $output .= '</dl>';

        return $output;
    }


    /**
     * Render array as <dl>
     *
     * @param array $array
     * @param array $skipKeys
     * @return string
     */
    protected function arrayToDtDd(array $array, array $skipKeys=array()) {
        $output = '<dl>';
        foreach ($array as $key => $value) {

            if (in_array($key, $skipKeys)) {
                continue;
            }

            if (is_array($value)) {
                $value = $this->arrayToDtDd($value);
            }
            if (is_int($key)) {
                $output .= $value . '<br />';
            } else {
                $output .= '<dt>'.ucfirst($key).':</dt><dd>';
                $output .= $value;
                $output .= '</dd>';
            }
        }
        $output .= '</dl>';
        return $output;
    }


}
