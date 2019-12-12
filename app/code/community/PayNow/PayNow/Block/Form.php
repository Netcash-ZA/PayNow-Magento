<?php
/**
 * Form.php
 */

/**
 * PayNow_PayNow_Block_Form
 */
class PayNow_PayNow_Block_Form extends Mage_Payment_Block_Form
{
    /**
     * _construct()
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate( 'paynow/form.phtml' );
    }
}