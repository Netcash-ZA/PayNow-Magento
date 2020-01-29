Netcash Pay Now Credit Card Payment Module for Magento
===================================================

Revision 2.0.0

Introduction
------------
A credit card module to take credit card transaction for Magento using Netcash South Africa's Pay Now gateway.

Installation Instructions
-------------------------
Download the files from GitHub:
* https://github.com/Netcash-ZA/PayNow-Magento/archive/master.zip

Copy all the files to your Magento /app folder.

Configuration
-------------

Prerequisites:

You will need:
* Netcash account
* Pay Now service activated
* Netcash account login credentials (with the appropriate permissions setup)
* Netcash - Pay Now Service key
* Cart admin login credentials

A. Netcash Account Configuration Steps:
1. Log into your Netcash account:
	https://merchant.netcash.co.za/SiteLogin.aspx
2. Type in your Username, Password, and PIN
2. Click on ACCOUNT PROFILE on the top menu
3. Select NETCONNECTOR from tghe left side menu
4. Click on PAY NOW from the subsection
5. ACTIVATE the Pay Now service
6. Type in your EMAIL address
7. It is highly advisable to activate test mode & ignore errors while testing
8. Select the PAYMENT OPTIONS required (only the options selected will be displayed to the end user)
9. Remember to remove the "Make Test Mode Active" indicator to accept live payments

* For immediate assistance contact Netcash on 0861 338 338



Netcash Pay Now Callback

10. Choose the following for your Accept, Decline, Notify & Redirect URLs:
	> http://magento_installation/index.php/paynow/notify

Magento Steps:

1. Log into Magento as admin (http://magento_installation/index.php/admin/)
2. Click on System / Configuration, and scroll down to 'Payment Methods'
3. In this list you will find "PayNow".
4. Choose 'Enabled'
5. Give the gateway a title (e.g. MasterCard/VISA)
6. Type in your Pay Now Service Key
7. Click 'Save Config' on the top right

Issues & Feature Requests
-------------------------

We welcome your feedback.

Please contact Netcash South Africa with any questions or issues.
