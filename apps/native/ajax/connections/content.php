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

require_once(cl_full_path("core/apps/profile/app_ctrl.php"));

if ($action == 'load_more') {
	$data['err_code'] = 0;
    $data['status']   = 400;
    $offset           = fetch_or_get($_GET['offset'], 0);
    $prof_id          = fetch_or_get($_GET['prof_id'], 0);
    $type             = fetch_or_get($_GET['type'], false);
    $users_list       = array();
    $html_arr         = array();

    if (is_posnum($prof_id) && is_posnum($offset) && in_array($type, array('followers','following'))) {
        if (cl_can_view_profile($prof_id)) {
        	if ($type == 'followers') {
        		$users_list = cl_get_followers($prof_id, 30, $offset);	
        	}

        	else {
        		$users_list = cl_get_followings($prof_id, 30, $offset);
        	}


        	if (not_empty($users_list)) {
    			foreach ($users_list as $cl['li']) {
    				$html_arr[] = cl_template('connections/includes/list_item');
    			}

    			$data['status'] = 200;
    			$data['html']   = implode("", $html_arr);
    		}
        }
    }
}