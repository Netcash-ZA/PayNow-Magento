<?php
/**
 * Server.php
 */

/**
 * PayNow_PayNow_Model_Source_Server
 */
class SagePayNow_SagePayNow_Model_Source_Server
{
    /**
     * toOptionArray
     */ 
    public function toOptionArray()
    {
        return array(
            array( 'value' => 'test', 'label' => Mage::helper( 'sagepaynow' )->__( 'Test' ) ),
            array( 'value' => 'live', 'label' => Mage::helper( 'sagepaynow' )->__( 'Live' ) ),
        );
    }
}