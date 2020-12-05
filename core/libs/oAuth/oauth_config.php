<?php
# @*************************************************************************@
# @ @author Mansur Altamirov (Mansur_TL)                                    @
# @ @author_url 1: https://www.instagram.com/mansur_tl                      @
# @ @author_url 2: http://codecanyon.net/user/mansur_tl                     @
# @ @author_email: highexpresstore@gmail.com                                @
# @*************************************************************************@
# @ HighExpress - The Ultimate Modern Marketplace Platform                  @
# @ Copyright (c) 05.07.19 HighExpress. All rights reserved.                @
# @*************************************************************************@

$provider     = ((empty($provider)) ? "None" : $provider);
$oauth_config = array(
	"callback"       => cl_link(cl_strf("oauth?provider=%s", $provider)),
	"providers"      => array(
		"Google"     => array(
			"enabled" => true,
			"keys"    => array(
				"id"     => $cl['config']['google_api_id'],
				"secret" => $cl['config']['google_api_key']
			),
		),
		"Facebook" => array(
			"enabled" => true,
			"keys"    => array(
				"id" => $cl['config']['facebook_api_id'], 
				"secret" => $cl['config']['facebook_api_key']
			),
			"scope" => "email",
			"trustForwarded" => false
		),
		"Twitter" => array(
			"enabled" => true,
			"keys" => array(
				"key" => $cl['config']['twitter_api_id'], 
				"secret" => $cl['config']['twitter_api_key']
			),
			"includeEmail" => true
		),
	),
	"debug_mode" => false,
	"debug_file" => "",
);