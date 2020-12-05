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
    $server_errors = mysqli_connect_error();

    if (not_empty($server_errors)) {
        header('Content-Type: application/json');
        echo json(array('status' => 400, "errors" => $server_errors), true);
        exit();
    }
}

$db_connection     = $mysqli;
$query             = $mysqli->query("SET NAMES utf8");
$set_charset       = $mysqli->set_charset('utf8mb4');
$set_charset       = $mysqli->query("SET collation_connection = utf8mb4_unicode_ci");
$db                = new MysqliDb($mysqli);
$url               = $site_url;
$config            = cl_get_configurations();
$config["url"]     = $url;
$cl["server_mode"] = 'production';
$langs             = cl_get_langs($config["language"]);
$cl["is_logged"]   = false;
$cl["is_admin"]    = false;
$cl["config"]      = $config;
$me                = array();
$cl['auth_status'] = cl_is_logged();

if (not_empty($cl['auth_status']['auth'])) {
    $user_data_ = cl_user_data($cl['auth_status']['id']);
    $me         = $cl['me'] = ((empty($user_data_)) ? false : $user_data_);

    if (empty($me)) {
        header('Content-Type: application/json');
        echo json(array('status' => 400, "error" => 'Invalid access token'), true);
        exit();
    }

    else {

        $cl['is_logged']  = true;
        $me['draft_post'] = array();
        $cl["is_admin"]   = (($me['admin'] == '1') ? true : false);
        
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