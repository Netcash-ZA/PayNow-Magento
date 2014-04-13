<?php
/**
 * Info.php
 */
 
/**
 * PayNow_PayNow_Block_Payment_Info 
 */
class PayNow_PayNow_Block_Payment_Info extends Mage_Payment_Block_Info
{    
    /**
     * _prepareSpecificInformation 
     */
    protected function _prepareSpecificInformation( $transport = null )
    {
        $transport = parent::_prepareSpecificInformation( $transport );
        $payment = $this->getInfo();
        $pfInfo = Mage::getModel( 'paynow/info' );
        
        if( !$this->getIsSecureMode() )
            $info = $pfInfo->getPaymentInfo( $payment, true );
        else
            $info = $pfInfo->getPublicPaymentInfo( $payment, true );

        return( $transport->addData( $info ) );
    } 
}