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

$cl["page_title"] = $cl["config"]["name"];
$cl["page_desc"]  = $cl["config"]["description"];
$cl["page_kw"]    = $cl["config"]["keywords"];
$cl["pn"]         = "suggested";
$cl["sbr"]        = true;
$cl["sbl"]        = true;
$cl["users_list"] = cl_get_follow_suggestions(30);

if (empty($cl["users_list"])) {
	cl_redirect("404");
}

else {
	$cl["app_statics"] = array(
		"styles" => array(
			cl_css_template("statics/css/apps/suggested/style.master"),
			cl_css_template("statics/css/apps/suggested/style.custom")
		)
	);

	$cl["http_res"] = cl_template("suggested/content");
}
