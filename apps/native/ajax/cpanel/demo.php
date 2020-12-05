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

if (empty($cl['is_admin'])) {
	$data['status'] = 400;
    $data['error']  = 'Invalid access token';
}

else if ($action == 'save_settings') {	
	$data['status']    = 400;
	$data['err_field'] = null;
	$raw_configs       = $db->get(T_CONFIGS);
	$raw_configs       = ((cl_queryset($raw_configs)) ? $raw_configs : array());

	if ($me['id'] != '1') {
		$data['status']    = 200;
		$data['err_field'] = null;
	}
	else{
		if ($raw_configs) {

			require_once(cl_full_path("core/apps/cpanel/settings/app_ctrl.php"));

			foreach ($raw_configs as $config_data) {
				if (isset($_POST[$config_data['name']])) {

					if ($config_data['name'] == 'google_analytics') {
						$conf_new_val = htmlspecialchars($_POST[$config_data['name']]);
					}

					else {
						$conf_new_val = cl_text_secure($_POST[$config_data['name']]);
					}

					if ($config_data['regex']) {
						if (preg_match($config_data['regex'], $conf_new_val)) {
							cl_admin_save_config($config_data['name'], $conf_new_val);
						}

						else {
							$field_label       = $config_data['title'];
							$data['message']   = cl_translate('Invalid value of field: {@field_name@}', array('field_name' => $field_label));
							$data['err_field'] = $config_data['name']; break;
						}
					}
					else {
						cl_admin_save_config($config_data['name'],$conf_new_val);
					}
				}
			}

			if (empty($data['err_field'])) {
				$data['status'] = 200;
			}
		}
	}
}

else if ($action == 'get_users') {

	require_once(cl_full_path("core/apps/cpanel/users/app_ctrl.php"));

	$filter_data      = fetch_or_get($_POST['filter'], array());
	$offset_to        = fetch_or_get($_POST['dir'], 'none');
	$offset_lt        = ((is_posnum($_POST['offset_lt'])) ? intval($_POST['offset_lt']) : 0);
	$offset_gt        = ((is_posnum($_POST['offset_gt'])) ? intval($_POST['offset_gt']) : 0);
	$users            = array();
	$data['status']   = 404;
	$data['err_code'] = 0;
	$html_arr         = array();

	if ($offset_to == 'up' && $offset_lt) {
		$users          = cl_admin_get_users(array(
			'limit'     => 7,
			'offset'    => $offset_lt,
			'offset_to' => 'gt',
			'order'     => 'ASC',
			'filter'    => $filter_data
		));

		$users = array_reverse($users);
	}

	else if($offset_to == 'down' && $offset_gt) {
		$users          = cl_admin_get_users(array(
			'limit'     => 7,
			'offset'    => $offset_gt,
			'offset_to' => 'lt',
			'order'     => 'DESC',
			'filter'    => $filter_data
		));
	}

	if (not_empty($users)) {
		foreach ($users as $cl['li']) {
			array_push($html_arr, cl_template('cpanel/assets/users/includes/list_item'));
		}

		$data['status'] = 200;
		$data['html']   = implode('', $html_arr);
	}
}

else if ($action == 'search_users') {

	require_once(cl_full_path("core/apps/cpanel/users/app_ctrl.php"));

	$filter_data      = fetch_or_get($_POST['filter'], array());
	$data['err_code'] = 0;
	$html_arr         = array();
	$users            = cl_admin_get_users(array(
		'limit'       => 7,
		'filter'      => $filter_data
	));

	if (not_empty($users)) {
		foreach ($users as $cl['li']) {
			array_push($html_arr, cl_template('cpanel/assets/users/includes/list_item'));
		}

		$data['status'] = 200;
		$data['html']   = implode('', $html_arr);
	}
	else{
		$data['status'] = 404;
		$data['html']   = cl_template('cpanel/assets/users/includes/filter404');
	}
}

