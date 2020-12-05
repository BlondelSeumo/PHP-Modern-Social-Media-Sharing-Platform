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

require_once("core/web_req_init.php");

$app_name = (isset($_GET["app"])) ? $_GET["app"] : "home";
$app_stat = fetch_or_get($applications[$app_name], false);
$spa_load = fetch_or_get($_GET['spa_load'], '0');
$spa_data = array();
$site_url = parse_url($site_url);

if (is_array($site_url)) {
	if ($site_url['host'] != fetch_or_get($_SERVER['HTTP_HOST'], 'none')) {
		cl_redirect("/");
	}
}

if ($spa_load != '1') {
	require_once("core/components/mw/http_request_mw.php");
}

if ($app_stat == true) {
	include_once(cl_strf("apps/native/http/%s/content.php",$app_name));

	if (empty($cl["http_res"])) {
		include_once("apps/native/http/err404/content.php");
	}
} 

else {
	include_once("apps/native/http/err404/content.php");
}

if ($spa_load == '1') {

	header('Content-Type: application/json');

	$spa_data['status']    = 200;
	$spa_data['html']      = $cl["http_res"];
	$spa_data['json_data'] = array(
		"page_title"       => $cl["page_title"],
		"page_desc"        => $cl["page_desc"],
		"page_kw"          => $cl["page_kw"],
		"pn"               => $cl["pn"]
	);

	echo json_encode($spa_data, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
	mysqli_close($mysqli);
	unset($cl);
	exit();
}

else {
	$cl['json_data']  = array(
		"page_title"  => $cl["page_title"],
		"page_desc"   => $cl["page_desc"],
		"page_kw"     => $cl["page_kw"],
		"pn"          => $cl["pn"],
		"app_statics" => $cl["app_statics"],
	);

	$http_res = cl_template("container");

	echo $http_res;
	mysqli_close($mysqli);
	unset($cl);
}
