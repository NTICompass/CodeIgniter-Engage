<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Config file for Janrain Engage
 */

// Enter your rpxnow.com domain here (no trailing slash)
// Example: https://example-app.rpxnow.com
$rpxnow = 'YOUR RPX URL';

// Enter your API key and token URL here
$config['RPX'] = array(
		'api_key' => 'YOUR API KEY',
		'token_url' => 'YOUR TOKEN URL',
);

/**
 * Do not edit anything else in this file
 */

// RPX Config Entries
$config['RPX']['post_url'] = "https://rpxnow.com/api/v2/";
$config['RPX']['embed'] = "$rpxnow/openid/embed";
$config['RPX']['signin'] = "$rpxnow/openid/v2/signin";
$config['RPX']['script'] = "https://rpxnow.com/js/lib/rpx.js";
?>
