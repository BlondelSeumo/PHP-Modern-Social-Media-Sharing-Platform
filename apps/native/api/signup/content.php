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
    $data['code']     = 400;
    $user_data_fileds = array(
        'fname'       => fetch_or_get($_POST['first_name'],null),
        'lname'       => fetch_or_get($_POST['last_name'], null),
        'uname'       => fetch_or_get($_POST['username'], null),
        'email'       => fetch_or_get($_POST['email'], null),
        'password'    => fetch_or_get($_POST['password'], null)
    );

    foreach ($user_data_fileds as $field_name => $field_val) {
        if ($field_name == 'fname') {
            if (empty($field_val) || len_between($field_val, 3, 25) != true) {
                $data['err_code'] = "invalid_fname";
                $data['code']     = 410;
                $data['message']  = "Invalid user first name"; break;
            }
        }

        else if($field_name == 'lname') {
            if (empty($field_val) || len_between($field_val, 3, 25) != true) {
                $data['err_code'] = "invalid_lname";
                $data['code']     = 410;
                $data['message']  = "Invalid user last name"; break;
            }
        }

        else if ($field_name == 'uname') {
            if (empty($field_val) || len_between($field_val, 3, 25) != true || preg_match('/^[\w]+$/', $field_val) != true) {
                $data['err_code'] = "invalid_username";
                $data['code']     = 410;
                $data['message']  = "Invalid username"; break;
            }

            else if(cl_uname_exists($field_val)) {
                $data['err_code'] = "doubling_username";
                $data['code']     = 410;
                $data['message']  = "This username is already taken"; break;
            }
        }

        else if ($field_name == 'email') {
            if (empty($field_val) || (filter_var(trim($field_val), FILTER_VALIDATE_EMAIL) == false) || len($field_val) > 55) {
                $data['err_code'] = "invalid_email";
                $data['code']     = 410;
                $data['message']  = "Invalid email address"; break;
            }

            else if (cl_email_exists($field_val)) {
                $data['err_code'] = "doubling_email";
                $data['code']     = 409;
                $data['message']  = "Email ID already registered"; break;
            }
        }

        else if ($field_name == 'password') {
            if (empty($field_val) || len_between($field_val, 6, 20) != true) {
                $data['err_code'] = "invalid_password";
                $data['code']     = 410;
                $data['message']  = "Invalid password"; break;
            }
        }
    }

    if (empty($data['err_code'])) {
        $email_code       = sha1(time() + rand(111,999));
        $password_hashed  = password_hash($user_data_fileds["password"], PASSWORD_DEFAULT);
        $user_ip          = cl_get_ip();
        $user_ip          = ((filter_var($user_ip, FILTER_VALIDATE_IP) == true) ? $user_ip : '0.0.0.0');
        $insert_data      = array(
            'fname'       => cl_text_secure($user_data_fileds["fname"]),
            'lname'       => cl_text_secure($user_data_fileds["lname"]),
            'username'    => cl_text_secure($user_data_fileds["uname"]),
            'password'    => $password_hashed,
            'email'       => cl_text_secure($user_data_fileds["email"]),
            'active'      => '1',
            'em_code'     => $email_code,
            'last_active' => time(),
            'joined'      => time(),
            'ip_address'  => $user_ip,
            'language'    => $cl['config']['language'],
        ); $user_id       = $db->insert(T_USERS, $insert_data);

        if (is_posnum($user_id)) {
        	$data_exp               = (time() + (10 * 365 * 24 * 60 * 60));
            $session_id             = cl_create_user_session($user_id, "mobile");
            $data['code']           = 200;
            $data['message']        = "User logged in successfully";
            $data['data']           = array();
            $data['data']           = array();
            $data["auth"]           = array(
            	"auth_token"        => $session_id,
            	"auth_token_expiry" => $data_exp,
            );
        }
        else {
        	$data['err_code'] = "server_error";
            $data['code']     = 500;
            $data['message']  = "An error occurred while processing your request. Please try again later";
        }
    }
}