<?php
/**
 * Data.php
 */

/**
 * SagePayNow_SagePayNow_Helper_Data
 */
class SagePayNow_SagePayNow_Helper_Data extends Mage_Payment_Helper_Data
{    
    /**
     * getPendingPaymentStatus
     */
    public function getPendingPaymentStatus()
    {
        if( version_compare( Mage::getVersion(), '1.4.0', '<' ) )
            return( Mage_Sales_Model_Order::STATE_HOLDED );
        else
            return( Mage_Sales_Model_Order::STATE_PENDING_PAYMENT );
    } 
}
