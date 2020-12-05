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

require_once(cl_full_path("core/apps/profile/app_ctrl.php"));

if (empty($_GET["uname"])) {
	cl_redirect("404");
}

$uname           = fetch_or_get($_GET["uname"], false);
$uname           = cl_text_secure($uname);
$cl['prof_user'] = cl_get_user_by_name($uname);
$cl['page_tab']  = fetch_or_get($_GET["tab"], "followers");

if (empty($cl['prof_user'])) {
	cl_redirect("404");
}

else if(cl_can_view_profile($cl['prof_user']['id']) != true) {
	cl_redirect("404");
}

if (not_empty($cl["is_logged"]) && ($cl['prof_user']['id'] != $me['id'])) {
	if (cl_is_blocked($cl['prof_user']['id'], $me['id'])) {
		cl_redirect("blocked");
	}

	else if(cl_is_blocked($me['id'], $cl['prof_user']['id'])) {
		cl_redirect("404");
	}
}

$cl["page_title"] = $cl['prof_user']['name'];
$cl["page_desc"]  = $cl['prof_user']['about'];
$cl["page_kw"]    = $cl["config"]["keywords"];
$cl["pn"]         = "connections";
$cl["sbr"]        = true;
$cl["sbl"]        = true;
$cl["users_list"] = array();

if ($cl['page_tab'] == 'followers') {
	$cl["users_list"] = cl_get_followers($cl['prof_user']['id'], 30, false);
}

else {
	$cl["users_list"] = cl_get_followings($cl['prof_user']['id'], 30, false);
}

if (empty($cl["users_list"])) {
	cl_redirect("404");
}

else {
	if (not_empty($cl["is_logged"])) {
		$cl['prof_user']['owner'] = ($cl['prof_user']['id'] == $me['id']);
	}

	$cl["app_statics"] = array(
		"styles" => array(
			cl_css_template("statics/css/apps/followers/style.master"),
			cl_css_template("statics/css/apps/followers/style.mq"),
			cl_css_template("statics/css/apps/followers/style.custom")
		)
	);

	$cl["http_res"] = cl_template("connections/content");
}
