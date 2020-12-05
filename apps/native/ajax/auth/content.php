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

if ($action == "login") {
	$data['err_code']  = 0;
	$user_data_fileds  = array(
		'email'        => fetch_or_get($_POST['email'],null),
		'password'     => fetch_or_get($_POST['password'],null),
	);

	foreach ($user_data_fileds as $field_name => $field_val) {
		if ($field_name == 'email') {
			if (empty($field_val) || len($field_val) > 55) {
	            $data['err_code'] = $field_name; break;
	        }
		}

		if ($field_name == 'password') {
			if (empty($field_val) || len($field_val) > 20) {
	            $data['err_field'] = $field_name; break;
	        }
		}
	}

	if (empty($data['err_code'])) {
        $email    = cl_text_secure($user_data_fileds['email']);
        $password = cl_text_secure($user_data_fileds['password']);
        $db       = $db->where("active", array("1", "2"), "IN");
        $db       = $db->where("email", $email);
        $raw_user = $db->getOne(T_USERS, array("password", "id", "active"));

        if (cl_queryset($raw_user) != true) {
        	$data['err_code'] = "invalid_creds";
        } 

        else if (password_verify($password, $raw_user["password"]) != true) {
        	$data['err_code'] = "invalid_creds";
        } 

        if (empty($data["err_code"])) {   
        	$user_ip        = cl_get_ip();
        	$user_ip        = ((filter_var($user_ip, FILTER_VALIDATE_IP) == true) ? $user_ip : '0.0.0.0');
	        $session_id     = cl_create_user_session($raw_user["id"], "web");
            $data['status'] = 200;

            cl_update_user_data($raw_user["id"],array(
            	'ip_address'  => $user_ip,
            	'last_active' => time(),
            ));
        }
    }
}

else if ($action == 'signup') {
    $data['err_code']  = 0;
    $data['status']    = 400;
    $user_data_fileds  = array(
        'fname'        => fetch_or_get($_POST['fname'],null),
        'lname'        => fetch_or_get($_POST['lname'],null),
        'uname'        => fetch_or_get($_POST['uname'],null),
        'email'        => fetch_or_get($_POST['email'],null),
        'password'     => fetch_or_get($_POST['password'],null),
        'conf_pass'    => fetch_or_get($_POST['conf_pass'],null),
    );

    foreach ($user_data_fileds as $field_name => $field_val) {
        if ($field_name == 'fname') {
            if (empty($field_val) || len_between($field_val,3, 25) != true) {
                $data['err_code'] = "invalid_fname"; break;
            }
        }

        else if($field_name == 'lname') {
            if (empty($field_val) || len_between($field_val,3, 25) != true) {
                $data['err_code'] = "invalid_lname"; break;
            }
        }

        else if ($field_name == 'uname') {
            if (empty($field_val)) {
                $data['err_code'] = "invalid_uname"; break;
            }

            else if (len_between($field_val,3, 25) != true) {
                $data['err_code'] = "invalid_uname"; break;
            }

            else if (preg_match('/^[\w]+$/', $field_val) != true) {
                $data['err_code'] = "invalid_uname"; break;
            }

            else if(cl_uname_exists($field_val)) {
                $data['err_code'] = "doubling_uname"; break;
            }
        }

        else if ($field_name == 'email') {
            if (empty($field_val)) {
                $data['err_code'] = "invalid_email"; break;
            }

            else if (!filter_var(trim($field_val), FILTER_VALIDATE_EMAIL) || len($field_val) > 55) {
                $data['err_code'] = "invalid_email"; break;
            }

            else if (cl_email_exists($field_val)) {
                $data['err_code'] = "doubling_email"; break;
            }
        }

        else if ($field_name == 'password') {
            if (empty($field_val) || len_between($field_val,6,20) != true) {
                $data['err_code'] = "invalid_password"; break;
            }
        }

        else if($field_name == 'conf_pass') {
            if (empty($field_val) || ($field_val != $user_data_fileds['password'])) {
                $data['err_code'] = "invalid_password"; break;
            }
        }
    }

    if (empty($data['err_code'])) {

        if ($cl['config']['acc_validation'] == 'off') {
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
            ); $user_id       =  $db->insert(T_USERS, $insert_data);

            if (is_posnum($user_id)) {
                cl_create_user_session($user_id,'web');
                $data['status'] = 200;
            }
        }

        else {
            $rand_code                   = rand(100000,999999);
            $user_data_fileds['em_code'] = $rand_code;
            $user_name         = cl_strf("%s %s", $user_data_fileds['fname'], $user_data_fileds['lname']);
            $user_email        = $user_data_fileds['email'];
            $cl['email_data']  = array('name' => $user_name, 'code' => $rand_code);
            $send_email_data   = array(
                'from_email'   => $cl['config']['email'],
                'from_name'    => $cl['config']['name'],
                'to_email'     => $user_email,
                'to_name'      => $user_name,
                'subject'      => cl_translate("Confirm registration on - {@name@}", array("name" => $cl['config']['name'])),
                'charSet'      => 'UTF-8',
                'is_html'      => true,
                'message_body' => cl_template('emails/confirm_registration')
            ); 

            if (cl_send_mail($send_email_data)) {
                cl_session('validated_user_data', $user_data_fileds);
                $data['status'] = 401;
            }
        }
    }
}

