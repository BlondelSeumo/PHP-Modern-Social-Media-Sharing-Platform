<?php 
# @*************************************************************************@
# @ @author Mansur Altamirov (Mansur_TL)                                    @
# @ @author_url 1: https://www.instagram.com/mansur_tl                      @
# @ @author_url 2: http://codecanyon.net/user/mansur_tl                     @
# @ @author_email: highexpresstore@gmail.com                                @
# @*************************************************************************@
# @ ColibriSM - The Ultimate Modern Social Media Sharing Platform           @
# @ Copyright (c) 21.03.2020 ColibriSM. All rights reserved.                @
# @*************************************************************************@

if (empty($cl["is_logged"])) {
    $data['status'] = 400;
    $data['error']  = 'Invalid access token';
}

else if ($action == "save_profile_name") {
    $data['err_code'] =  0;
    $data['status']   =  400;
    $user_data_fields =  array(
        'fname'       => fetch_or_get($_POST['fname'],null),
        'lname'       => fetch_or_get($_POST['lname'],null),
        'uname'       => fetch_or_get($_POST['uname'],null)
    );

    foreach ($user_data_fields as $field_name => $field_val) {
        if ($field_name == 'uname') {
            if (empty($field_val)) {
                $data['err_code'] = "invalid_uname"; break;
            }

            else if (len_between($field_val,3, 25) != true) {
                $data['err_code'] = "invalid_uname"; break;
            }

            else if (preg_match('/^[\w]+$/', $field_val) != true) {
                $data['err_code'] = "invalid_uname"; break;
            }

            else if(cl_uname_exists($field_val) && $field_val != $me['raw_uname']) {
                $data['err_code'] = "doubling_uname"; break;
            }
        }

        else if ($field_name == 'fname') {
            if (empty($field_val) || len_between($field_val,3,25) != true) {
                $data['err_code'] = "invalid_fname"; break;
            }
        }

        else if ($field_name == 'lname') {
            if (empty($field_val) || len_between($field_val,3,25) != true) {
                $data['err_code'] = "invalid_lname"; break;
            }
        }
    }

    if (empty($data['err_code'])) {
        $fname          = cl_text_secure($user_data_fields['fname']);
        $lname          = cl_text_secure($user_data_fields['lname']);
        $uname          = cl_text_secure($user_data_fields['uname']);
        $data['status'] = 200;

        cl_update_user_data($me["id"], array(
            'fname'    => $fname,
            'lname'    => $lname,
            'username' => $uname,
        ));

        if ($uname != $me['raw_uname']) {
            cl_update_user_data($me["id"], array(
                'verified' => '0'
            ));
        }
    }
}

else if ($action == "save_profile_email") {
    $data['err_code'] = 0;
    $data['status']   = 400;
    $email            = fetch_or_get($_POST['email'],null);

    if (empty($email)) {
        $data['err_code'] = "invalid_email";
    }

    else if (filter_var($email, FILTER_VALIDATE_EMAIL) != true || len($email) > 55) {
        $data['err_code'] = "invalid_email";
    }

    else if (cl_email_exists($email) && ($email != $me['email'])) {
        $data['err_code'] = "doubling_email";
    }

    else {
        $email          = cl_text_secure($email);
        $data['status'] = 200;

        if ($email != $me['email']) {
            cl_update_user_data($me["id"], array(
                'email' => $email
            ));
        }
    }
}

else if ($action == "save_profile_url") {
    $data['err_code'] = 0;
    $data['status']   = 400;
    $website          = fetch_or_get($_POST['url'],null);

    if (not_empty($website)) {
        if (is_url($website) != true || len($website) > 115) {
            $data['err_code'] = "invalid_url";
        }

        else {
            $website        = cl_text_secure($website);
            $data['status'] = 200;

            if ($website != $me['website']) {
                cl_update_user_data($me["id"], array(
                    'website' => $website
                ));
            }
        }
    }
    else {
        $data['status'] = 200;
        cl_update_user_data($me["id"], array(
            'website' => ""
        )); 
    }
}

else if ($action == "save_profile_bio") {
    $data['err_code'] = 0;
    $data['status']   = 400;
    $user_bio         = fetch_or_get($_POST['bio'],null);

    if (not_empty($user_bio)) {
        if (len($user_bio) > 140) {
            $data['err_code'] = "invalid_bio";
        }

        else {
            $user_bio       = cl_text_secure($user_bio);
            $data['status'] = 200;

            if ($user_bio != $me['about']) {  
                cl_update_user_data($me["id"], array(
                    'about' => $user_bio
                ));
            }
        }
    }
    else {
        $data['status'] = 200;
        cl_update_user_data($me["id"], array(
            'about' => ""
        )); 
    }
}

