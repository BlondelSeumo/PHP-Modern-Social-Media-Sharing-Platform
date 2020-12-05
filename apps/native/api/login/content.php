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

if (not_empty($cl['is_logged'])) {
	$data         = array(
		'code'    => 400,
		'message' => 'You are already logged in'
	);
}
else {
	$data['err_code'] = 0;
	$user_data_fileds = array(
		'email'       => fetch_or_get($_POST['email'], null),
		'password'    => fetch_or_get($_POST['password'], null),
	);

	foreach ($user_data_fileds as $field_name => $field_val) {
		if ($field_name == 'email') {
			if (empty($field_val) || len($field_val) > 55) {
	            $data['err_code'] = cl_strf("invalid_%s", $field_name);
	            $data['code']     = 402;
	        	$data['message']  = "Incorrect Credentials";
	        	$data['data']     = array(); break;
	        }
		}

		else if ($field_name == 'password') {
			if (empty($field_val) || len($field_val) > 20) {
	            $data['err_code'] = cl_strf("invalid_%s", $field_name);
	            $data['code']     = 402;
	        	$data['message']  = "Incorrect Credentials";
	        	$data['data']     = array(); break;
	        }
		}
	}

	if (empty($data['err_code'])) {
        $email    = cl_text_secure($user_data_fileds['email']);
        $password = cl_text_secure($user_data_fileds['password']);
        $db       = $db->where("active", array("1", "2"), "IN");
        $db       = $db->where("email", $email);
        $raw_user = $db->getOne(T_USERS);

        if (cl_queryset($raw_user) != true) {
        	$data['code']     = 402;
        	$data['message']  = "Incorrect Credentials";
        	$data['data']     = array();
        	$data['err_code'] = "invalid_creds";
        } 

        else if (password_verify($password, $raw_user["password"]) != true) {
        	$data['err_code'] = "invalid_creds";
        	$data['code']     = 402;
        	$data['message']  = "Incorrect Credentials";
        	$data['data']     = array();
        } 

        if (empty($data["err_code"])) {   
        	$user_ip      = cl_get_ip();
        	$data_exp     = (time() + (10 * 365 * 24 * 60 * 60));
        	$user_ip      = ((filter_var($user_ip, FILTER_VALIDATE_IP) == true) ? $user_ip : '0.0.0.0');
	        $session_id   = cl_create_user_session($raw_user["id"], "mobile");
            $data['code'] = 200;

            cl_update_user_data($raw_user["id"], array(
            	'ip_address'  => $user_ip,
            	'last_active' => time(),
            ));

            $data['code']     = 200;
            $data['message']  = "User logged in successfully";
            $data['data']     = array(
            	'id'          => $raw_user['id'],
            	'first_name'  => $raw_user['fname'],
            	'last_name'   => $raw_user['lname'],
            	'user_name'   => $raw_user['username'],
            	'email'       => $raw_user['email'],
            	'is_verified' => (($raw_user['verified'] == '1') ? true : false),
            	'website'     => $raw_user['website'],
            	'about_you'   => $raw_user['about'],
            	'gender'      => $raw_user['gender'],
            	'country'     => $cl['countries'][$raw_user['country_id']],
            	'post_count'  => $raw_user['posts'],
                'last_post'   => $raw_user['last_post'],
                'last_ad'     => $raw_user['last_ad'],
                'language'    => $raw_user['language'],
            	'following_count' => $raw_user['following'],
            	'follower_count'  => $raw_user['followers'],
                'wallet'          => $raw_user['wallet'],
                'ip_address'      => $raw_user['ip_address'],
                'last_active'     => $raw_user['last_active'],
            	'member_since'    => date("M Y", $raw_user['joined']),
                'profile_privacy' => $raw_user['profile_privacy']
            );

            $data["auth"]           = array(
            	"auth_token"        => $session_id,
            	"auth_token_expiry" => $data_exp,
            );
        }
    }
}