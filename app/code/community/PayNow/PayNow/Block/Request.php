<?php
/**
 * Request.php 
 */

/**
 * PayNow_PayNow_Block_Request 
 */
class PayNow_PayNow_Block_Request extends Mage_Core_Block_Abstract
{    
    /**
     * _toHtml 
     */
    protected function _toHtml()
    {
        $standard = Mage::getModel( 'paynow/standard' );
        $form = new Varien_Data_Form();
        $form->setAction( $standard->getPayNowUrl() )
            ->setId( 'paynow_checkout' )
            ->setName( 'paynow_checkout' )
            ->setMethod( 'POST' )
            ->setUseContainer( true );
        
        foreach( $standard->getStandardCheckoutFormFields() as $field=>$value )
            $form->addField( $field, 'hidden', array( 'name' => $field, 'value' => $value, 'size' => 200 ) );
        
        $html = '<html><body>';
        $html.= $this->__( 'You will be redirected to Sage Pay Now in a few seconds.' );
        $html.= $form->toHtml();
		#echo $html;exit;
        $html.= '<script type="text/javascript">document.getElementById( "paynow_checkout" ).submit();</script>';
        $html.= '</body></html>';
        return $html;
    } 
}