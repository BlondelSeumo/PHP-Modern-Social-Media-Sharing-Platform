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

$profile_id = fetch_or_get($_GET["user_id"], false);

if (empty($profile_id) || is_posnum($profile_id) != true) {
	$data['code']    = 400;
    $data['message'] = "Invalid request data";
    $data['data']    = array();
}

else {
	require_once(cl_full_path("core/apps/profile/app_ctrl.php"));

	$user_data = cl_raw_user_data($profile_id); 

	if (not_empty($user_data)) {
		$data['code']    = 200;
		$data['message'] = "Profile fetched successfully";
		$data['data']    = array(
			'posts'      => array(),
			'media'      => array(),
			'likes'      => array(),
			'user'       => array(
				'id'          => $user_data['id'],
            	'first_name'  => $user_data['fname'],
            	'last_name'   => $user_data['lname'],
            	'user_name'   => $user_data['username'],
            	'email'       => $user_data['email'],
            	'is_verified' => (($user_data['verified'] == '1') ? true : false),
            	'website'     => $user_data['website'],
            	'about_you'   => $user_data['about'],
            	'gender'      => $user_data['gender'],
            	'country'     => $cl['countries'][$user_data['country_id']],
            	'post_count'  => $user_data['posts'],
            	'last_post'   => $user_data['last_post'],
            	'last_ad'     => $user_data['last_ad'],
            	'wallet'      => $user_data['wallet'],
            	'about'       => $user_data['about'],
            	'ip_address'  => $user_data['ip_address'],
            	'following_count'    => $user_data['following'],
            	'follower_count'     => $user_data['followers'],
            	'language'           => $user_data['language'],
            	'last_active'        => $user_data['last_active'],
            	'profile_privacy'    => $user_data['profile_privacy'],
            	'member_since'       => date("M Y", $user_data['joined']),
            	'is_blocked_visitor' => false,
            	'is_blocked_visitor' => false,
            	'is_following'       => false,
            	'can_view_profile'   => cl_can_view_profile($user_data['id'])
			)
		);

		if (not_empty($cl['is_logged'])) {
			$data['data']['user']['is_blocked_visitor'] = cl_is_blocked($user_data['id'], $me['id']);
			$data['data']['user']['is_blocked_profile'] = cl_is_blocked($me['id'], $user_data['id']);
			$data['data']['user']['is_following']       = cl_is_following($me['id'], $user_data['id']);
		}

		$profile_posts = cl_get_profile_posts($profile_id, 15);
		$profile_media = cl_get_profile_posts($profile_id, 15, true);
		$profile_likes = cl_get_profile_likes($profile_id, 15);

		if (not_empty($profile_posts)) {
			$data['data']["posts"] = $profile_posts;
		}
		
		if (not_empty($profile_media)) {
			$data['data']["media"] = $profile_media;
		}
		
		if (not_empty($profile_likes)) {
			$data['data']["likes"] = $profile_likes;
		}
	}

	else {
		$data['code']    = 404;
        $data['message'] = "Profile with this ID does not exist";
        $data['data']    = array();
	}
}