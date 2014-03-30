<?php
/**
 * Info.php
 */
 
/**
 * SagePayNow_SagePayNow_Block_Payment_Info 
 */
class SagePayNow_SagePayNow_Block_Payment_Info extends Mage_Payment_Block_Info
{    
    /**
     * _prepareSpecificInformation 
     */
    protected function _prepareSpecificInformation( $transport = null )
    {
        $transport = parent::_prepareSpecificInformation( $transport );
        $payment = $this->getInfo();
        $pfInfo = Mage::getModel( 'sagepaynow/info' );
        
        if( !$this->getIsSecureMode() )
            $info = $pfInfo->getPaymentInfo( $payment, true );
        else
            $info = $pfInfo->getPublicPaymentInfo( $payment, true );

        return( $transport->addData( $info ) );
    } 
}