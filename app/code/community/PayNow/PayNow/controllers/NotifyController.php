<?php
/**
 * NotifyController.php
 */

// Include the Sage Pay Now common file
define('PN_DEBUG', (Mage::getStoreConfig('payment/paynow/debugging') ? true : false));
include_once(dirname(__FILE__) . '/../paynow_common.inc');


/**
 * PayNow_PayNow_NotifyController
 */
class PayNow_PayNow_NotifyController extends Mage_Core_Controller_Front_Action
{
    /**
     * indexAction
     *
     * Instantiate IPN model and pass IPN request to it
     */
    public function indexAction()
    {
        // Variable Initialization
        $pnError = false;
        $pnErrMsg = '';
        $pnData = array();

        pnlog('Sage Pay Now IPN call received');
        pnlog('Server = ' . Mage::getStoreConfig('payment/paynow/server'));

        // Notify Pay Now that information has been received
        // Fails with 'headers already sent' on some servers
        // See http://stackoverflow.com/questions/8028957/how-to-fix-headers-already-sent-error-in-php
        //if (!$pnError) {
        //header('HTTP/1.0 200 OK');
        //flush();
        //}

        // Get data posted back by Pay Now
        if (!$pnError) {
            pnlog('Get data posted back by Pay Now');

            // Posted variables from ITN
            $pnData = pnGetData();

            pnlog('Sage Pay Now Data: ' . print_r($pnData, true));

            if ($pnData === false) {
                $pnError = true;
                $pnErrMsg = PN_ERR_BAD_ACCESS;
            }
        }

        if ($pnData['TransactionAccepted'] == 'false') {
            $pnError = true;
            $pnErrMsg = PN_MSG_FAILED;
        }

        // Get internal order and verify it hasn't already been processed
        if (!$pnError) {
            pnlog("Check if the order has not already been processed");

            // Load order
            $trnsOrdId = $pnData['Reference'];
            $order = Mage::getModel('sales/order');
            $order->loadByIncrementId($trnsOrdId);
            $this->_storeID = $order->getStoreId();

            // Check order is in "pending payment" state
            pnlog("The current order status is " . $order->getStatus());
            if ($order->getStatus() !== Mage_Sales_Model_Order::STATE_PENDING_PAYMENT) {
                $pnError = true;
                $pnErrMsg = PN_ERR_ORDER_PROCESSED;
            }
        }

        // Check status and update order
        if (!$pnError) {
            pnlog('Check status and update order');

            // Successful
            if ($pnData['TransactionAccepted'] == "true") {
                pnlog('Order complete');

                // Update order additional payment information
                $payment = $order->getPayment();
                $payment->setAdditionalInformation("TransactionAccepted", $pnData['TransactionAccepted']);
                $payment->setAdditionalInformation("Reference", $pnData['Reference']);
                $payment->setAdditionalInformation("RequestTrace", $pnData['RequestTrace']);
                //$payment->setAdditionalInformation( "email_address", $pnData['email_address'] );
                $payment->setAdditionalInformation("Amount", $pnData['Amount']);
                $payment->save();
                // Save invoice
                $this->saveInvoice($order);
            }
        }

        // If an error occurred show the reason and present a hyperlink back to the store
        if ($pnError) {
            pnlog('Transaction failed, reason: ' . $pnErrMsg);
            $url = Mage::getUrl('paynow/redirect/cancel', array('_secure' => true));
            echo "<html><body>";
            echo "Transaction failed, reason: " . $pnErrMsg . "<br><br>";
            if ($pnData['TransactionAccepted'] != "true") {
                pnlog('Return message from payment gateway: ' . $pnData['Reason']);
                echo "Return message from payment gateway: " . $pnData['Reason'];
            }
            echo "<a href='$url'>Click here to return to the store.</a>";
            echo "</body></html>";
        } else { // Redirect to the success page
            // return Mage::getUrl( 'paynow/redirect/success', array( '_secure' => true ) );
            $this->_redirect('paynow/redirect/success');
        }
    }

    /**
     * saveInvoice
     */
    protected function saveInvoice(Mage_Sales_Model_Order $order)
    {
        pnlog('Saving invoice');

        // Check for mail msg
        $invoice = $order->prepareInvoice();

        $invoice->register()->capture();
        Mage::getModel('core/resource_transaction')
            ->addObject($invoice)
            ->addObject($invoice->getOrder())
            ->save();
        //$invoice->sendEmail();

        $message = Mage::helper('paynow')->__('Notified customer about invoice #%s.', $invoice->getIncrementId());
        $comment = $order->sendNewOrderEmail()->addStatusHistoryComment($message)
            ->setIsCustomerNotified(true)
            ->save();
    }

}