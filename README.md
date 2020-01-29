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
* Netcash login credentials
* Netcash Pay Now Service key
* OpenCart admin login credentials

Netcash Pay Now Gateway Server Configuration Steps:

1. Log into your Netcash account:
	https://merchant.netcash.co.za/SiteLogin.aspx
2. Go to Account / Profile
3. Click NetConnector
4. Click Pay Now
5. Make a note of your Service key

Netcash Pay Now Callback

6. Choose the following for your Accept, Decline, Notify & Redirect URLs:
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
