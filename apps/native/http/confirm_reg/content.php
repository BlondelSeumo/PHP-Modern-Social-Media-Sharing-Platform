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

if (not_empty($cl['is_logged'])) {
	cl_redirect('404');
}

else if(empty(cl_session('validated_user_data'))) {
	cl_redirect('404');
}

$cl["app_statics"] = array(
	"styles" => array(
		cl_css_template("statics/css/apps/confirm_reg/style.master"),
		cl_css_template("statics/css/apps/confirm_reg/style.mq"),
		cl_css_template("statics/css/apps/confirm_reg/style.custom")
	)
);

$cl["page_title"] = cl_translate("Confirm registration");
$cl["page_desc"]  = $cl["config"]["description"];
$cl["page_kw"]    = $cl["config"]["keywords"];
$cl["pn"]         = "confirm_reg";
$cl["sbr"]        = true;
$cl["sbl"]        = true;
$cl["http_res"]   = cl_template("confirm_reg/content");
