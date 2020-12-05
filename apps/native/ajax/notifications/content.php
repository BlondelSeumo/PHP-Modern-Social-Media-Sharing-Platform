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
    $data['status'] = 400;
    $data['error']  = 'Invalid access token';
}
else {
    if ($action == 'load_more') {
    	$data['err_code'] = 0;
        $data['status']   = 400;
        $offset           = fetch_or_get($_GET['offset'], 0);
        $type             = fetch_or_get($_GET['type'], false);
        $notifs_list      = array();
        $html_arr         = array();

        if (is_posnum($offset) && in_array($type, array('notifs', 'mentions'))) {

            require_once(cl_full_path("core/apps/notifications/app_ctrl.php"));

        	$notifs_list =  cl_get_notifications(array(
                "type"   => $type,
                "offset" => $offset,
                "limit"  => 50,
            ));

        	if (not_empty($notifs_list)) {
    			foreach ($notifs_list as $cl['li']) {
    				$html_arr[] = cl_template('notifications/includes/list_item');
    			}

    			$data['status'] = 200;
    			$data['html']   = implode("", $html_arr);
    		}
        }
    }

    else if($action == 'delete') {
        $data['err_code'] = 0;
        $data['status']   = 400;
        $scope            = fetch_or_get($_POST['scope'], array());
        $ids              = array();

        if (not_empty($scope) && is_array($scope)) {
            foreach ($scope as $id) {
                $ids[] = $id;
            }

            $db             = $db->where('recipient_id', $me['id']);
            $db             = $db->where('id', $ids, 'IN');
            $rq             = $db->delete(T_NOTIFS);
            $data['status'] = 200;
        }
    }
}