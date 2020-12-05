<?php 
# @*************************************************************************@
# @ @author Mansur Altamirov (Mansur_TL)									@
# @ @author_url 1: https://www.instagram.com/mansur_tl                      @
# @ @author_url 2: http://codecanyon.net/user/mansur_tl                     @
# @ @author_email: highexpresstore@gmail.com                                @
# @*************************************************************************@
# @ ColibriSM - The Ultimate Modern Social Media Sharing Platform           @
# @ Copyright (c) 21.03.2020 ColibriSM. All rights reserved.                @
# @*************************************************************************@

$provider_ls = array('Google','Facebook','Twitter');
$provider    = false;

if (not_empty($_GET['provider']) && in_array($_GET['provider'], $provider_ls)) {
    $provider = $_GET['provider'];
}

require_once(cl_full_path("core/libs/oAuth/vendor/autoload.php"));
require_once(cl_full_path("core/libs/oAuth/oauth_config.php"));

if ($provider) {
    try {
        $hybridauth    = new Hybridauth\Hybridauth($oauth_config);
        $auth_provider = $hybridauth->authenticate($provider);
        $tokens        = $auth_provider->getAccessToken();
        $user_profile  = $auth_provider->getUserProfile();

        if ($user_profile && isset($user_profile->identifier)) {
            $fname      = fetch_or_get($user_profile->firstName, time());
            $lname      = fetch_or_get($user_profile->lastName, time());
            $prov_email = "mail.com";
            $prov_prefx = "xx_";

            if ($provider == 'Google') {
                $prov_email = 'google.com';
                $prov_prefx = 'go_';
            } 

            else if ($provider == 'Facebook') {
                $prov_email = 'facebook.com';
                $prov_prefx = 'fa_';
            } 

            else if ($provider == 'Twitter') {
                $prov_email = 'twitter.com';
                $prov_prefx = 'tw_';
            }

            $user_name  = uniqid($prov_prefx);
            $user_email = cl_strf('%s@%s', $user_name, $prov_email);

            if (not_empty($user_profile->email)) {
                $user_email = $user_profile->email;
            }

            if (cl_email_exists($user_email) === true) {
            	$db        = $db->where('email', $user_email);
            	$user_data = $db->getOne(T_USERS);

                cl_create_user_session($user_data['id'], 'web');
                cl_redirect('/');
            } 

            else {
            	$about            = fetch_or_get($user_profile->description, "");
            	$email_code       = sha1(time() + rand(111,999));
		        $password_hashed  = password_hash(time(), PASSWORD_DEFAULT);
		        $user_ip          = cl_get_ip();
		        $user_ip          = ((filter_var($user_ip, FILTER_VALIDATE_IP) == true) ? $user_ip : '0.0.0.0');
		        $user_id          = $db->insert(T_USERS, array(
		            'fname'       => cl_text_secure($fname),
		            'lname'       => cl_text_secure($lname),
		            'username'    => $user_name,
		            'password'    => $password_hashed,
		            'email'       => $user_email,
		            'active'      => '1',
		            'about'       => cl_croptxt($about, 130),
		            'em_code'     => $email_code,
		            'last_active' => time(),
		            'joined'      => time(),
		            'ip_address'  => $user_ip,
		            'language'    => $cl['config']['language'],
		        ));

		        if (is_posnum($user_id)) {

		        	cl_create_user_session($user_id,'web');

		            $avatar = fetch_or_get($user_profile->photoURL, null);

	                if (is_url($avatar)) {
	                	$avatar = cl_import_image(array(
	                		'url' => $avatar,
	                		'file_type' => 'thumbnail',
				            'folder' => 'avatars',
				            'slug' => 'avatar',
	                	));

	                	if ($avatar) {
	                		cl_update_user_data($user_id, array('avatar' => $avatar));
	                	}
	                }

		            cl_redirect('/');
		        }
            }
        }
    }
    catch (Exception $e) {
        exit($e->getMessage());
    }
} 

else {
    cl_redirect("/");
}
