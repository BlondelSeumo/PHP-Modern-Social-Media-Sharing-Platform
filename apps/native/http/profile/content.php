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

if (empty($_GET["uname"])) {
	cl_redirect("404");
}

$uname           = fetch_or_get($_GET["uname"], false);
$uname           = cl_text_secure($uname);
$cl['prof_user'] = cl_get_user_by_name($uname);
$cl['page_tab']  = fetch_or_get($_GET["tab"], 'posts');


if (empty($cl['prof_user'])) {
	cl_redirect("404");
}

require_once(cl_full_path("core/apps/profile/app_ctrl.php"));

$cl["page_title"]  = $cl['prof_user']['name'];
$cl["page_desc"]   = $cl['prof_user']['about'];
$cl["page_kw"]     = $cl["config"]["keywords"];
$cl["pn"]          = "profile";
$cl["sbr"]         = true;
$cl["sbl"]         = true;
$cl["user_posts"]  = array();
$cl["user_likes"]  = array();
$cl["can_view"]    = cl_can_view_profile($cl['prof_user']['id']);
$cl["app_statics"] = array(
	"styles" => array(
		cl_css_template("statics/css/apps/profile/style.master"),
		cl_css_template("statics/css/apps/profile/style.mq"),
		cl_css_template("statics/css/apps/profile/style.custom")
	)
);

if (not_empty($cl["is_logged"])) {

	if (($cl['prof_user']['id'] != $me['id']) && cl_is_blocked($cl['prof_user']['id'], $me['id'])) {
		cl_redirect("blocked");
	}

	$cl['prof_user']['owner']        = ($cl['prof_user']['id'] == $me['id']);
	$cl['prof_user']['is_following'] = cl_is_following($me['id'], $cl['prof_user']['id']);
	$cl['prof_user']['is_blocked']   = cl_is_blocked($me['id'], $cl['prof_user']['id']);

	if (not_empty($cl['prof_user']['owner'])) {
		$cl["app_statics"]["scripts"] = array(
			cl_js_template("statics/js/libs/jquery-ui")
		);
	}
}

if (not_empty($cl["can_view"]) && empty($cl['prof_user']['is_blocked'])) {
	if (in_array($cl['page_tab'], array('posts', 'media'))) {
		$media_type       = (($cl['page_tab'] == 'media') ? true : false);
		$cl["user_posts"] = cl_get_profile_posts($cl['prof_user']['id'], 30, $media_type);
	}

	else {
		$cl["user_likes"] = cl_get_profile_likes($cl['prof_user']['id'], 30);
	}
}

$cl["http_res"] = cl_template("profile/content");

