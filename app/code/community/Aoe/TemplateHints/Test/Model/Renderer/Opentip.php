<?php

/**
 * Testing the opentip renderer
 */
class Aoe_TemplateHints_Test_Model_Renderer_Opentip extends EcomDev_PHPUnit_Test_Case_Config {

    protected function setConfigValue($path, $value) {
        $t = new EcomDev_PHPUnit_Model_Fixture_Processor_Config();
        $t->apply(array($path => $value), '', new EcomDev_PHPUnit_Model_Fixture);
    }

    protected function enableHints() {
        $coreHelperMock = $this->getHelperMock('core', array('isDevAllowed'));
        $coreHelperMock->expects($this->any())
            ->method('isDevAllowed')
            ->will($this->returnValue(true));
        $this->replaceByMock('helper', 'core', $coreHelperMock);

        $cookie = $this->getModelMock('core/cookie', array('get'));
        $cookie->expects($this->any())
            ->method('get')
            ->will($this->returnValue(true));
        $this->replaceByMock('singleton', 'core/cookie', $cookie);
    }

    public function setUp() {
        $this->setConfigValue('default/dev/aoe_templatehints/templateHintRenderer', 'aoe_templatehints/renderer_opentip');
        $this->enableHints();
    }

    /**
     * @test
     * @dataProvider blocksProvider
     */
    public function coreTextBlock(Mage_Core_Block_Abstract $block, array $regex, array $notRegex) {
        $this->assertEventObserverDefined('global', 'core_block_abstract_to_html_after', 'Aoe_TemplateHints_Model_Observer', 'core_block_abstract_to_html_after');

        $observer = Mage::getModel('aoe_templatehints/observer'); /* @var $observer Aoe_TemplateHints_Model_Observer */
        $rendererFromObserver = $observer->getRenderer();
        $this->assertInstanceOf('Aoe_TemplateHints_Model_Renderer_Abstract', $rendererFromObserver);
        $this->assertInstanceOf('Aoe_TemplateHints_Model_Renderer_Opentip', $rendererFromObserver);

        $html = $block->toHtml();
        $this->assertEventDispatchedExactly('core_block_abstract_to_html_before', 1);
        $this->assertEventDispatchedExactly('core_block_abstract_to_html_after', 1);

        $this->assertContains('id="tpl-hint-', $html);
        $this->assertContains('class="tpl-hint tpl-hint-border', $html);

        $this->assertRegExp('/<div id="tpl-hint-\d*-title"/', $html);
        $this->assertRegExp('/<div id="tpl-hint-\d*-infobox"/', $html);

        $this->assertContains('<dt>Class:</dt><dd>'.get_class($block).'</dd>', $html);

        foreach ($regex as $c) {
            $this->assertRegExp($c, $html);
        }

        Mage::app()->disableEvents();
        $rawBlockHtml = $block->toHtml();
        Mage::app()->enableEvents();
        $this->assertContains($rawBlockHtml, $html);
    }

    public function blocksProvider() {
        $provider = array();

        $block = Mage::app()->getLayout()->createBlock('core/text'); /* @var $block Mage_Core_Block_Text */
        $block->setText('HELLO WORLD');

        $contains = array(
            '/<!-- INIT AOE_TEMPLATEHINTS RENDERER START -->/',
            '/<!-- INIT AOE_TEMPLATEHINTS RENDERER STOP -->/'
        );

        // first block contains init
        $provider[] = array($block, $contains, array());
        // second doesn't
        $provider[] = array($block, array(), $contains);

        // check block name
        $block = Mage::app()->getLayout()->createBlock('core/text', 'blockname'); /* @var $block Mage_Core_Block_Text */
        $block->setText('HELLO WORLD');
        $contains = array(
            '/<div id="tpl-hint-\d*-title".*>blockname<\/div>/',
        );
        $provider[] = array($block, $contains, array());

        // check cache status for cached block
        $block = Mage::app()->getLayout()->createBlock('core/text', 'blockname'); /* @var $block Mage_Core_Block_Text */
        $block->setText('HELLO WORLD');
        $block->setCacheLifetime(100);
        $contains = array(
            '/<div id="tpl-hint-\d*" class="tpl-hint tpl-hint-border cached">/',
        );
        $provider[] = array($block, $contains, array());

        // check cache status for uncached block
        $block = Mage::app()->getLayout()->createBlock('core/text', 'blockname'); /* @var $block Mage_Core_Block_Text */
        $block->setText('HELLO WORLD');
        $block->setCacheLifetime(null);
        $contains = array(
            '/<div id="tpl-hint-\d*" class="tpl-hint tpl-hint-border notcached">/',
        );
        $provider[] = array($block, $contains, array());

        return $provider;
    }

}

