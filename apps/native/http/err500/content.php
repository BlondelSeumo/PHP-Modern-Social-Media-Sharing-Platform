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

$cl["app_statics"] = array(
	"styles" => array(
		cl_css_template("statics/css/apps/err500/style.master"),
		cl_css_template("statics/css/apps/err500/style.custom")
	)
);

$cl["page_title"] = cl_translate('Server error 500!');
$cl["page_desc"]  = $cl["config"]["description"];
$cl["page_kw"]    = $cl["config"]["keywords"];
$cl["pn"]         = "err500";
$cl["sbr"]        = true;
$cl["sbl"]        = true;
$cl["err_msg"]    = cl_session('err500_message');

if ($cl["err_msg"]) {
	cl_session_unset('err500_message');
}

$cl["http_res"] = cl_template("err500/content");
