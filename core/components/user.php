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

function cl_get_ip() {
    if (not_empty($_SERVER['HTTP_CLIENT_IP']) && filter_var($_SERVER['HTTP_CLIENT_IP'], FILTER_VALIDATE_IP)) {
        return $_SERVER['HTTP_CLIENT_IP'];
    }
    
    if (not_empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        if (strpos($_SERVER['HTTP_X_FORWARDED_FOR'], ',') !== false) {
            $iplist = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            foreach ($iplist as $ip) {
                if (filter_var($ip, FILTER_VALIDATE_IP)){
                    return $ip;
                }
            }
        } 

        else {
            if (filter_var($_SERVER['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP)){
                return $_SERVER['HTTP_X_FORWARDED_FOR'];
            }
        }
    }

    if (not_empty($_SERVER['HTTP_X_FORWARDED']) && filter_var($_SERVER['HTTP_X_FORWARDED'], FILTER_VALIDATE_IP)) {
        return $_SERVER['HTTP_X_FORWARDED'];
    }
        
    if (not_empty($_SERVER['HTTP_X_CLUSTER_CLIENT_IP']) && filter_var($_SERVER['HTTP_X_CLUSTER_CLIENT_IP'], FILTER_VALIDATE_IP)) {
        return $_SERVER['HTTP_X_CLUSTER_CLIENT_IP'];
    }
        
    if (not_empty($_SERVER['HTTP_FORWARDED_FOR']) && filter_var($_SERVER['HTTP_FORWARDED_FOR'], FILTER_VALIDATE_IP)) {
        return $_SERVER['HTTP_FORWARDED_FOR'];
    }
        
    if (not_empty($_SERVER['HTTP_FORWARDED']) && filter_var($_SERVER['HTTP_FORWARDED'], FILTER_VALIDATE_IP)) {
        return $_SERVER['HTTP_FORWARDED'];
    }
        
    return $_SERVER['REMOTE_ADDR'];
}

function cl_create_user_session($user_id = 0, $platform = 'web') {
    global $db;
    if (empty($user_id)) {
        return false;
    }

    $session_id      = sha1(rand(11111, 99999)) . time() . md5(microtime() . $user_id);
    $insert_data     = array(
        'user_id'    => $user_id,
        'session_id' => $session_id,
        'platform'   => $platform,
        'time'       => time()
    );

    $data_exp = (time() + (10 * 365 * 24 * 60 * 60));
    $insert   = $db->insert(T_SESSIONS, $insert_data);
    
    
    if ($platform == "web") {
        setcookie("user_id", $session_id, $data_exp, '/') or die('unable to create cookie');
    }

    return $session_id;
}

function cl_is_logged() {

    if (isset($_POST['session_id'])) {
        $id = cl_get_userfromsession_id($_POST['session_id'], "mobile");
        if (is_numeric($id) && not_empty($id)) {
            return array(
                "auth"     => true,
                "id"       => $id,
                "token"    => fetch_or_get($_POST['session_id'], 'none'),
                "platform" => "mobile"
            );
        }
        else {
            return array(
                "auth"     => false,
                "token"    => false,
                "platform" => "mobile"
            );
        }
    }

    else if (isset($_GET['session_id'])) {
        $id = cl_get_userfromsession_id($_GET['session_id'], "mobile");
        if (is_numeric($id) && not_empty($id)) {
            return array(
                "auth"     => true,
                "id"       => $id,
                "token"    => fetch_or_get($_GET['session_id'], 'none'),
                "platform" => "mobile"
            );
        }
        else {
            return array(
                "auth"     => false,
                "token"    => false,
                "platform" => "mobile"
            );
        }
    }
    
    else if (isset($_COOKIE['user_id']) && not_empty($_COOKIE['user_id'])) {
        $id = cl_get_userfromsession_id($_COOKIE['user_id'], "web");
        if (is_numeric($id) && not_empty($id)) {
            return array(
                "auth"     => true,
                "id"       => $id,
                "token"    => fetch_or_get($_COOKIE['user_id'], 'none'),
                "platform" => "web"
            );
        }
    }

    else {
        return array(
            "auth"     => false,
            "token"    => false,
            "platform" => "web"
        );
    }
}

function cl_get_userfromsession_id($session_id, $platform = 'web') {
    global $db;
    if (empty($session_id)) {
        return false;
    }
    
    $platform   = cl_text_secure($platform);
    $session_id = cl_text_secure($session_id);
    $return     = $db->where('session_id', $session_id);
    $return     = $db->where('platform', $platform);
    return $db->getValue(T_SESSIONS, 'user_id');
}

function cl_update_user_data($user_id = null,$data = array()) {
    global $db;
    if ((not_num($user_id)) || (empty($data) || is_array($data) != true)) {
        return false;
    } 

    $db     = $db->where('id', $user_id);
    $update = $db->update(T_USERS,$data);
    return ($update == true) ? true : false;
}

function cl_uname_exists($uname = "") {
    global $db;
    return ($db->where('username', cl_text_secure($uname))->getValue(T_USERS, 'count(*)') > 0) ? true : false;
}

function cl_email_exists($email = "") {
    global $db;
    return ($db->where('email', cl_text_secure($email))->getValue(T_USERS, 'count(*)') > 0) ? true : false;
}

function cl_verify_emcode($emcode = "") {
    global $db;
    return ($db->where('em_code', cl_text_secure($emcode))->getValue(T_USERS, 'count(*)') > 0) ? true : false;
}

function cl_user_data($user_id = 0) {
    global $db, $cl;
    if (not_num($user_id)) {
        return false;
    } 

    $db        = $db->where('id', $user_id);
    $user_data = $db->getOne(T_USERS);

    if (empty($user_data)) {
        return false;
    }
  
    $user_data['name']         = cl_strf("%s %s",$user_data['fname'],$user_data['lname']);
    $user_data['about']        = cl_rn_strip($user_data['about']);  
    $user_data['about']        = stripcslashes($user_data['about']);  
    $user_data['about']        = htmlspecialchars_decode($user_data['about'], ENT_QUOTES);   
    $user_data['raw_uname']    = $user_data['username'];
    $user_data['username']     = cl_strf("@%s",$user_data['username']);    
    $user_data['raw_avatar']   = $user_data['avatar'];
    $user_data['raw_cover']    = $user_data['cover'];
    $user_data['avatar']       = cl_get_media($user_data['avatar']);
    $user_data['cover']        = cl_get_media($user_data['cover']);
    $user_data['url']          = cl_link(cl_strf("@%s",$user_data['raw_uname']));
    $user_data['chaturl']      = cl_link(cl_strf("conversation/@%s",$user_data['raw_uname']));
    $user_data['last_active']  = cl_time2str($user_data['last_active']);
    $user_data['joined']       = date("F, Y",$user_data['joined']);
    $user_data['country_a2c']  = fetch_or_get($cl['country_codes'][$user_data['country_id']],'us');
    $user_data['country_name'] = cl_translate($cl['countries'][$user_data['country_id']],'Unknown');

    return $user_data;
}

function cl_raw_user_data($user_id = 0) {
    global $db;
    if (not_num($user_id)) {
        return false;
    } 

    $db        = $db->where('id', $user_id);
    $user_data = $db->getOne(T_USERS);

    if (empty($user_data)) {
        return false;
    }

    return $user_data;
}

function cl_signout_user() {
    global $db;
    if (not_empty($_SESSION['user_id'])) {
        $db->where('session_id', cl_text_secure($_SESSION['user_id']));
        $db->delete(T_SESSIONS);
    }

    if (not_empty($_COOKIE['user_id'])) {
        $db->where('session_id', cl_text_secure($_COOKIE['user_id']));
        $db->delete(T_SESSIONS);
        unset($_COOKIE['user_id']);
        setcookie('user_id', null, -1);
    }

    @session_destroy();

    cl_redirect('/');
}

function cl_signout_user_by_id($user_id = false) {
    global $db;

    if (not_num($user_id)) {
        return false;
    }

    $db = $db->where('user_id', $user_id);
    $qr = $db->delete(T_SESSIONS);

    return $qr;
}

function cl_delete_user_data($user_id = false) {
    global $db;
    if (not_num($user_id)) {
        return false;
    }

    else {
        $db        = $db->where('id', $user_id);
        $user_data = $db->getOne(T_USERS);

        if (cl_queryset($user_data)) {

            /*===== Delete user notifications =====*/
                $db = $db->where('notifier_id', $user_id);
                $qr = $db->delete(T_NOTIFS);

                $db = $db->where('recipient_id', $user_id);
                $qr = $db->delete(T_NOTIFS);
            /*====================================*/

            /*===== Delete user bookmarks =====*/
                $db = $db->where('user_id', $user_id);
                $qr = $db->delete(T_BOOKMARKS);
            /*====================================*/

            /*===== Delete user reports =====*/
                $db = $db->where('user_id', $user_id);
                $qr = $db->delete(T_PROF_REPORTS);
                
                $db = $db->where('profile_id', $user_id);
                $qr = $db->delete(T_PROF_REPORTS);
            /*====================================*/

            /*===== Delete user blocks =====*/
                $db = $db->where('user_id', $user_id);
                $qr = $db->delete(T_BLOCKS);
                
                $db = $db->where('profile_id', $user_id);
                $qr = $db->delete(T_BLOCKS);
            /*====================================*/

            /*===== Delete user aff payouts =====*/
                $db = $db->where('user_id', $user_id);
                $qr = $db->delete(T_AFF_PAYOUTS);
            /*====================================*/

            /*===== Delete user wallet history =====*/
                $db = $db->where('user_id', $user_id);
                $qr = $db->delete(T_WALLET_HISTORY);
            /*====================================*/

            /*===== Delete user ads =====*/
                $db = $db->where('user_id', $user_id);
                $qr = $db->get(T_ADS);

                if (cl_queryset($qr)) {
                    foreach ($qr as $row) {
                        cl_delete_media($row['cover']);
                    }

                    $db = $db->where('user_id', $user_id);
                    $qr = $db->delete(T_ADS);
                }
            /*====================================*/

            /*===== Delete user likes =====*/
                $db = $db->where('user_id', $user_id);
                $qr = $db->get(T_LIKES);

                if (cl_queryset($qr)) {
                    foreach ($qr as $row) {
                        $post_data = cl_raw_post_data($row['pub_id']);

                        if (not_empty($post_data) && ($post_data['user_id'] != $user_id)) {
                            $num = ($post_data['likes_count'] -= 1);
                            $num = (is_posnum($num)) ? $num : 0;
                            cl_update_post_data($row['pub_id'], array(
                                'likes_count' => $num
                            ));
                        }
                    }

                    $db = $db->where('user_id', $user_id);
                    $qr = $db->delete(T_LIKES);
                }
            /*====================================*/

            /*===== Delete user reposts =====*/
                $db = $db->where('user_id', $user_id);
                $db = $db->where('type', 'repost');
                $qr = $db->get(T_POSTS);

                if (cl_queryset($qr)) {
                    foreach ($qr as $row) {
                        $post_data = cl_raw_post_data($row['publication_id']);

                        if (not_empty($post_data) && ($post_data['user_id'] != $user_id)) {
                            $num = ($post_data['reposts_count'] -= 1);
                            $num = (is_posnum($num)) ? $num : 0;
                            cl_update_post_data($row['publication_id'], array(
                                'reposts_count' => $num
                            ));
                        }
                    }

                    $db = $db->where('user_id', $user_id);
                    $db = $db->where('type', 'repost');
                    $qr = $db->delete(T_POSTS);
                }
            /*====================================*/
            
            /*===== Delete user publications =====*/
                $db = $db->where('user_id', $user_id);
                $qr = $db->get(T_PUBS);

                if (cl_queryset($qr)) {
                    foreach ($qr as $row) {
                        if ($row['target'] == 'pub_reply') {
                            cl_update_thread_replys($row['thread_id'], 'minus');
                        }

                        $db = $db->where('publication_id', $row['id']);
                        $qr = $db->delete(T_POSTS);

                        cl_recursive_delete_post($row['id']);
                    }
                }
            /*====================================*/

            /*===== Delete user chats =====*/
                $db = $db->where('user_one', $user_id);
                $qr = $db->delete(T_CHATS);

                $db = $db->where('user_two', $user_id);
                $qr = $db->delete(T_CHATS);

                $db = $db->where('sent_by', $user_id);
                $db = $db->where('sent_to', $user_id, '=', 'OR');
                $qr = $db->get(T_MSGS);

                if (cl_queryset($qr)) {
                    foreach ($qr as $row) {
                        if (not_empty($row['media_file'])) {
                            cl_delete_media($row['media_file']);
                        }
                    }

                    $db = $db->where('sent_by', $user_id);
                    $db = $db->where('sent_to', $user_id, '=', 'OR');
                    $qr = $db->delete(T_MSGS);
                }
            /*====================================*/

            /*===== Delete user connections =====*/
                $db = $db->where('follower_id', $user_id);
                $qr = $db->get(T_CONNECTIONS);

                if (cl_queryset($qr)) {
                    foreach ($qr as $row) {
                        $user_data = cl_raw_user_data($row['following_id']);

                        if (not_empty($user_data)) {
                            $num = ($user_data['followers'] -= 1);
                            $num = (is_posnum($num)) ? $num : 0;
                            cl_update_user_data($user_data['id'], array(
                                'followers' => $num
                            ));
                        }
                    }

                    $db = $db->where('follower_id', $user_id);
                    $qr = $db->delete(T_CONNECTIONS);
                }

                $db = $db->where('following_id', $user_id);
                $qr = $db->get(T_CONNECTIONS);

                if (cl_queryset($qr)) {
                    foreach ($qr as $row) {
                        $user_data = cl_raw_user_data($row['follower_id']);

                        if (not_empty($user_data)) {
                            $num = ($user_data['following'] -= 1);
                            $num = (is_posnum($num)) ? $num : 0;
                            cl_update_user_data($user_data['id'], array(
                                'following' => $num
                            ));
                        }
                    }

                    $db = $db->where('following_id', $user_id);
                    $qr = $db->delete(T_CONNECTIONS);
                }
            /*====================================*/

            $db = $db->where('user_id', $user_id);
            $qr = $db->delete(T_SESSIONS);

            $db = $db->where('id', $user_id);
            $qr = $db->delete(T_USERS);

            return true;
        }

        else {
            return false;
        }
    }
}

function cl_get_user_by_name($uname = null) {
    global $cl,$db;

    if (empty($uname)) {
        return false;
    }

    $db    = $db->where('username',$uname);
    $udata = $db->getOne(T_USERS, 'id');

    if (cl_queryset($udata)) {
        $user_id = intval($udata['id']);
        $udata   = cl_user_data($user_id);
    }

    return $udata;
}

function cl_get_user_id_by_name($uname = null) {
    global $cl,$db;

    if (empty($uname)) {
        return false;
    }

    $db      = $db->where('username', $uname);
    $udata   = $db->getOne(T_USERS, 'id');
    $user_id = 0;

    if (cl_queryset($udata)) {
        $user_id = intval($udata['id']);
    }

    return $user_id;
}

function cl_get_follow_suggestions($limit = 10, $offset = false) {
    global $db, $cl, $me;

    $data          = array();
    $user_id       = ((not_empty($cl['is_logged'])) ? $me['id'] : false);
    $sql           = cl_sqltepmlate('components/sql/user/fetch_follow_suggestions',array(
        't_users'  => T_USERS,
        't_conns'  => T_CONNECTIONS,
        't_blocks' => T_BLOCKS,
        'user_id'  => $user_id,
        'limit'    => $limit,
        'offset'   => $offset
    ));

    $query_result = $db->rawQuery($sql);

    if (cl_queryset($query_result)) {
        foreach ($query_result as $row) {
            $row['about']       = cl_rn_strip($row['about']);
            $row['about']       = stripslashes($row['about']);
            $row['name']        = cl_strf("%s %s",$row['fname'],$row['lname']);      
            $row['avatar']      = cl_get_media($row['avatar']);
            $row['url']         = cl_link(cl_strf("@%s",$row['username']));
            $row['username']    = cl_strf("@%s",$row['username']);
            $row['last_active'] = date("d M, y h:m A",$row['last_active']);
            $data[]             = $row;
        }
    }

    return $data;
}

function cl_is_following($follower_id = false, $following_id = false) {
    global $db;

    if (is_posnum($follower_id) != true || is_posnum($following_id) != true) {
        return false;
    }

    else if($follower_id == $following_id) {
        return false;
    }

    $db  = $db->where('follower_id', $follower_id);
    $db  = $db->where('following_id', $following_id);
    $res = $db->getValue(T_CONNECTIONS,'COUNT(id)');
    
    return is_posnum($res);
}

function cl_is_blocked($user_id = false, $profile_id = false) {
    global $db;

    if (is_posnum($user_id) != true || is_posnum($profile_id) != true) {
        return false;
    }

    else if($user_id == $profile_id) {
        return false;
    }

    $db  = $db->where('user_id', $user_id);
    $db  = $db->where('profile_id', $profile_id);
    $res = $db->getValue(T_BLOCKS, 'COUNT(id)');
    
    return is_posnum($res);
}

function cl_unfollow($follower_id = false, $following_id = false){
    global $db;

    if (is_posnum($follower_id) != true || is_posnum($following_id) != true) {
        return false;
    }

    $db = $db->where('follower_id', $follower_id);
    $db = $db->where('following_id', $following_id);
    $rm = $db->delete(T_CONNECTIONS);
    
    return $rm;
}

function cl_follow($follower_id = false, $following_id = false){
    global $db;

    if (is_posnum($follower_id) != true || is_posnum($following_id) != true) {
        return false;
    }

    $insert_id         =  $db->insert(T_CONNECTIONS,array(
        'follower_id'  => $follower_id,
        'following_id' => $following_id,
        'time'         => time()
    ));

    return $insert_id;
}

function cl_get_followers($user_id = false, $limit = 10, $offset = false) {
    global $db, $cl;

    if (is_posnum($user_id) != true) {
        return false;
    }

    $data         = array();
    $sql          = cl_sqltepmlate('components/sql/user/fetch_followers',array(
        't_users' => T_USERS,
        't_conns' => T_CONNECTIONS,
        'user_id' => $user_id,
        'limit'   => $limit,
        'offset'  => $offset,
    ));

    $query_result = $db->rawQuery($sql);

    if (cl_queryset($query_result)) {
        foreach ($query_result as $row) {
            $row['about']        = cl_rn_strip($row['about']);
            $row['about']        = stripslashes($row['about']);
            $row['name']         = cl_strf("%s %s",$row['fname'],$row['lname']);      
            $row['avatar']       = cl_get_media($row['avatar']);
            $row['url']          = cl_link(cl_strf("@%s",$row['username']));
            $row['username']     = cl_strf("@%s",$row['username']);
            $row['last_active']  = date("d M, y h:m A",$row['last_active']);
            $row['is_following'] = false;
            $row['is_user']      = false;

            if (not_empty($cl['is_logged'])) {
                $row['is_following'] = cl_is_following($cl['me']['id'], $row['id']);

                if ($cl['me']['id'] == $row['id']) {
                    $row['is_user'] = true; 
                }
            }

            $data[] = $row;
        }
    }

    return $data;
}

function cl_get_followings($user_id = false, $limit = 10, $offset = false) {
    global $db,$cl;

    if (is_posnum($user_id) != true) {
        return false;
    }

    $data         =  array();
    $sql          =  cl_sqltepmlate('components/sql/user/fetch_followings',array(
        't_users' => T_USERS,
        't_conns' => T_CONNECTIONS,
        'user_id' => $user_id,
        'limit'   => $limit,
        'offset'  => $offset,
    ));

    $query_result = $db->rawQuery($sql);

    if (cl_queryset($query_result)) {
        foreach ($query_result as $row) {
            $row['about']        = cl_rn_strip($row['about']);
            $row['about']        = stripslashes($row['about']);
            $row['name']         = cl_strf("%s %s",$row['fname'],$row['lname']);      
            $row['avatar']       = cl_get_media($row['avatar']);
            $row['url']          = cl_link(cl_strf("@%s",$row['username']));
            $row['username']     = cl_strf("@%s",$row['username']);
            $row['last_active']  = date("d M, y h:m A",$row['last_active']);
            $row['is_following'] = false;
            $row['is_user']      = false;

            if (not_empty($cl['is_logged'])) {
                $row['is_following'] = cl_is_following($cl['me']['id'], $row['id']);

                if ($cl['me']['id'] == $row['id']) {
                    $row['is_user'] = true; 
                }
            }

            $data[] = $row;
        }
    }

    return $data;
}

function cl_notify_user($data = array()) {
    global $db, $cl, $me;

    if (empty($data)) {
        return false;
    }

    $db = $db->where('notifier_id', $me['id']);
    $db = $db->where('recipient_id', $data['user_id']);
    $db = $db->where('subject', $data['subject']);
    $db = $db->where('entry_id', $data['entry_id']);
    $qr = $db->getValue(T_NOTIFS,'COUNT(*)');

    if (empty($qr)) {
        $db->insert(T_NOTIFS, array(
            'notifier_id'   => $me['id'],
            'recipient_id'  => $data['user_id'],
            'status'        => '0',
            'entry_id'      => $data['entry_id'],
            'subject'       => $data['subject'],
            'time'          => time()
        ));
    }
    else {
        $db = $db->where('notifier_id', $me['id']);
        $db = $db->where('recipient_id', $data['user_id']);
        $db = $db->where('subject', $data['subject']);
        $db = $db->where('entry_id', $data['entry_id']);
        $qr = $db->update(T_NOTIFS, array(
            'time'   => time(),
            'status' => '0',
        ));
    }
}

function cl_get_user_mentions($text = "") {
    if (empty($text) || is_string($text) != true) {
        return array();
    }

    $users = array();

    preg_match_all('/(?:^|\s|,)\B@([a-zA-Z0-9_]{4,32})/is', $text, $mentions);
    
    if (is_array($mentions) && not_empty($mentions[1])) {
        $users = $mentions[1];
    }

    return $users;
}

function cl_notify_mentioned_users($users = array(), $post_id = false) {
    global $db, $cl, $me;

    if (empty($cl['is_logged']) || is_posnum($post_id) != true) {
        return false;
    }

    foreach ($users as $username) {
        $uid = cl_get_user_id_by_name($username);

        if ($uid && ($uid != $me['id'])) {
            cl_notify_user(array(
                'subject'  => 'mention',
                'user_id'  => $uid,
                'entry_id' => $post_id,
            ));
        }
    }
}

function cl_likify_mentions($text = "") {
    $text = preg_replace_callback('/(?:^|\s|,)\B@([a-zA-Z0-9_]{4,32})/is', function($m) {

        $uname = fetch_or_get($m[1]);

        if (not_empty($uname) && cl_get_user_id_by_name($uname)) {
            return cl_html_el('a', cl_strf("@%s",$uname), array(
                'href'   => cl_link(cl_strf("@%s",$uname)),
                'target' => '_blank',
                'class'  => 'inline-link',
            ));
        }
        else{
            return cl_strf("@%s",$uname);
        }

    }, $text);

    return $text;
}

function cl_total_new_notifs() {
    global $db, $cl;

    if (empty($cl['is_logged'])) {
        return 0;
    }

    $sql           = cl_sqltepmlate('apps/notifications/sql/fetch_total', array(
        't_notifs' => T_NOTIFS,
        't_blocks' => T_BLOCKS,
        'user_id'  => $cl['me']['id']
    ));

    $qr = $db->rawQueryOne($sql);

    if (cl_queryset($qr)) {
        return fetch_or_get($qr['total'], 0);
    }

    return 0;
}

function cl_total_new_messages() {
    global $db, $cl;

    if (empty($cl['is_logged'])) {
        return 0;
    }

    $sql           = cl_sqltepmlate('apps/chat/sql/fetch_total', array(
        't_msgs'   => T_MSGS,
        't_blocks' => T_BLOCKS,
        'user_id'  => $cl['me']['id']
    ));

    $qr = $db->rawQueryOne($sql);

    if (cl_queryset($qr)) {
        return fetch_or_get($qr['total'], 0);
    }

    return 0;
}

function cl_get_blocked_user_ids($user_id = false) {
    global $db, $cl;

    if (not_num($user_id)) {
        return array();
    }

    else {
        $db    = $db->where('user_id', $user_id);
        $users = $db->get(T_BLOCKS, null, array('profile_id'));
        $data  = array();

        if (cl_queryset($users)) {
            foreach ($users as $row) {
                $data[] = $row['profile_id'];
            }
        }

        return $data;
    }
}

function cl_get_blocked_users() {
    global $db, $cl;

    $data          = array();
    $sql           = cl_sqltepmlate('components/sql/user/fetch_blocked_users', array(
        't_users'  => T_USERS,
        't_blocks' => T_BLOCKS,
        'user_id'  => $cl['me']['id']
    ));

    $users = $db->rawQuery($sql);

    if (cl_queryset($users)) {
        foreach ($users as $row) {
            $row['name']     = cl_strf("%s %s", $row['fname'], $row['lname']);      
            $row['avatar']   = cl_get_media($row['avatar']);
            $row['url']      = cl_link(cl_strf("@%s",$row['username']));
            $row['username'] = cl_strf("@%s",$row['username']);
            $data[]          = $row;
        }
    }

    return $data;
}

function cl_calc_affiliate_bonuses() {
    global $cl;

    if (empty($cl['is_logged'])) {
        return "0.00";
    }

    else {
        $money      = "0.00";
        $bonus_rate = $cl['config']['aff_bonus_rate'];

        if (not_empty($cl['me']['aff_bonuses'])) {
            $money = ($bonus_rate * $cl['me']['aff_bonuses']);
        }

        return $money;
    }
}

function cl_aff_request_exists() {
    global $db, $cl;

    $db = $db->where('user_id', $cl['me']['id']);
    $db = $db->where('status', 'pending');
    $qr = $db->getValue(T_AFF_PAYOUTS, 'COUNT(*)');

    return ($qr > 0);
}