<?php

/**
 * Template hints
 *
 * @author Fabrizio Branca
 */
class Aoe_TemplateHints_Model_Observer {

    /**
     * @var bool
     */
    protected $showHints;

    /**
     * @var bool
     */
    protected $init = true;

    /**
     * @var bool
     */
    protected $afterHead = false;

    /**
     * @var int
     */
    protected $hintId = 0;

    /**
     * @var Aoe_TemplateHints_Model_Renderer_Abstract
     */
    protected $renderer;



    /**
     * Check if hints should be displayed
     *
     * @return bool
     */
    public function showHints() {
        if (is_null($this->showHints)) {
            $this->showHints = false;
            if (Mage::helper('core')->isDevAllowed()) {
                if (Mage::getSingleton('core/cookie')->get('ath') || Mage::app()->getRequest()->get('ath')) {
                    $this->showHints = true;
                }
            }
        }
        return $this->showHints;
    }



    /**
     * Get renderer
     *
     * @return Aoe_TemplateHints_Model_Renderer_Abstract
     */
    public function getRenderer() {
        if (is_null($this->renderer)) {
            $rendererClass = Mage::getStoreConfig('dev/aoe_templatehints/templateHintRenderer');
            if (empty($rendererClass)) {
                Mage::throwException('No renderer configured');
            }
            $this->renderer = Mage::getSingleton($rendererClass);
            if (!is_object($this->renderer) || !$this->renderer instanceof Aoe_TemplateHints_Model_Renderer_Abstract) {
                Mage::throwException('Render must be an instanceof Aoe_TemplateHints_Model_Renderer_Abstract');
            }
        }
        return $this->renderer;
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

        if (substr(trim($params->getTransport()->getHtml()), 0, 4) == 'http') {
            return;
        }

        $wrappedHtml = '';

        $block = $params->getBlock(); /* @var $block Mage_Core_Block_Abstract */

        // will only be called once and allows renderes to initialize themselves (e.g. adding js/css)
        if ($this->init && $this->afterHead && !Mage::getStoreConfigFlag('advanced/modules_disable_output/' . $block->getModuleName()) && $block->getNameInLayout()) {
            $wrappedHtml = '<!-- INIT AOE_TEMPLATEHINTS RENDERER START -->' . $this->getRenderer()->init($wrappedHtml) . '<!-- INIT AOE_TEMPLATEHINTS RENDERER STOP -->';
            $this->init = false;
        }

        if ($block->getNameInLayout() == 'head') {
            $this->afterHead = true;
        }

        $transport = $params->getTransport();

        $this->hintId++;

        $wrappedHtml .= $this->getRenderer()->render($block, $transport->getHtml(), $this->hintId);

        $transport->setHtml($wrappedHtml);
    }


}