else if ($action == "save_profile_gender") {
    $data['err_code'] = 0;
    $data['status']   = 400;
    $gender           = fetch_or_get($_POST['gender'],null);

    if (not_empty($gender) && array($gender, array('M', 'F'))) {
        cl_update_user_data($me["id"], array(
            'gender' => $gender
        ));

        $data['status'] = 200;
    }
}

else if ($action == "save_privacy_settings") {
    $data['err_code'] = 0;
    $data['status']   = 400;
    $profile_privacy  = fetch_or_get($_POST['profile_privacy'],null);
    $contact_privacy  = fetch_or_get($_POST['contact_privacy'],null);
    $index_privacy    = fetch_or_get($_POST['index_privacy'],null);

    if (in_array($profile_privacy, array('everyone','followers', 'nobody')) != true) {
        $data['err_code'] = "invalid_profile_privacy";
    }

    else if (in_array($contact_privacy, array('everyone','followed')) != true) {
        $data['err_code'] = "invalid_contact_privacy";
    }

    else if (in_array($index_privacy, array('Y','N')) != true) {
        $data['err_code'] = "invalid_index_privacy";
    }

    else {
        cl_update_user_data($me["id"], array(
            'profile_privacy' => $profile_privacy,
            'contact_privacy' => $contact_privacy,
            'index_privacy' => $index_privacy,
        ));

        $data['status'] = 200;
    }
}

else if ($action == 'save_profile_pass') {
    $data['status']     =  400;
    $data['err_code']   =  null;
    $user_data_fields   =  array(
        'curr_password' => fetch_or_get($_POST['curr_password'],null),
        'new_password'  => fetch_or_get($_POST['new_password'],null),
        'new_conf_pass' => fetch_or_get($_POST['new_conf_pass'],null),
    );

    foreach ($user_data_fields as $field_name => $field_val) {
        if ($field_name == 'curr_password') {
            if (empty($field_val) || (password_verify($field_val, $me['password']) != true)) {
                $data['err_code'] = "invalid_curr_pass"; break;
            }
        }

        else if ($field_name == 'new_password') {
            if (empty($field_val) || len_between($field_val,6,20) != true) {
                $data['err_code'] = "invalid_password"; break;
            }
        }

        else if($field_name == 'new_conf_pass') {
            if (empty($field_val) || ($field_val != $user_data_fields['new_password'])) {
                $data['err_code'] = "invalid_password"; break;
            }
        }
    }

    if (empty($data['err_code'])) {
        $data['status'] =  200;
        $user_id        =  $me['id'];
        $update_data    =  array(
            'password'  => password_hash(cl_text_secure($user_data_fields['new_password']), PASSWORD_DEFAULT),
        ); 

        cl_update_user_data($user_id, $update_data);
    }
}

else if ($action == "save_profile_lang") {
    $data['err_code'] = 0;
    $data['status']   = 400;
    $prof_lang        = fetch_or_get($_POST['language'],null);
    $langs_list       = array_keys($cl["languages"]);

    if (empty($prof_lang) || (in_array($prof_lang,  $langs_list) != true)) {
        $data['err_code'] = "invalid_lang";
    }

    else {
        $data['status'] = 200;

        if ($prof_lang != $me['language']) {
            cl_update_user_data($me["id"], array(
                'language' => $prof_lang
            ));
        }
    }
}

else if ($action == "save_profile_country") {
    $data['err_code'] = 0;
    $data['status']   = 400;
    $prof_country     = fetch_or_get($_POST['country'],null);
    $country_list     = array_keys($cl["countries"]);

    if (not_num($prof_country) || (in_array($prof_country, $country_list) != true)) {
        $data['err_code'] = "invalid_country";
    }

    else {
        $data['status'] = 200;

        if ($prof_country != $me['country_id']) {
            cl_update_user_data($me["id"], array(
                'country_id' => $prof_country
            ));
        }
    }
}

else if ($action == 'delete_account') {
    $data['status']   = 400;
    $data['err_code'] = null;
    $curr_password    = fetch_or_get($_POST['password'],null);

    if (empty($curr_password) || (password_verify($curr_password, $me['password']) != true)) {
        $data['err_code'] = "invalid_pass";
    }

    else {
        $data['status'] = 200;

        cl_delete_user_data($me['id']);
    }
}

