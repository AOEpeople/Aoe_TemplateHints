<?php
/**
 * Hint renderer
 *
 * @author Fabrizio Branca
 * @since 2013-2/19/13
 */
class Aoe_TemplateHints_Model_Adminhtml_System_Config_Source_HintRenderer {

    /**
     * Get option array
     *
     * @return array
     */
    public function toOptionArray() {
        $options = array();
        $options[] = array(
            'value'=> 'aoe_templatehints/renderer_comment',
            'label'=> Mage::helper('aoe_templatehints')->__('Comments')
        );
        $options[] = array(
            'value'=> 'aoe_templatehints/renderer_opentip',
            'label'=> Mage::helper('aoe_templatehints')->__('Popups')
        );
        $options[] = array(
            'value'=> 'aoe_templatehints/renderer_tipOnly',
            'label'=> Mage::helper('aoe_templatehints')->__('Popups (border initially invisible)')
        );

        Mage::dispatchEvent('aoetemplatehints_hintrenderer_options', array('options' => &$options));

        return $options;
    }
}
