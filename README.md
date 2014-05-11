Sage Pay Now Credit Card Payment Module for Magento
===================================================

Revision 1.0.1

Introduction
------------
A credit card module to take credit card transaction for Magento using Sage Pay South Africa's Pay Now gateway.

Installation Instructions
-------------------------
Download the files from GitHub:
* https://github.com/SagePay/PayNow-Magento/archive/master.zip

Copy all the files to your Magento /app folder.

Configuration
-------------

Prerequisites:

You will need:
* Sage Pay Now login credentials
* Sage Pay Now Service key
* OpenCart admin login credentials

Sage Pay Now Gateway Server Configuration Steps:

1. Log into your Sage Pay Now Gateway Server configuration page:
	https://merchant.sagepay.co.za/SiteLogin.aspx
2. Go to Account / Profile
3. Click Sage Connect
4. Click Pay Now
5. Make a note of your Service key

Sage Pay Now Callback

6. Choose the following for both your Accept and Decline URLs:
	> http://magento_installation/index.php/paynow/notify

Magento Steps:

1. Log into Magento as admin (http://magento_installation/index.php/admin/)
2. Click on System / Configuration, and scroll down to 'Payment Methods'
3. In this list you will find "PayNow".
4. Choose 'Enabled'
5. Give the gateway a title (e.g. MasterCard/VISA)
6. Type in your Pay Now Service Key
7. Click 'Save Config' on the top right

![alt tag](http://magento.gatewaymodules.com/magento_screenshot1.png)

Revision History
----------------

* 11 May 2014/1.0.1 Documentation with callback URL
* 09 Mar 2014/1.0.0 First version

Tested with Magento version 

Demo Site
---------

Below is a Magento demo site with the Sage Pay Now credit card gateway module implemented:

http://magento.gatewaymodules.com

Issues & Feature Requests
-------------------------

We welcome your feedback.

Please contact Sage Pay South Africa with any questions or issues.