else if ($action == 'upload_profile_avatar') {
    if (not_empty($_FILES['avatar']) && not_empty($_FILES['avatar']['tmp_name'])) {
        $file_info      =  array(
            'file'      => $_FILES['avatar']['tmp_name'],
            'size'      => $_FILES['avatar']['size'],
            'name'      => $_FILES['avatar']['name'],
            'type'      => $_FILES['avatar']['type'],
            'file_type' => 'thumbnail',
            'folder'    => 'avatars',
            'slug'      => 'avatar',
            'crop'      => array('width' => 120, 'height' => 120),
            'allowed'   => 'jpg,png,jpeg,gif'
        );

        $file_upload = cl_upload($file_info);

        if (not_empty($file_upload['cropped'])) {
            $data['status'] = 200;
            $data['url']    = cl_get_media($file_upload['cropped']);

            cl_delete_media($file_upload['filename']);
            cl_delete_media($me['raw_avatar']);

            cl_update_user_data($me['id'], array(
                'avatar' => $file_upload['cropped']
            ));
        } 

        else{
            $data['err_code'] = "invalid_req_data";
            $data['status']   = 400;
        }
    }
}

else if ($action == 'upload_profile_cover') {
    if (not_empty($_FILES['cover']) && not_empty($_FILES['cover']['tmp_name'])) {
        $file_info      =  array(
            'file'      => $_FILES['cover']['tmp_name'],
            'size'      => $_FILES['cover']['size'],
            'name'      => $_FILES['cover']['name'],
            'type'      => $_FILES['cover']['type'],
            'file_type' => 'image',
            'folder'    => 'covers',
            'slug'      => 'cover',
            'allowed'   => 'jpg,png,jpeg,gif'
        );

        $file_upload = cl_upload($file_info);

        if (not_empty($file_upload['filename'])) {
            try {
                require_once(cl_full_path("core/libs/PHPgumlet/ImageResize.php"));
                require_once(cl_full_path("core/libs/PHPgumlet/ImageResizeException.php"));

                $prof_cover = new \Gumlet\ImageResize(cl_full_path($file_upload['filename']));
                $sw         = $prof_cover->getSourceWidth();
                $sh         = $prof_cover->getSourceHeight();
                $data['sw'] = $sw;
                $data['sh'] = $sh;

                if ($sw != 600) {
                    $prof_cover->resize(600,(($sh * 600) / $sw),true);
                    $prof_cover->save(cl_full_path($file_upload['filename']));
                }

                $path_info      = explode(".", $file_upload['filename']);
                $filepath       = fetch_or_get($path_info[0],"");
                $file_ext       = fetch_or_get($path_info[1],"");
                $cropped_cover  = cl_strf("%s_600x200.%s",$filepath,$file_ext);
                $data['status'] = 200;

                $prof_cover->crop(600, 200, true);
                $prof_cover->save(cl_full_path($cropped_cover));

                cl_delete_media($me['raw_cover']);
                cl_delete_media($me['cover_orig']);

                cl_update_user_data($me['id'], array(
                    'cover' => $cropped_cover,
                    'cover_orig' => $file_upload['filename']
                ));
            } 

            catch (Exception $e) {
                $data['err_code']    = "invalid_req_data";
                $data['err_message'] = $e->getMessage();
                $data['status']      = 400;
            }
        } 

        else{
            $data['err_code'] = "invalid_req_data";
            $data['status']   = 400;
        }
    }
}

else if($action == "save_profcover_rep") {
    $data['err_code'] = 0;
    $data['status']   = 400;
    $new_position     = fetch_or_get($_POST['position'],0);
    $dw               = 600;
    $dh               = 200;

    if (is_numeric($new_position)) {
        try {
            require_once(cl_full_path("core/libs/PHPgumlet/ImageResize.php"));
            require_once(cl_full_path("core/libs/PHPgumlet/ImageResizeException.php"));

            $prof_cover     = new \Gumlet\ImageResize(cl_full_path($me['cover_orig']));
            $data['status'] = 200;
            $file_ext       = explode('.', $me['raw_cover']);
            $file_ext       = end($file_ext);
            $file_ext       = (empty($file_ext)) ? 'jpg' : $file_ext;
            $filename       =  cl_gen_path(array(
                'file_ext'  => $file_ext,
                'file_type' => 'image',
                'folder'    => 'covers',
                'slug'      => 'cover',
            ));

            $prof_cover->freecrop($dw, $dh, 0, -$new_position);
            $prof_cover->save(cl_full_path($filename));
            
            cl_delete_media($me['raw_cover']);

            cl_update_user_data($me['id'], array('cover' => $filename));
        } 

        catch (Exception $e) {
            $data['err_code']    = "invalid_req_data";
            $data['err_message'] = $e->getMessage();
            $data['status']      = 400;
        }
    }
}

