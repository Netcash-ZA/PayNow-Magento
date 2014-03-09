<?php
/**
 * RedirectController.php
 */

// Include the PayFast common file
define( 'PN_DEBUG', ( Mage::getStoreConfig( 'payment/paynow/debugging' ) ? true : false ) );
include_once( dirname( __FILE__ ) .'/../paynow_common.inc' );
 
/**
 * PayNow_PayNow_RedirectController
 */
class PayNow_PayNow_RedirectController extends Mage_Core_Controller_Front_Action
{
    protected $_order;
	protected $_WHAT_STATUS = false;

    // {{{ getOrder()
    /**
     * getOrder
     */
    public function getOrder()
    {
        return( $this->_order );
    }
    // }}}
    // {{{ _expireAjax()
    /**
     * _expireAjax
     */
    protected function _expireAjax()
    {
        if( !Mage::getSingleton( 'checkout/session' )->getQuote()->hasItems() )
        {
            $this->getResponse()->setHeader( 'HTTP/1.1', '403 Session Expired' );
            exit;
        }
    }

    /**
     * _getCheckout
     * 
     * Get singleton of Checkout Session Model
     *
     * @return Mage_Checkout_Model_Session
     */
    protected function _getCheckout()
    {
        return Mage::getSingleton( 'checkout/session' );
    }
    
    /**
     * getQuote
     */
	public function getQuote()
    {
        return $this->getCheckout()->getQuote();
    }
    
    /**
     * getStandard()
     */
    public function getStandard()
    {
        return Mage::getSingleton( 'paynow/standard' );
    }
   
    /**
     * getConfig
     */
	public function getConfig()
    {
        return $this->getStandard()->getConfig();
    }
    
    /**
     * _getPendingPaymentStatus
     */
    protected function _getPendingPaymentStatus()
    {
        return Mage::helper( 'paynow' )->getPendingPaymentStatus();
    }
    
    /**
     * redirectAction
     */
    public function redirectAction()
    {
        pnlog( 'Redirecting to Pay Now' );
        
		try
        {
            $session = Mage::getSingleton( 'checkout/session' );

            $order = Mage::getModel( 'sales/order' );
            $order->loadByIncrementId( $session->getLastRealOrderId() );
        
            if( !$order->getId() )
                Mage::throwException( 'No order for processing found' );
        
            if( $order->getState() != Mage_Sales_Model_Order::STATE_PENDING_PAYMENT )
            {
                $order->setState(
                    Mage_Sales_Model_Order::STATE_PENDING_PAYMENT,
                    $this->_getPendingPaymentStatus(),
                    Mage::helper( 'paynow' )->__( 'Customer was redirected to Pay Now.' )
                )->save();
            }

            if( $session->getQuoteId() && $session->getLastSuccessQuoteId() )
            {
                $session->setPaynowQuoteId( $session->getQuoteId() );
                $session->setPaynowSuccessQuoteId( $session->getLastSuccessQuoteId() );
                $session->setPaynowRealOrderId( $session->getLastRealOrderId() );
                $session->getQuote()->setIsActive( false )->save();
                $session->clear();
            }
			
			$this->getResponse()->setBody( $this->getLayout()->createBlock( 'paynow/request' )->toHtml() );
	        $session->unsQuoteId();
            
            return;
        }
        catch( Mage_Core_Exception $e )
        {
            $this->_getCheckout()->addError( $e->getMessage() );
        }
        catch( Exception $e )
        {
            Mage::logException($e);
        }
        
        $this->_redirect( 'checkout/cart' );
    }
   
    /**
     * cancelAction
     * 
     * Action for when a user cancel's a payment on Pay Now.
     * @TODO Cancel Action does not exist on Pay Now, remove
     */
    public function cancelAction()
    {
		// Get the user session
        $session = Mage::getSingleton( 'checkout/session' );
        $session->setQuoteId( $session->getPaynowQuoteId( true ) );
		$session = $this->_getCheckout();
        
        if( $quoteId = $session->getPaynowQuoteId() )
        {
            $quote = Mage::getModel( 'sales/quote' )->load( $quoteId );
            
            if( $quote->getId() )
            {
                $quote->setIsActive( true )->save();
                $session->setQuoteId( $quoteId );
            }
        }
		
        // Cancel order
		$order = Mage::getModel( 'sales/order' )->loadByIncrementId( $session->getLastRealOrderId() );
		if( $order->getId() )
            $order->cancel()->save();

        $this->_redirect('checkout/cart');
    }

    /**
     * successAction
     */
    public function successAction()
    {
		try
        {
			$session = Mage::getSingleton( 'checkout/session' );;
			$session->unsPaynowRealOrderId();
			$session->setQuoteId( $session->getPaynowQuoteId( true ) );
			$session->setLastSuccessQuoteId( $session->getPaynowSuccessQuoteId( true ) );
			$this->_redirect( 'checkout/onepage/success', array( '_secure' => true ) );
			
            return;
		}
        catch( Mage_Core_Exception $e )
        {
			$this->_getCheckout()->addError( $e->getMessage() );
		}
        catch( Exception $e )
        {
			Mage::logException( $e );
		}
		
        $this->_redirect( 'checkout/cart' );
    }

}