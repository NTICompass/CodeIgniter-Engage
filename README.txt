Janrain Engage Library v2.0 Documentation
(http://codeigniter.com/wiki/Janrain_Engage)

By: Eric Siegel
NTICompass@gmail.com
http://NTICompassInc.com

0. Notes
--------
Some API calls require a Plus/Pro account.
This library requies PHP5 with cURL and JSON.
It also requires that CodeIgntier has query strings enabled.
  Set $config['enable_query_strings'] to TRUE in config/config.php.

1. Installation
---------------
Copy the included files into your application folder.
application/
  config/
    enagage_conf.php
  controllers/
    engage_login.php
  libraries/
    engage.php

Open the engage_conf.php and set your RPX URL, API Key, and Token URL (will be setup in next step).

2. Configure Token URL
----------------------
When a user uses Engage to login, rpxnow.com passes your Token URL a 'token' that is used to get the user's data.
We need to create a controller to receive this token and make the correct API calls.  A basic controller is included in this package.
rpxnow.com passes the token using GET, so we need to make sure CodeIgniter can accept this.
We can use .htaccess to make this URL.  Add the following to your .htaccess file (and edit accordingly).

RewriteEngine On
RewriteBase /
RewriteRule ^engagelogin$ index.php?c=engage [QSA]

This will make your Token URL http://example.com/engagelogin.  This will be rewritten to http://example.com/index.php?c=engage.
The '[QSA]' flag will make sure the token is passed to the controller.

3. Adding Engage login box to page
----------------------------------
This library contains 3 helper functions to add the Engage login box to the page.
a) Popup login box:
To use the popup login box, use the script() and popup() functions.
Example:
// Clicking this link will make the login box popup
$this->engage->popup("Sign in"); // Prints a link with the text given
// Add this to the footer
$this->engage->script(); // Prints the javascript tags needed for the popup

b) Embed login box:
The embed() functions embeds the login box in to the page
Example:
// Embed login box as iFrame (text/html object)
$this->engage->embed(); // Prints the iFrame tag to embed login box

4. API Reference
----------------
See API_Ref.txt
