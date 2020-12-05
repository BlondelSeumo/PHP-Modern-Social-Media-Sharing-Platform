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

ini_set('display_errors', 1);
ini_set('display_startup_errors',1);
error_reporting(E_ALL);

require_once("settings.php");
require_once("definitions.php");
require_once("components/tools.php");
require_once("components/shortcuts.php");
require_once("components/compilers.php");
require_once("components/localization.php");
require_once("components/glob_context.php");
require_once("components/user.php");
require_once("components/post.php");
require_once("components/ad.php");
require_once("libs/DB/vendor/autoload.php");

$sql_db_host   = (isset($sql_db_host) ? $sql_db_host : "");
$sql_db_user   = (isset($sql_db_user) ? $sql_db_user : "");
$sql_db_pass   = (isset($sql_db_pass) ? $sql_db_pass : "");
$sql_db_name   = (isset($sql_db_name) ? $sql_db_name : "");
$site_url      = (isset($site_url)    ? $site_url    : "");

$mysqli        = new mysqli($sql_db_host, $sql_db_user, $sql_db_pass, $sql_db_name);
$server_errors = array();

if (mysqli_connect_errno()) {
    $server_errors[] = mysqli_connect_error();
}

if (not_empty($server_errors)) {
    foreach ($server_errors as $serv_error) {
        echo cl_html_el("h3", $serv_error);
    }

    die();
}

session_start();

$db_connection          = $mysqli;
$query                  = $mysqli->query("SET NAMES utf8");
$set_charset            = $mysqli->set_charset('utf8mb4');
$set_charset            = $mysqli->query("SET collation_connection = utf8mb4_unicode_ci");
$db                     = new MysqliDb($mysqli);
$url                    = $site_url;
$config                 = cl_get_configurations();
$config["url"]          = $url;
$config["theme_url"]    = cl_strf("%s/themes/%s",$url,$config["theme"]);
$config["site_logo"]    = cl_strf("%s/%s",$config["theme_url"],$config["site_logo"]);
$config["site_favicon"] = cl_strf("%s/%s",$config["theme_url"],$config["site_favicon"]);
$display_lang           = cl_session('lang');
$cl["language"]         = (not_empty($display_lang)) ? $display_lang : $config["language"];
$cl["is_logged"]        = false;
$cl["is_admin"]         = false;
$cl["config"]           = $config;
$cl["server_mode"]      = 'production';
$me                     = array();
$langs                  = cl_get_langs($cl["language"]);
$cl['csrf_token']       = cl_generate_csrf_token(); 
$cl['ref_url']          = http_referer();
$cl['auth_status']      = cl_is_logged();
$cl["languages"]        = array(
    "english"           => cl_translate("English"),
    "french"            => cl_translate("French"),
    "german"            => cl_translate("German"),
    "italian"           => cl_translate("Italian"),
    "russian"           => cl_translate("Russian"),
    "portuguese"        => cl_translate("Portuguese"),
    "spanish"           => cl_translate("Spanish"),
    "turkish"           => cl_translate("Turkish"),
    "dutch"             => cl_translate("Dutch"),
    "ukraine"           => cl_translate("Ukraine"),
);

if (not_empty($cl['auth_status']['auth'])) {
    
    $cl['hash_session'] = $cl['auth_status']['token'];
    $user_data_         = cl_user_data($cl['auth_status']['id']);
    $me                 = $cl['me'] = ((empty($user_data_)) ? false : $user_data_);

    if (empty($me)) {
        header('Content-Type: application/json');
        echo json(array('status' => 400, "error" => 'Invalid access token'), true);
        exit();
    }

    else {
        if (in_array($me['language'], array_keys($cl["languages"]))) {
            $cl["language"] = $me['language'];
            $langs          = cl_get_langs($cl["language"]);
        }
        
        $cl['is_logged']    = true;
        $me['display_lang'] = $cl["languages"][$me['language']];
        $me['draft_post']   = array();
        $me['new_notifs']   = cl_total_new_notifs();
        $me['new_messages'] = cl_total_new_messages();

        $me['new_notifs']   = ((is_posnum($me['new_notifs'])) ? $me['new_notifs'] : '');
        $me['new_messages'] = ((is_posnum($me['new_messages'])) ? $me['new_messages'] : '');
        $cl["is_admin"]     = (($me['admin'] == '1') ? true : false);
        
        if (is_posnum($me['last_post'])) {
            $me['draft_post'] = cl_get_orphan_post($me['last_post']);

            if (empty($me['draft_post'])) {
                cl_delete_orphan_posts($me['id']);
                cl_update_user_data($me['id'],array(
                    'last_post' => 0
                ));
            }
        }

        if ($me['last_active'] < (time() - (60 * 30))) {
            cl_update_user_data($me['id'], array(
                'last_active' => time(),
                'ip_address'  => cl_get_ip()
            ));
        }
    }
}

else {
    if ($cl['config']['affiliates_system'] == 'on') {
        if (not_empty($_GET['ref'])) {
            $ref_uname = cl_text_secure($_GET['ref']);
            $ref_udata = cl_get_user_by_name($ref_uname);

            if (not_empty($ref_udata)) {
                cl_session('ref_id', $ref_udata['id']);
            }
        }
    }
}

if (not_empty($_GET['language'])) {
    $lang_name  = cl_text_secure($_GET['language']);

    if (in_array($lang_name, array_keys($cl["languages"]))) {
        cl_session('lang', $lang_name);

        if (not_empty($cl["is_logged"])) {
            $set_lang = cl_update_user_data($me['id'], array('language' => $lang_name));
            $ref_url  = http_referer();

            if ($cl['ref_url']) {
                cl_location($cl['ref_url']);
            }
            else {
                cl_redirect_after('/', 0.05);
            }
        }
    }
}
