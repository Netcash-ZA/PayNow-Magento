<?php
/**
 * Ipn.php
 */

/**
 * Mage_Paypal_Model_Ipn
 */
class Mage_Paypal_Model_Ipn
{
    /**
     * getWriteLog
     */
	public function getWriteLog( $data )
    {
		$text = "\n";
		$text .= "RESPONSE: From Pay Now[". date("Y-m-d H:i:s") ."]"."\n";
		
        foreach( $_REQUEST as $key => $val )
			$text .= $key."=>".$val."\n";

		$file = dirname( dirname( __FILE__ ) ) ."/Logs/notify.txt";
		
		$handle = fopen( $file, 'a' );
		fwrite( $handle, $text );
		fclose( $handle );
	}
}