<?php
/**
 * Server.php
 */

/**
 * PayNow_PayNow_Model_Source_Server
 */
class PayNow_PayNow_Model_Source_Server
{
    /**
     * toOptionArray
     */ 
    public function toOptionArray()
    {
        return array(
            array( 'value' => 'test', 'label' => Mage::helper( 'payfast' )->__( 'Test' ) ),
            array( 'value' => 'live', 'label' => Mage::helper( 'payfast' )->__( 'Live' ) ),
        );
    }
}