else if($action == 'verify_account') {
    $data['status']   = 400;
    $data['err_code'] = 0;

    if ($me['verified'] == '2') {
        $data['err_code'] = "duplicate_request_error";
    }

    else if (empty($_POST['full_name']) || len_between($_POST['full_name'], 3, 60) != true) {
        $data['err_code'] = "invalid_full_name";
    }

    else if (empty($_POST['text_message']) || len_between($_POST['text_message'], 1, 1200) != true) {
        $data['err_code'] = "invalid_text_message";
    }

    else if(empty($_FILES['video']) || empty($_FILES['video']['tmp_name'])) {
        $data['err_code'] = "invalid_video_message";
    }

    else {
        $file_info      = array(
            'file'      => $_FILES['video']['tmp_name'],
            'size'      => $_FILES['video']['size'],
            'name'      => $_FILES['video']['name'],
            'type'      => $_FILES['video']['type'],
            'file_type' => 'video',
            'folder'    => 'videos',
            'slug'      => 'video_message',
            'allowed'   => 'mp4,mov,3gp,webm',
        );

        $file_upload = cl_upload($file_info);

        if (not_empty($file_upload['filename'])) {
            $full_name          = cl_text_secure($_POST['full_name']);
            $text_message       = cl_text_secure($_POST['text_message']);
            $insert_data        = array(
                'user_id'       => $me['id'],
                'full_name'     => $full_name,
                'text_message'  => $text_message,
                'video_message' => $file_upload['filename'],
                'time'          => time(),
            );

            $req_id = $db->insert(T_VERIFICATIONS,$insert_data);

            if (is_posnum($req_id)) {
                $data['err_code'] = 0;
                $data['status']   = 200;

                cl_update_user_data($me['id'], array(
                    'verified' => '2'
                ));
            }
        }
    }
}

else if($action == 'affiliate_payout_req') {
    $data['status']   = 400;
    $data['err_code'] = 0;
    $curr_aff_balance = cl_calc_affiliate_bonuses();
    $payout_amount    = fetch_or_get($_POST['amount'], false);
    $payout_email     = fetch_or_get($_POST['paypal'], false);

    if (empty($payout_amount) || not_num($payout_amount) || ($payout_amount > $curr_aff_balance)) {
        $data['status'] = "invalid_payment_amount";
    }

    else if(empty($payout_email) || filter_var($payout_email, FILTER_VALIDATE_EMAIL) != true) {
        $data['status'] = "invalid_payment_email";
    }

    else if(cl_aff_request_exists()) {
        $data['status'] = "invalid_req_data";
    }

    else {

        $insert_data  = array(
            'user_id' => $me['id'],
            'amount'  => $payout_amount,
            'email'   => $payout_email,
            'status'  => 'pending',
            'bonuses' => $me['aff_bonuses'],
            'time'    => time()
        );

        $insert_id = $db->insert(T_AFF_PAYOUTS, $insert_data);

        if (is_posnum($insert_id)) {
            $data['status'] = 200;
        }
    }
}

