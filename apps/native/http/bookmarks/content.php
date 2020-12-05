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

if (empty($cl['is_logged'])) {
	cl_redirect("guest");
}

else {
	require_once(cl_full_path("core/apps/bookmarks/app_ctrl.php"));

	$cl["page_title"]  = cl_translate("Bookmarks");
	$cl["page_desc"]   = $cl["config"]["description"];
	$cl["page_kw"]     = $cl["config"]["keywords"];
	$cl["pn"]          = "bookmarks";
	$cl["sbr"]         = true;
	$cl["sbl"]         = true;
	$cl["bookmarks"]   = cl_get_bookmarks($me['id'], 30);
	$cl["app_statics"] = array(
		"styles" => array(
			cl_css_template("statics/css/apps/bookmarks/style.master"),
			cl_css_template("statics/css/apps/bookmarks/style.custom")
		)
	);

	$cl["http_res"] = cl_template("bookmarks/content");
}