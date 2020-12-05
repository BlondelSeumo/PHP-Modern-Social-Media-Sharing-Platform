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
	"scripts" => array(
		cl_static_file_path("statics/js/libs/bootstrap-select-v1.13.9.min.js"),
		cl_static_file_path("statics/js/libs/jquery-plugins/jquery.form-v4.2.2.min.js"),
	),
	"styles" => array(
		cl_static_file_path("statics/css/libs/bootstrap-select-v1.13.9.min.css")
	)
);

$cl['http_res'] = cl_template("cpanel/assets/email_settings/content");