else if($action == 'topup_wallet') {
    $data['status']   = 400;
    $data['err_code'] = 0;
    $topup_amount     = fetch_or_get($_POST['amount'], false);

    if (empty($topup_amount) || is_numeric($topup_amount) != true) {
        $data['err_code'] = 'invalid_topup_amount';
    }

    else if ($topup_amount < 50 || $topup_amount > 15000) {
        $data['err_code'] = 'invalid_topup_amount';
    }

    else {
        try {
            require_once("core/libs/configs/paypal.php");

            $currency       = strtoupper($cl['config']['site_currency']);
            $payer          = new \PayPal\Api\Payer();
            $itemList       = new \PayPal\Api\ItemList();
            $details        = new \PayPal\Api\Details();
            $amount         = new \PayPal\Api\Amount();
            $transaction    = new \PayPal\Api\Transaction();
            $redirectUrls   = new \PayPal\Api\RedirectUrls();
            $payment        = new \PayPal\Api\Payment();
            $line_item      = new \PayPal\Api\Item();
            $inputFields    = new \PayPal\Api\InputFields();
            $webProfile     = new \PayPal\Api\WebProfile();

            $payer          = $payer->setPaymentMethod('paypal');
            $subtotal       = $topup_amount;
            $url_success    = cl_link("native_api/settings/tup_wallet_success");
            $url_cancel     = cl_link("native_api/settings/tup_wallet_cancel");

            $inputFields    = $inputFields->setAllowNote(true);
            $inputFields    = $inputFields->setNoShipping(1);
            $inputFields    = $inputFields->setAddressOverride(0);
            
            $webProfile     = $webProfile->setName(uniqid());
            $webProfile     = $webProfile->setInputFields($inputFields);
            $webProfile     = $webProfile->setTemporary(true);

            $createProfile  = $webProfile->create($paypal);
            $profileID      = $createProfile->getId();
            $payment        = $payment->setExperienceProfileId($profileID); 

            #Set redirect URLS
            $redirectUrls   = $redirectUrls->setReturnUrl($url_success);
            $redirectUrls   = $redirectUrls->setCancelUrl($url_cancel); 

            $line_item      = $line_item->setName(cl_translate('Top up your account balance'));
            $line_item      = $line_item->setQuantity(1);
            $line_item      = $line_item->setPrice($topup_amount);
            $line_item      = $line_item->setCurrency($currency);

            #Set items to be purchased
            $itemList       = $itemList->setItems(array($line_item)); 
            $details        = $details->setSubtotal($subtotal);

            #Set amount
            $amount         = $amount->setCurrency($currency);
            $amount         = $amount->setTotal($subtotal);
            $amount         = $amount->setDetails($details);

            $transaction    = $transaction->setAmount($amount);
            $transaction    = $transaction->setItemList($itemList);
            $transaction    = $transaction->setDescription(cl_translate('Pay to: {@site_name@}', array('site_name' => $cl['config']['name'])));
            $transaction    = $transaction->setInvoiceNumber(time());
            $payment        = $payment->setIntent('sale');
            $payment        = $payment->setPayer($payer);
            $payment        = $payment->setRedirectUrls($redirectUrls);
            $payment        = $payment->setTransactions(array($transaction));
            $payment        = $payment->create($paypal);
            $data['url']    = $payment->getApprovalLink();
            $data['status'] = 200;
            
            cl_session('tup_amount', $topup_amount);
        }

        catch (Exception $ex) {
            $data['status']  = 500;
            $data['message'] = $ex->getMessage();
        }
    }
}

else if($action == 'tup_wallet_success') {
    if (not_empty($_GET['paymentId']) && not_empty($_GET['token']) && not_empty($_GET['PayerID'])) {

        try{

            require_once("core/libs/configs/paypal.php");

            $paym_id    = fetch_or_get($_GET['paymentId'], false);
            $paym_tok   = fetch_or_get($_GET['token'], false);
            $payer_id   = fetch_or_get($_GET['PayerID'], false);
            $payment    = \PayPal\Api\Payment::get($paym_id, $paypal);
            $execute    = new \PayPal\Api\PaymentExecution();
            $execute    = $execute->setPayerId($payer_id);
            $tup_amount = cl_session('tup_amount');

            if ($tup_amount) {
                $result = $payment->execute($execute, $paypal);
                
                cl_update_user_data($me['id'], array(
                    'wallet' => ($me['wallet'] += $tup_amount)
                ));

                cl_db_insert(T_WALLET_HISTORY, array(
                    'user_id'   => $me['id'],
                    'operation' => 'paypal_wallet_tup',
                    'amount'    => $tup_amount,
                    'json_data' => json(array(
                        'paypal_pid' => $result->id
                    ), true),
                    'time' => time()
                ));

                cl_session_unset('tup_amount');

                cl_redirect('wallet');
            }
            else {
                throw new Exception('The current payment is duplicated of already processed payment. Please check your details');
            }
        }

        catch (Exception $e) {
            cl_session_unset('tup_amount');

            cl_session('err500_message', array(
                'title' => "Transaction failed!",
                'desc' => $e->getMessage()
            ));

            cl_redirect('500');
        }
    }
}

else if($action == 'tup_wallet_cancel') {
    cl_session_unset('tup_amount');
    cl_redirect('wallet');
}