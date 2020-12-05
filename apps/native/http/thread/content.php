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

require_once(cl_full_path("core/apps/thread/app_ctrl.php"));

$thread_id         = fetch_or_get($_GET["thread_id"], false);
$thread_id         = cl_text_secure($thread_id);
$cl['thread_data'] = cl_get_thread_data($thread_id);

if (empty($cl['thread_data']['post'])) {
	cl_redirect("404");
}

$cl["page_title"]  = $cl['thread_data']['post']['owner']['name'];
$cl["page_desc"]   = $cl['thread_data']['post']['og_text'];
$cl["page_kw"]     = $cl["config"]["keywords"];
$cl["page_image"]  = $cl['thread_data']['post']['og_image'];
$cl["page_url"]    = cl_link(cl_strf("thread/%d", $thread_id));
$cl["pn"]          = "thread";
$cl["sbr"]         = true;
$cl["sbl"]         = true;
$cl["app_statics"] = array(
	"styles" => array(
		cl_css_template("statics/css/apps/thread/style.master"),
		cl_css_template("statics/css/apps/thread/style.mq"),
		cl_css_template("statics/css/apps/thread/style.custom")
	)
);

$cl['thread_data']['parent'] = cl_get_thread_parent_posts($cl['thread_data']['post']);

if (not_empty($cl['thread_data']['parent'])) {
	$cl['thread_data']['parent'] = array_reverse($cl['thread_data']['parent']);
}

$cl["http_res"] = cl_template("thread/content");

