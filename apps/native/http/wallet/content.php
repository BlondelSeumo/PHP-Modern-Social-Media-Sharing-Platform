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

	require_once(cl_full_path("core/apps/wallet/app_ctrl.php"));

	$cl["page_title"]  = cl_translate("Wallet");
	$cl["page_desc"]   = $cl["config"]["description"];
	$cl["page_kw"]     = $cl["config"]["keywords"];
	$cl["pn"]          = "wallet";
	$cl["sbr"]         = true;
	$cl["sbl"]         = true;
	$cl["app_statics"] = array(
		"styles" => array(
			cl_css_template("statics/css/apps/wallet/style.master"),
			cl_css_template("statics/css/apps/wallet/style.mq"),
			cl_css_template("statics/css/apps/wallet/style.custom")
		)
	);

	$cl["wallet_history"] = cl_get_account_wallet_history();
	$cl["http_res"]       = cl_template("wallet/content");
}