else if ($action == 'get_posts') {

	require_once(cl_full_path("core/apps/cpanel/posts/app_ctrl.php"));

	$offset_to        = fetch_or_get($_GET['dir'], 'none');
	$offset_lt        = ((is_posnum($_GET['offset_lt'])) ? intval($_GET['offset_lt']) : 0);
	$offset_gt        = ((is_posnum($_GET['offset_gt'])) ? intval($_GET['offset_gt']) : 0);
	$posts            = array();
	$data['status']   = 404;
	$data['err_code'] = 0;
	$html_arr         = array();

	if ($offset_to == 'up' && $offset_lt) {
		$posts          = cl_admin_get_posts(array(
			'limit'     => 10,
			'offset'    => $offset_lt,
			'offset_to' => 'gt',
			'order'     => 'ASC'
		));

		$posts = array_reverse($posts);
	}

	else if($offset_to == 'down' && $offset_gt) {
		$posts          = cl_admin_get_posts(array(
			'limit'     => 10,
			'offset'    => $offset_gt,
			'offset_to' => 'lt',
			'order'     => 'DESC'
		));
	}

	if (not_empty($posts)) {
		foreach ($posts as $cl['li']) {
			array_push($html_arr, cl_template('cpanel/assets/publications/includes/list_item'));
		}

		$data['status'] = 200;
		$data['html']   = implode('', $html_arr);
	}
}

else if($action == 'delete_user') {
	$data['status']   = 404;
	$data['err_code'] = 0;
	$user_id          = fetch_or_get($_POST['id'], 0);

	if ($me['id'] != '1') {
		$data['status']    = 200;
		$data['err_field'] = null;
	}
	else{
		if (is_posnum($user_id)) {
			$data['status']      = 200;
			$data['status_code'] = (cl_delete_user_data($user_id) == true) ? 1 : 0;
		}
	}
}

else if($action == 'toggle_user_status') {
	$data['status']   = 404;
	$data['err_code'] = 0;
	$user_id          = fetch_or_get($_POST['id'], 0);

	if ($me['id'] != '1') {
		$data['status']    = 200;
		$data['err_field'] = null;
	}
	else {
		if (is_posnum($user_id)) {
			$udata = cl_raw_user_data($user_id);

			if (not_empty($udata)) {
				$data['status']  = 200;
				$data['message'] = cl_translate("Your changes has been successfully saved!");
				$status          = (($udata['active'] == '1') ? '2' : '1' );

				cl_update_user_data($user_id, array(
					'active' => $status
				));

				if ($status == '2') {
					cl_signout_user_by_id($user_id);
				}
			}
		}
	}
}

else if($action == 'delete_post') {
    $data['err_code'] = 0;
    $data['status']   = 400;
    $post_id          = fetch_or_get($_POST['id'], 0);

    if ($me['id'] != '1') {
		$data['status']    = 200;
		$data['err_field'] = null;
	}
	else {
	    if (is_posnum($post_id)) {
	        $post_data = cl_raw_post_data($post_id);

	        if (not_empty($post_data)) {

	            $post_owner = cl_raw_user_data($post_data['user_id']);

	            if (not_empty($post_owner)) {
	                if ($post_data['target'] == 'publication') {

	                    $posts_total = ($post_owner['posts'] -= 1);
	                    $posts_total = ((is_posnum($posts_total)) ? $posts_total : 0);

	                    cl_update_user_data($post_data['user_id'], array(
	                        'posts' => $posts_total
	                    ));

	                    $db = $db->where('publication_id', $post_id);
	                    $qr = $db->delete(T_POSTS);
	                }

	                else {
	                    cl_update_thread_replys($post_data['thread_id'], 'minus');
	                }
	                
	                cl_recursive_delete_post($post_id);
	                
	                $data['status'] = 200;
	            }
	        }
	    }
    }
}

else if($action =='create_backup') {

	if ($me['id'] != '1') {
		$data['status']    = 200;
		$data['err_field'] = null;
	}
	else {
		require_once(cl_full_path("core/apps/cpanel/backups/app_ctrl.php"));
		
		require_once(cl_full_path("core/apps/cpanel/settings/app_ctrl.php"));

		$data['err_code'] = 'failed_to_create_backup';
		$data['status']   = 500;
		$new_backup       = cl_admin_create_backup();

		if ($new_backup) {
			$time                = time();
			$data['status']      = 200;
			$data['err_code']    = 0;
			$data['last_backup'] = date('d F, Y - h:m', $time);

			cl_admin_save_config('last_backup', $time);
		}
	}
}

