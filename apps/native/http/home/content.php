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

if (empty($cl["is_logged"])) {
	cl_redirect("guest");
}
else {
	require_once(cl_full_path("core/apps/home/app_ctrl.php"));

	$cl["app_statics"] = array(
		"styles" => array(
			cl_css_template("statics/css/apps/home/style.master"),
			cl_css_template("statics/css/apps/home/style.custom")
		)
	);

	$cl["page_title"] = cl_translate("Homepage");
	$cl["page_desc"]  = $cl["config"]["description"];
	$cl["page_kw"]    = $cl["config"]["keywords"];
	$cl["pn"]         = "home";
	$cl["sbr"]        = true;
	$cl["sbl"]        = true;
	$cl["tl_feed"]    = cl_get_timeline_feed(15);
	$cl["http_res"]   = cl_template("home/content");
}