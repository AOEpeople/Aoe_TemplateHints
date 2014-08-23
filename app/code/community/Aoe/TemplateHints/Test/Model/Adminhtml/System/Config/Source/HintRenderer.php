<?php
/**
 * Hint renderer
 *
 * @author Fabrizio Branca
 * @since 2013-2/19/13
 */
class Aoe_TemplateHints_Test_Model_Adminhtml_System_Config_Source_HintRenderer extends EcomDev_PHPUnit_Test_Case {

    /**
     * @test
     */
    public function toOptionArray() {
        $sourceModel = new Aoe_TemplateHints_Model_Adminhtml_System_Config_Source_HintRenderer();

        $options = $sourceModel->toOptionArray();
        $this->assertEventDispatchedExactly('aoetemplatehints_hintrenderer_options', 1);

        $this->assertCount(3, $options);

        foreach ($options as $option) {
            $renderer = Mage::getModel($option['value']);
            $this->assertInstanceOf('Aoe_TemplateHints_Model_Renderer_Abstract', $renderer);

            $this->setConfigValue('default/dev/aoe_templatehints/templateHintRenderer', $option['value']);

            $observer = Mage::getModel('aoe_templatehints/observer'); /* @var $observer Aoe_TemplateHints_Model_Observer */
            $rendererFromObserver = $observer->getRenderer();
            $this->assertInstanceOf('Aoe_TemplateHints_Model_Renderer_Abstract', $rendererFromObserver);
            $this->assertInstanceOf(get_class($renderer), $rendererFromObserver);
        }
    }

    protected function setConfigValue($path, $value) {
        $t = new EcomDev_PHPUnit_Model_Fixture_Processor_Config();
        $t->apply(array($path => $value), '', new EcomDev_PHPUnit_Model_Fixture);
    }


}