else if ($action == 'get_account_verifications') {

	require_once(cl_full_path("core/apps/cpanel/account_verification/app_ctrl.php"));

	$offset_to        = fetch_or_get($_GET['dir'], 'none');
	$offset_lt        = ((is_posnum($_GET['offset_lt'])) ? intval($_GET['offset_lt']) : 0);
	$offset_gt        = ((is_posnum($_GET['offset_gt'])) ? intval($_GET['offset_gt']) : 0);
	$data['status']   = 404;
	$data['err_code'] = 0;
	$html_arr         = array();

	if ($offset_to == 'up' && $offset_lt) {
		$requests       = cl_admin_get_verification_requests(array(
			'limit'     => 7,
			'offset'    => $offset_lt,
			'offset_to' => 'gt',
			'order'     => 'ASC'
		));

		$requests = array_reverse($requests);
	}

	else if($offset_to == 'down' && $offset_gt) {
		$requests       = cl_admin_get_verification_requests(array(
			'limit'     => 7,
			'offset'    => $offset_gt,
			'offset_to' => 'lt',
			'order'     => 'DESC'
		));
	}

	if (not_empty($requests)) {
		foreach ($requests as $cl['li']) {
			array_push($html_arr, cl_template('cpanel/assets/account_verification/includes/list_item'));
		}

		$data['status'] = 200;
		$data['html']   = implode('', $html_arr);
	}
}

else if ($action == 'get_verifreq_data') {

	require_once(cl_full_path("core/apps/cpanel/account_verification/app_ctrl.php"));

	$request_id       = fetch_or_get($_GET['id'], 'none');
	$data['status']   = 404;
	$data['err_code'] = 0;
	$cl['req_data']   = cl_admin_get_verification_request_data($request_id);

	if (not_empty($cl['req_data'])) {
		$data['status'] = 200;
		$data['html']   = cl_template('cpanel/assets/account_verification/modals/popup_ticket');
	}
}

else if ($action == 'delete_verifreq_data') {
	if ($me['id'] != '1') {
		$data['status']    = 200;
		$data['err_field'] = null;
	}
	else {
		$request_id       = fetch_or_get($_GET['id'], 'none');
		$data['status']   = 404;
		$data['err_code'] = 0;
		$db               = $db->where('id', $request_id);
		$req_data         = $db->getOne(T_VERIFICATIONS);

		if (cl_queryset($req_data)) {
			$data['status'] = 200;
			$db             = $db->where('id', $request_id);
			$qr             = $db->delete(T_VERIFICATIONS);

			cl_delete_media($req_data['video_message']);

			cl_update_user_data($req_data['user_id'], array(
				'verified' => '0'
			));
		}
		else {
			$data['status']  = 400;
			$data['message'] = cl_translate("An error occurred while processing your request. Please try again later.");
		}
	}
}

else if ($action == 'verify_user_account') {
	if ($me['id'] != '1') {
		$data['status']    = 200;
		$data['err_field'] = null;
	}
	else {
		$request_id       = fetch_or_get($_GET['id'], 'none');
		$data['status']   = 404;
		$data['err_code'] = 0;
		$db               = $db->where('id', $request_id);
		$req_data         = $db->getOne(T_VERIFICATIONS);

		if (cl_queryset($req_data)) {
			$data['status']  = 200;
			$data['message'] = cl_translate("Account has been verified successfully!");
			$db              = $db->where('id', $request_id);
			$qr              = $db->delete(T_VERIFICATIONS);

			cl_delete_media($req_data['video_message']);

			cl_update_user_data($req_data['user_id'], array(
				'verified' => '1'
			));

			cl_notify_user(array(
	            'subject'  => 'verified',
	            'user_id'  => $req_data['user_id'],
	            'entry_id' => 0
	        ));
		}
		else {
			$data['status']  = 400;
			$data['message'] = cl_translate("An error occurred while processing your request. Please try again later.");
		}
	}
}

