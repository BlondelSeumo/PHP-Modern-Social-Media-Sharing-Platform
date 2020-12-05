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

$cl["page_title"] = cl_translate("Hot topics");
$cl["page_desc"]  = $cl["config"]["description"];
$cl["page_kw"]    = $cl["config"]["keywords"];
$cl["pn"]         = "trending";
$cl["sbr"]        = true;
$cl["sbl"]        = true;
$cl["htags"]      = cl_get_hot_topics(30);

$cl["app_statics"] = array(
	"styles" => array(
		cl_css_template("statics/css/apps/trending/style.master"),
		cl_css_template("statics/css/apps/trending/style.mq"),
		cl_css_template("statics/css/apps/trending/style.custom")
	)
);

$cl["http_res"] = cl_template("trending/content");