else if($action == 'confirm_registration') {
    $data['err_code'] = 0;
    $data['status']   = 400;

    if(empty(cl_session('validated_user_data'))) {
        $data['err_code'] = "invalid_user_data";
    }

    else if(empty($_POST['code'])) {
        $data['err_code'] = "invalid_acc_code";
        $data['status']   = 401;
    }

    else {
        $acc_validation_code = fetch_or_get($_POST['code'], "none");
        $validated_user_data = cl_session('validated_user_data');

        if ($acc_validation_code == $validated_user_data['em_code']) {

            $email_code       = sha1(time() + rand(111,999));
            $password_hashed  = password_hash($validated_user_data["password"], PASSWORD_DEFAULT);
            $user_ip          = cl_get_ip();
            $user_ip          = ((filter_var($user_ip, FILTER_VALIDATE_IP) == true) ? $user_ip : '0.0.0.0');
            $insert_data      = array(
                'fname'       => cl_text_secure($validated_user_data["fname"]),
                'lname'       => cl_text_secure($validated_user_data["lname"]),
                'username'    => cl_text_secure($validated_user_data["uname"]),
                'password'    => $password_hashed,
                'email'       => cl_text_secure($validated_user_data["email"]),
                'active'      => '1',
                'em_code'     => $email_code,
                'last_active' => time(),
                'joined'      => time(),
                'ip_address'  => $user_ip,
                'language'    => $cl['config']['language'],
            ); $user_id       =  $db->insert(T_USERS, $insert_data);

            if (is_posnum($user_id)) {
                cl_create_user_session($user_id,'web');
                $data['status'] = 200;

                cl_session_unset('validated_user_data');
            }

            if ($cl['config']['affiliates_system'] == 'on') {

                $ref_id = cl_session('ref_id');

                if (is_posnum($ref_id)) {
                    $ref_udata = cl_raw_user_data($ref_id);

                    if (not_empty($ref_udata)) {
                        cl_update_user_data($ref_id, array(
                            'aff_bonuses' => ($ref_udata['aff_bonuses'] += 1)
                        ));

                        cl_session_unset('ref_id');
                    }
                }
            }
        }
        else {
            $data['err_code'] = "invalid_acc_code";
            $data['status']   = 401;
        }
    }
}

else if ($action == 'resetpass') {
    $data['err_code'] = 0;
    $data['status']   = 400;
    $email_addr       = fetch_or_get($_POST['email'],null);

    if (empty($email_addr)) {
        $data['err_code'] = "invalid_email";
    } 

    else if (filter_var($email_addr, FILTER_VALIDATE_EMAIL) != true) {
        $data['err_code'] = "invalid_email";
    }

    else if (len_between($email_addr,8, 55) != true) {
        $data['err_code'] = "invalid_email";
    }

    else {
        $email          = cl_text_secure($email_addr);
        $db->returnType = "Array";
        $db             = $db->where("email",$email);
        $me             = $db->getOne(T_USERS, array("password", "id", "em_code","fname","lname"));

        if (empty($me)) {
            $data['err_code'] = "unknown_email";
        }

        if (empty($data['err_code'])) { 
            $cl['me']            = $me;
            $user_id             = $me["id"];
            $email_code          = sha1(rand(11111, 99999) . $me["password"]);
            $update              = cl_update_user_data($user_id, array('em_code' => $email_code));
            $cl['me']['em_code'] = $email_code;
            $cl['me']['name']    = cl_strf("%s %s",$me['fname'],$me['lname']);
            $reset_url           = cl_strf("guest?em_code=%s",$email_code);
            $cl['reset_url']     = cl_link($reset_url);
            $send_email_data     = array(
                'from_email'     => $cl['config']['email'],
                'from_name'      => $cl['config']['name'],
                'to_email'       => $email,
                'to_name'        => $cl['me']['name'],
                'subject'        => cl_translate("Reset your password"),
                'charSet'        => 'UTF-8',
                'is_html'        => true,
                'message_body'   => cl_template('emails/reset_password')
            ); 

            if (cl_send_mail($send_email_data)) {
                $data['status'] = 200;
            }
        }
    }
}

else if ($action == 'save_password') {
    $data['err_code']   = 0;
    $data['status']     = 400;
    $user_data_fileds   = array(
        'em_code'       => fetch_or_get($_POST['em_code'],null),
        'password'      => fetch_or_get($_POST['password'],null),
        'conf_pass'     => fetch_or_get($_POST['conf_pass'],null),
    );

    foreach ($user_data_fileds as $field_name => $field_val) {
        if ($field_name == 'em_code') {
            if (empty($field_val) || len($field_val) > 130) {
                $data['err_code'] = "invalid_emcode"; break;
            }

            else if(cl_verify_emcode($field_val) != true) {
                $data['err_code'] = "invalid_emcode"; break;
            }
        } 

        else if ($field_name == 'password') {
            if (empty($field_val) || len_between($field_val,6,20) != true) {
                $data['err_code'] = "invalid_pass"; break;
            }
        }

        else if ($field_name == 'conf_pass') {
            if (empty($field_val)) {
                $data['err_code'] = "invalid_pass"; break;
            }

            else if ($user_data_fileds['password'] != $field_val) {
                $data['err_code'] = "invalid_pass"; break;
            }
        }
    }

    $password    = cl_text_secure($user_data_fileds['password']);
    $c_password  = cl_text_secure($user_data_fileds['conf_pass']);
    $email_code  = cl_text_secure($user_data_fileds['em_code']);
    $passwd_hash = password_hash($password, PASSWORD_DEFAULT);

    if (empty($data['err_code'])) {
        $db->returnType = "Array";
        $db             = $db->where('em_code', $email_code);
        $user_id        = $db->getValue(T_USERS, "id");

        if (is_posnum($user_id)) {
            $data['status'] = 200;
            $email_code     = sha1(time() + rand(1111,9999));
            $update         = cl_update_user_data($user_id, array(
                'password'  => $passwd_hash, 
                'em_code'   => $email_code
            )); 

            cl_create_user_session($user_id);
        }
    }
}