else if($action == "update_sitemap") {
	$data['status']   = 404;
	$data['err_code'] = 0;
	$data['errors']   = array();

	if (is_writable('sitemap') != true) {
		$data['err_code'] = "permission_denied";
		$data['message']  = "The sitemaps storage folder does not exists or not writable!";
	}

	else if(is_writable('sitemap/sitemap-index.xml') != true) {
		$data['err_code'] = "permission_denied";
		$data['message']  = "The sitemap-index.xml does not exists or not writable!";
	}

	else if(is_writable('sitemap/maps') != true) {
		$data['err_code'] = "permission_denied";
		$data['message']  = "The sitemap/maps forder does not exists or not writable!";
	}

	else {

		require_once(cl_full_path("core/apps/cpanel/sitemap/app_ctrl.php"));

		$old_maps = glob('sitemap/maps/*.xml');
		$old_maps = ((is_array($old_maps) && not_empty($old_maps)) ? $old_maps : array());
		$maps     = 0;
		$posts    = cl_admin_get_publication_indexes();
		$users    = cl_admin_get_user_indexes();

		

		if (not_empty($old_maps)) {
			foreach($old_maps as $old_site_map){
			    try {
			    	@unlink($old_site_map);
			    } catch (Exception $e) { /* pass */ }
			}
		}

		if (not_empty($posts)) {
			$posts = array_chunk($posts, 1000);

			foreach ($posts as $cl['sitemap_entries']) {
				$map_url  = cl_strf("sitemap/maps/sitemap-%d.xml", $maps);
				$map_code = cl_sitemap('temps/sitemap');
				$map_code = trim($map_code);
				$map_code = str_replace("{%xml_version%}", '<?xml version="1.0" encoding="UTF-8"?>', $map_code);
				$exe_code = file_put_contents($map_url, $map_code);

				if ($exe_code) {
					$maps += 1;
				}

				else {
					$data['errors'][] = array(
						'file_index' => $maps,
						'file_path' => $map_url,
						'message' => "Failed to save sitemap file."
					);
				}
			}
		}

		if (not_empty($users)) {
			$users = array_chunk($users, 1000);

			foreach ($users as $cl['sitemap_entries']) {
				$map_url  = cl_strf("sitemap/maps/sitemap-%d.xml", $maps);
				$map_code = cl_sitemap('temps/sitemap');
				$map_code = trim($map_code);
				$map_code = str_replace("{%xml_version%}", '<?xml version="1.0" encoding="UTF-8"?>', $map_code);
				$exe_code = file_put_contents($map_url, $map_code);

				if ($exe_code) {
					$maps += 1;
				}

				else {
					$data['errors'][] = array(
						'file_index' => $maps,
						'file_path' => $map_url,
						'message' => "Failed to save sitemap file."
					);
				}
			}
		}

		if($maps > 0) {
			$cl['map_indexes'] = $maps;
			$sitemap_index     = cl_sitemap('temps/index');
			$sitemap_index     = trim($sitemap_index);
			$sitemap_index     = str_replace("{%xml_version%}", '<?xml version="1.0" encoding="UTF-8"?>', $sitemap_index);
			$exe_code          = file_put_contents('sitemap/sitemap-index.xml', $sitemap_index);

			if ($exe_code) {
				$data['status']       = 200;
				$data['last_sitemap'] = date('d F, Y - h:m');

				$db = $db->where('name', 'sitemap_update');
		        $qr = $db->update(T_CONFIGS, array(
		        	'value' => time()
		        ));
			}

			else {
				$data['errors'][] = array(
					'file_index' => $maps,
					'file_path' => $map_url,
					'message' => "Failed to save sitemap file."
				);
			}
		}
	}
}

else if ($action == 'get_account_reports') {

	require_once(cl_full_path("core/apps/cpanel/account_reports/app_ctrl.php"));

	$offset_to        = fetch_or_get($_GET['dir'], 'none');
	$offset_lt        = ((is_posnum($_GET['offset_lt'])) ? intval($_GET['offset_lt']) : 0);
	$offset_gt        = ((is_posnum($_GET['offset_gt'])) ? intval($_GET['offset_gt']) : 0);
	$data['status']   = 404;
	$data['err_code'] = 0;
	$html_arr         = array();

	if ($offset_to == 'up' && $offset_lt) {
		$reports        = cl_get_profile_reports(array(
			'limit'     => 7,
			'offset'    => $offset_lt,
			'offset_to' => 'gt',
			'order'     => 'ASC'
		));

		$reports = array_reverse($reports);
	}

	else if($offset_to == 'down' && $offset_gt) {
		$reports        = cl_get_profile_reports(array(
			'limit'     => 7,
			'offset'    => $offset_gt,
			'offset_to' => 'lt',
			'order'     => 'DESC'
		));
	}

	if (not_empty($reports)) {
		foreach ($reports as $cl['li']) {
			array_push($html_arr, cl_template('cpanel/assets/account_reports/includes/list_item'));
		}

		$data['status'] = 200;
		$data['html']   = implode('', $html_arr);
	}
}

