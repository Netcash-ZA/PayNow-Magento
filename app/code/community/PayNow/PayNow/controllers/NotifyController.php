<?php
/**
 * NotifyController.php
 */

// Include the PayFast common file
define( 'PN_DEBUG', ( Mage::getStoreConfig( 'payment/paynow/debugging' ) ? true : false ) );
include_once( dirname( __FILE__ ) .'/../paynow_common.inc' );


/**
 * PayNow_PayNow_NotifyController
 */
class PayNow_PayNow_NotifyController extends Mage_Core_Controller_Front_Action
{
    // {{{ indexAction()
	/**
	 * indexAction
     * 
     * Instantiate ITN model and pass ITN request to it
	 */
    public function indexAction()
    {
        // Variable Initialization
        $pnError = false;
        $pnErrMsg = '';
        $pnData = array();
        $pnHost = 'www.paynowurl.co.za';
        $pnOrderId = '';
        $pnParamString = '';
        
        pnlog( 'Pay Now IPN call received' );
        pnlog( 'Server = '. Mage::getStoreConfig( 'payment/paynow/server' ) );
        
        //// Notify PayFast that information has been received
        if( !$pnError )
        {
            header( 'HTTP/1.0 200 OK' );
            flush();
        }
        
        //// Get data sent by PayFast
        if( !$pnError )
        {
            pnlog( 'Get posted data' );
        
            // Posted variables from ITN
            $pnData = pnGetData();
        
            pnlog( 'PayFast Data: '. print_r( $pnData, true ) );
        
            if( $pnData === false )
            {
                $pnError = true;
                $pnErrMsg = PN_ERR_BAD_ACCESS;
            }
        }
        
        //// Verify security signature
        if( !$pnError )
        {
            pnlog( 'Verify security signature' );
        
            // If signature different, log for debugging
            if( !pnValidSignature( $pnData, $pnParamString ) )
            {
                $pnError = true;
                $pnErrMsg = PF_ERR_INVALID_SIGNATURE;
            }
        }
        
        //// Verify source IP (If not in debug mode)
        if( !$pnError && !defined( 'PN_DEBUG' ) )
        {
            pnlog( 'Verify source IP' );
        
            if( !pnValidIP( $_SERVER['REMOTE_ADDR'] ) )
            {
                $pnError = true;
                $pnErrMsg = PF_ERR_BAD_SOURCE_IP;
            }
        }
        
        //// Get internal order and verify it hasn't already been processed
        if( !$pnError )
        {
            pnlog( "Check order hasn't been processed" );
            
            // Load order
    		$trnsOrdId = $pnData['m_payment_id'];
    		$order = Mage::getModel( 'sales/order' );
            $order->loadByIncrementId( $trnsOrdId );
    		$this->_storeID = $order->getStoreId();
            
            // Check order is in "pending payment" state
            if( $order->getStatus() !== Mage_Sales_Model_Order::STATE_PENDING_PAYMENT )
            {
                $pnError = true;
                $pnErrMsg = PF_ERR_ORDER_PROCESSED;
            }
        }
        
        //// Verify data received
        if( !$pnError )
        {
            pnlog( 'Verify data received' );
        
            $pnValid = pnValidData( $pnHost, $pnParamString );
        
            if( !$pnValid )
            {
                $pnError = true;
                $pnErrMsg = PN_ERR_BAD_ACCESS;
            }
        }

        //// Check status and update order
        if( !$pnError )
        {
            pnlog( 'Check status and update order' );
            
            // Successful
            if( $pnData['payment_status'] == "COMPLETE" )
            {
                pnlog( 'Order complete' );
                
                // Update order additional payment information
                $payment = $order->getPayment(); 
        		$payment->setAdditionalInformation( "payment_status", $pnData['payment_status'] );
        		$payment->setAdditionalInformation( "m_payment_id", $pnData['m_payment_id'] );
                $payment->setAdditionalInformation( "pn_payment_id", $pnData['pn_payment_id'] );
                $payment->setAdditionalInformation( "email_address", $pnData['email_address'] );
        		$payment->setAdditionalInformation( "amount_fee", $pnData['amount_fee'] );
                $payment->save();

                // Save invoice
                $this->saveInvoice( $order );
            }
        }
        
        // If an error occurred
        if( $pnError )
        {
            pnlog( 'Error occurred: '. $pnErrMsg );
            
            // TODO: Use Magento structures to send email
        }
    }

    /**
	 * saveInvoice
	 */
	protected function saveInvoice( Mage_Sales_Model_Order $order )
    {
        pnlog( 'Saving invoice' );
        
		// Check for mail msg
		$invoice = $order->prepareInvoice();

		$invoice->register()->capture();
		Mage::getModel( 'core/resource_transaction' )
		   ->addObject( $invoice )
		   ->addObject( $invoice->getOrder() )
		   ->save();
		//$invoice->sendEmail();
		
		$message = Mage::helper( 'paynow' )->__( 'Notified customer about invoice #%s.', $invoice->getIncrementId() );
        $comment = $order->sendNewOrderEmail()->addStatusHistoryComment( $message )
              ->setIsCustomerNotified( true )
              ->save();
    }

}