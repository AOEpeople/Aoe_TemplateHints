<?php

/**
 * Template hints
 *
 * @author Alexander Menk
 */
class Aoe_TemplateHints_Model_Renderer_TipOnly extends Aoe_TemplateHints_Model_Renderer_Opentip {


    /**
     * Just remove the border
     *
     * @return string
     */
    protected function getHintClass()
    {
        return 'tpl-hint';
    }
}