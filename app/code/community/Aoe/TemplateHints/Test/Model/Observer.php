<?php

class Aoe_TemplateHints_Test_Model_Observer extends EcomDev_PHPUnit_Test_Case
{

    /**
     * @var Aoe_TemplateHints_Model_Observer
     */
    protected $observer;

    public function setUp() {
        $this->observer = Mage::getModel('aoe_templatehints/observer');
    }

    /**
     * @test
     */
    public function checkClass()
    {
        $this->assertInstanceOf('Aoe_TemplateHints_Model_Observer', $this->observer);
    }

    /**
     * @test
     * @dataProvider showHintsParameters
     */
    public function showHints($devMode, $cookieValue, $paramValue, $expectedShowHints)
    {
        $coreHelperMock = $this->getHelperMock('core', array('isDevAllowed'));
        $coreHelperMock->expects($this->any())
            ->method('isDevAllowed')
            ->will($this->returnValue($devMode));
        $this->replaceByMock('helper', 'core', $coreHelperMock);

        $cookie = $this->getModelMock('core/cookie', array('get'));
        $cookie->expects($this->any())
            ->method('get')
            ->will($this->returnValue($cookieValue));
        $this->replaceByMock('singleton', 'core/cookie', $cookie);

        $requestMock = $this->getMock('Mage_Core_Controller_Request_Http', array('get'));
        $requestMock->expects($this->any())
            ->method('get')
            ->will($this->returnValue($paramValue));
        $app = Mage::getSingleton('core/app')->setRequest($requestMock);

        $this->assertEquals($expectedShowHints, $this->observer->showHints());
    }

    public function showHintsParameters() {
        return array(
            array(false, false, false, false),
            array(false, false, true, false),
            array(false, true, false, false),
            array(false, true, true, false),
            array(true, false, false, false),
            array(true, false, true, true),
            array(true, true, false, true),
            array(true, true, true, true),
        );
    }

}

