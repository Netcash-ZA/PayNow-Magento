<?php
/**
 * Standard.php 
 */

/**
 * SagePayNow_SagePayNow_Model_Standard
 */
class SagePayNow_SagePayNow_Model_Standard extends Mage_Payment_Model_Method_Abstract
{
	protected $_code = 'sagepaynow';
	protected $_formBlockType = 'sagepaynow/form';
	protected $_infoBlockType = 'sagepaynow/payment_info';
	protected $_order;
	
	protected $_isGateway              = true;
	protected $_canAuthorize           = true;
	protected $_canCapture             = true;
	protected $_canCapturePartial      = false;
	protected $_canRefund              = false;
	protected $_canVoid                = true;
	protected $_canUseInternal         = true;
	protected $_canUseCheckout         = true;
	protected $_canUseForMultishipping = true;
	protected $_canSaveCc			   = false;

    /**
     * getCheckout
     */
	public function getCheckout()
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
     * getQuote
     */
	public function getConfig()
    {
        return Mage::getSingleton( 'sagepaynow/config' );
    }

    /**
     * getOrderPlaceRedirectUrl
     */
	public function getOrderPlaceRedirectUrl()
	{
		return Mage::getUrl( 'sagepaynow/redirect/redirect', array( '_secure' => true ) );
	}
    // }}}
    // {{{ getPaidSuccessUrl()
    /**
     * getPaidSuccessUrl
     */
	public function getPaidSuccessUrl()
	{
		return Mage::getUrl( 'sagepaynow/redirect/success', array( '_secure' => true ) );
	}

    /**
     * getPaidCancelUrl
     */
	public function getPaidCancelUrl()
	{
		return Mage::getUrl( 'sagepaynow/redirect/cancel', array( '_secure' => true ) );
	}

    /**
     * getPaidNotifyUrl
     */
	public function getPaidNotifyUrl()
	{
		return Mage::getUrl( 'sagepaynow/notify', array( '_secure' => true ) );
	}

    /**
     * getRealOrderId
     */
	public function getRealOrderId()
    {
        return Mage::getSingleton( 'checkout/session' )->getLastRealOrderId();
    }

    /**
     * getNumberFormat
     */
	public function getNumberFormat( $number )
    {
        return number_format( $number, 2, '.', '' );
    }

    /**
     * getTotalAmount
     */
	public function getTotalAmount( $order )
    {
		if( $this->getConfigData( 'use_store_currency' ) )
            $price = $this->getNumberFormat( $order->getGrandTotal() );
    	else
        	$price = $this->getNumberFormat( $order->getBaseGrandTotal() );

		return $price;
	}

    /**
     * getStoreName
     */
	public function getStoreName()
    {
		$store_info = Mage::app()->getStore();
		return $store_info->getName();
	}

    /**
     * getStandardCheckoutFormFields
     */
	public function getStandardCheckoutFormFields()
	{
		// Variable initialization
        $orderIncrementId = $this->getCheckout()->getLastRealOrderId();
        $order = Mage::getModel( 'sales/order' )->loadByIncrementId( $orderIncrementId );
		$description = '';
		
		// Sage Pay Now service key
        $serviceKey = $this->getConfigData( 'service_key' );
        // Sage Pay Now software vendor key
        $softwareVendorKey = '24ade73c-98cf-47b3-99be-cc7b867b3080';
        
        // Create description
        foreach( $order->getAllItems() as $items )
        {
			$totalPrice = $this->getNumberFormat( $items->getQtyOrdered() * $items->getPrice() );
			$description .=
                $this->getNumberFormat( $items->getQtyOrdered() ) .
                ' x '. $items->getName() .
                ' @ '. $order->getOrderCurrencyCode() . $this->getNumberFormat( $items->getPrice() ) .
                ' = '. $order->getOrderCurrencyCode() . $totalPrice .'; ';
		}
		$description .= 'Shipping = '. $order->getOrderCurrencyCode() . $this->getNumberFormat( $order->getShippingAmount() ).';';
		$description .= 'Total = '. $order->getOrderCurrencyCode() . $this->getTotalAmount( $order ).';';
		
        // Construct data for the form
		$data = array(
            // Merchant details
            
			'm1' => $serviceKey,
			'm2' => $softwareVendorKey,
			'return_url' => $this->getPaidSuccessUrl(),
			'cancel_url' => $this->getPaidCancelUrl(),
			'notify_url' => $this->getPaidNotifyUrl(),
			
            // Buyer details
			// M9 = cardholder
			'm9' => $order->getData( 'customer_email' ),
            
            // Item details
            // P3 = description
			'p3' => $this->getStoreName().', Order #'.$this->getRealOrderId(),			
			'p4' => $this->getTotalAmount( $order ),
			// p2 = unique ref
			'p2' => $this->getRealOrderId()            
            
        );
        
		return( $data );
	}

    /**
     * initialize
     */
    public function initialize( $paymentAction, $stateObject )
    {
        $state = Mage_Sales_Model_Order::STATE_PENDING_PAYMENT;
        $stateObject->setState( $state );
        $stateObject->setStatus( 'pending_payment' );
        $stateObject->setIsNotified( false );
    }

    /**
     * getPayNowUrl
     * 
     * Get URL for form submission to Sage Pay Now.
     */
	public function getPayNowUrl()
    {
		$url = 'https://paynow.sagepay.co.za/site/paynow.aspx';        
		return( $url );
    }

}