else if ($action == 'get_account_report_data') {

	require_once(cl_full_path("core/apps/cpanel/account_reports/app_ctrl.php"));

	$report_id        = fetch_or_get($_GET['id'], 'none');
	$data['status']   = 404;
	$data['err_code'] = 0;
	$cl['rep_data']   = cl_admin_get_account_report_data($report_id);

	if (not_empty($cl['rep_data'])) {
		$data['status']  = 200;
		$data['is_seen'] = $cl['rep_data']['seen'];
		$data['html']    = cl_template('cpanel/assets/account_reports/modals/popup_ticket');
	}
}

else if($action == 'delete_account_report_data') {
	if ($me['id'] != '1') {
		$data['status']    = 200;
		$data['err_field'] = null;
	}
	else {
		$report_id        = fetch_or_get($_GET['id'], 'none');
		$data['status']   = 404;
		$data['err_code'] = 0;

		if (is_posnum($report_id)) {

			require_once(cl_full_path("core/apps/cpanel/account_reports/app_ctrl.php"));

			$db             = $db->where('id', $report_id);
			$qr             = $db->delete(T_PROF_REPORTS);
			$data['status'] = 200;
			$data['total']  = cl_get_total_profile_reports();;
		}
	}
}

else if ($action == 'get_affiliate_payouts') {

	require_once(cl_full_path("core/apps/cpanel/affiliate_payouts/app_ctrl.php"));

	$offset_to        = fetch_or_get($_GET['dir'], 'none');
	$offset_lt        = ((is_posnum($_GET['offset_lt'])) ? intval($_GET['offset_lt']) : 0);
	$offset_gt        = ((is_posnum($_GET['offset_gt'])) ? intval($_GET['offset_gt']) : 0);
	$data['status']   = 404;
	$data['err_code'] = 0;
	$html_arr         = array();

	if ($offset_to == 'up' && $offset_lt) {
		$requests       = cl_get_affiliate_payouts(array(
			'limit'     => 7,
			'offset'    => $offset_lt,
			'offset_to' => 'gt',
			'order'     => 'ASC'
		));

		$requests = array_reverse($requests);
	}

	else if($offset_to == 'down' && $offset_gt) {
		$requests       = cl_get_affiliate_payouts(array(
			'limit'     => 7,
			'offset'    => $offset_gt,
			'offset_to' => 'lt',
			'order'     => 'DESC'
		));
	}

	if (not_empty($requests)) {
		foreach ($requests as $cl['li']) {
			array_push($html_arr, cl_template('cpanel/assets/affiliate_payouts/includes/list_item'));
		}

		$data['status'] = 200;
		$data['html']   = implode('', $html_arr);
	}
}

else if ($action == 'delete_affiliate_payout') {
	if ($me['id'] != '1') {
		$data['status']    = 200;
		$data['err_field'] = null;
	}
	else {
		$request_id       = fetch_or_get($_POST['id'], 'none');
		$data['status']   = 400;
		$data['err_code'] = 0;

		if (is_posnum($request_id)) {
			$data['status'] = 200;
			$db             = $db->where('id', $request_id);
			$qr             = $db->delete(T_AFF_PAYOUTS);
		}
	}
}

else if ($action == 'update_affiliate_payout_status') {
	if ($me['id'] != '1') {
		$data['status']    = 200;
		$data['err_field'] = null;
	}
	else {
		$request_id       = fetch_or_get($_POST['id'], 'none');
		$data['status']   = 400;
		$data['err_code'] = 0;

		if (is_posnum($request_id)) {
			$data['status'] = 200;
			$db             = $db->where('id', $request_id);
			$qr             = $db->update(T_AFF_PAYOUTS, array('status' => 'paid'));
		}
	}
}
