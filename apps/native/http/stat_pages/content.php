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

$page = fetch_or_get($_GET['page'], 'terms');

if (in_array($page, array('terms','privacy_policy','cookies_policy','about_us','faqs'))) {
	echo cl_template(cl_strf("%s/content",$page));
	exit();
}
else {
	cl_redirect("404");
}
